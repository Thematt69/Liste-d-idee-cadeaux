"""Controller regression checks with a PDO test double; no database or mail access.

Run from any directory: python tools/tests/security_regressions.py
Requires PHP CLI. Temporary PHP fixtures contain no application DB configuration.
These checks do not validate SQL execution, table engines or real concurrency.
"""

import json
from pathlib import Path
import shutil
import subprocess
import tempfile


ROOT = Path(__file__).resolve().parents[2]
PHP = shutil.which("php")

HARNESS = r'''<?php
require __DIR__ . '/session.php';
require __DIR__ . '/helpers.php';
require __DIR__ . '/csrf.php';
$config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
$_SESSION = array('id_compte' => $config['user'] ?? 1);
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = $config['post'];
$_GET = $config['get'] ?? array();
class TestPDO extends PDO {
    public $data;
    public $before;
    public $events = array();
    public $affected = 0;
    public $lastId = '0';
    public $failure = null;
    private $snapshot = null;
    private $authReads = 0;
    public function __construct() {
        $this->data = array(
            'accounts' => array(),
            'lists' => array(
                10 => array('id' => 10, 'nom' => 'Before', 'partage' => 'public', 'lien_partage' => str_repeat('a', 32), 'deleted_to' => null),
                20 => array('id' => 20, 'nom' => 'Other', 'partage' => 'public', 'lien_partage' => str_repeat('b', 32), 'deleted_to' => null)),
            'rights' => array(
                array('id' => 1, 'id_compte' => 1, 'id_liste' => 10, 'type' => 'proprietaire'),
                array('id' => 2, 'id_compte' => 2, 'id_liste' => 10, 'type' => 'proprietaire'),
                array('id' => 3, 'id_compte' => 5, 'id_liste' => 10, 'type' => 'lecteur'),
                array('id' => 4, 'id_compte' => 3, 'id_liste' => 20, 'type' => 'proprietaire')),
            'ideas' => array(),
            'reservations' => array(
                101 => array('id_liste' => 10, 'buy_from' => null, 'is_buy' => 0, 'deleted_to' => null),
                102 => array('id_liste' => 20, 'buy_from' => null, 'is_buy' => 0, 'deleted_to' => null)));
        $hash = password_hash('fixture-password', PASSWORD_BCRYPT, array('cost' => 4));
        foreach (array(1 => 'membre', 2 => 'admin', 3 => 'admin', 4 => 'admin', 5 => 'membre', 6 => 'membre') as $id => $role) {
            $this->data['accounts'][$id] = array('id' => $id, 'fonction' => $role, 'deleted_to' => $id === 4 ? '2020-01-01' : null,
                'motdepasse' => $hash, 'mail' => 'user' . $id . '@example.invalid', 'prenom' => 'Before', 'nom' => 'Before');
        }
        global $config;
        if (isset($config['visibility'])) { $this->data['lists'][10]['partage'] = $config['visibility']; }
        if (!empty($config['account_deleted'])) { $this->data['accounts'][1]['deleted_to'] = '2020-01-01'; }
        if (!empty($config['moderator'])) { $this->data['rights'][2]['type'] = 'moderateur'; }
        $this->data['rights'][] = array('id' => 5, 'id_compte' => 6, 'id_liste' => 10, 'type' => 'lecteur');
        foreach (($config['reservation_state'] ?? array()) as $key => $value) { $this->data['reservations'][101][$key] = $value; }
        if (!empty($config['list_deleted'])) { $this->data['lists'][10]['deleted_to'] = '2020-01-01'; }
        $this->before = $this->data;
    }
    public function prepare(string $query, array $options = array()): PDOStatement|false { return new TestStatement($this, $query); }
    public function beginTransaction(): bool { $this->snapshot = $this->data; $this->events[] = 'begin'; return true; }
    public function inTransaction(): bool { return $this->snapshot !== null; }
    public function commit(): bool { $this->snapshot = null; $this->events[] = 'commit'; return true; }
    public function rollBack(): bool { $this->data = $this->snapshot; $this->snapshot = null; $this->events[] = 'rollback'; return true; }
    public function lastInsertId(?string $name = null): string|false {
        global $config;
        $this->events[] = 'last_id';
        return !empty($config['invalid_insert_id']) ? false : $this->lastId;
    }
    public function fault($stage) {
        global $config;
        $this->events[] = $stage;
        if (($config['fail'] ?? '') === $stage) { throw new RuntimeException('injected:' . $stage); }
    }
    public function active($id): bool { return isset($this->data['accounts'][$id]) && $this->data['accounts'][$id]['deleted_to'] === null; }
    public function right($user, $list) {
        foreach ($this->data['rights'] as $right) {
            if ($right['id_compte'] == $user && $right['id_liste'] == $list) { return $right; }
        }
        return null;
    }
    public function addRight($user, $list, $type) {
        if ($this->right($user, $list)) { throw new LogicException('duplicate_right'); }
        $this->data['rights'][] = array('id' => count($this->data['rights']) + 100, 'id_compte' => (int) $user, 'id_liste' => (int) $list, 'type' => $type);
    }
    public function executeQuery($q, $p) {
        global $config;
        $this->affected = 0;
        if (str_starts_with($q, 'SELECT lic_liste.id, lic_liste.partage')) {
            $this->authReads++;
            if (str_contains($q, 'FOR UPDATE') && !$this->inTransaction()) { throw new LogicException('unlocked_transaction'); }
            if (!empty($config['lost_right']) && $this->authReads > 1) { return array(); }
            foreach ($this->data['lists'] as $list) {
                $right = $this->right($p[1], $list['id']);
                if ($list['lien_partage'] === $p[0] && $list['deleted_to'] === null && $this->active($p[1]) && ($right['type'] ?? '') === 'proprietaire') { return array($list); }
            }
            return array();
        }
        if (str_starts_with($q, 'INSERT INTO lic_liste')) {
            $this->lastId = '77';
            $this->data['lists'][77] = array('id' => 77, 'nom' => $p[0], 'partage' => $p[1], 'lien_partage' => $p[2], 'deleted_to' => null);
            $this->fault('insert_list'); return array();
        }
        if (str_starts_with($q, 'SELECT lic_compte.id FROM lic_compte LEFT JOIN')) {
            if (!str_contains($q, 'lic_autorisation.id IS NULL') || !str_contains($q, 'lic_compte.deleted_to IS NULL')) { throw new LogicException('admin_filters_missing'); }
            $rows = array();
            foreach ($this->data['accounts'] as $account) {
                if ($account['fonction'] === 'admin' && $this->active($account['id']) && !$this->right($account['id'], $p[0])) { $rows[] = array('id' => $account['id']); }
            }
            return $rows;
        }
        if (str_starts_with($q, 'INSERT INTO lic_autorisation')) {
            if (str_contains($q, 'SELECT id')) {
                if ($this->active($p[1]) && $p[1] != $p[2]) { $this->addRight($p[1], $p[0], 'proprietaire'); $this->affected = 1; }
                $this->fault('insert_coowner');
            } else {
                $role = str_contains($q, '"proprietaire"') ? 'proprietaire' : 'moderateur';
                $this->addRight($p[0], $p[1], $role); $this->affected = 1;
                $this->fault($role === 'proprietaire' ? 'insert_owner' : 'insert_admin');
            }
            return array();
        }
        if (str_starts_with($q, 'DELETE FROM lic_autorisation')) {
            $this->data['rights'] = array_values(array_filter($this->data['rights'], static function ($right) use ($q, $p) {
                return $right['id_liste'] != $p[0] || (str_contains($q, 'type != "proprietaire"') && $right['type'] === 'proprietaire');
            }));
            $this->fault('delete_rights'); return array();
        }
        if (str_starts_with($q, 'UPDATE lic_liste SET')) {
            if (str_contains($q, 'deleted_to =')) { $this->data['lists'][$p[1]]['deleted_to'] = $p[0]; $this->fault('soft_delete'); }
            else { $this->data['lists'][$p[2]]['nom'] = $p[0]; $this->data['lists'][$p[2]]['partage'] = $p[1]; $this->fault('update_list'); }
            return array();
        }
        if (str_starts_with($q, 'SELECT motdepasse')) {
            return $this->active($p[0]) ? array(array('motdepasse' => $this->data['accounts'][$p[0]]['motdepasse'])) : array();
        }
        if (str_starts_with($q, 'UPDATE lic_idee INNER JOIN')) {
            foreach (array('lic_idee.deleted_to IS NULL', 'lic_liste.deleted_to IS NULL', 'lic_compte.deleted_to IS NULL', 'lic_autorisation.type = "lecteur"', 'COALESCE(lic_idee.is_buy, 0) = 0') as $guard) {
                if (!str_contains($q, $guard)) { throw new LogicException('reservation_guard'); }
            }
            $idea = $this->data['reservations'][$p[1]] ?? null;
            $list = $idea ? $this->data['lists'][$idea['id_liste']] : null;
            $right = $idea ? $this->right($p[3], $idea['id_liste']) : null;
            $available = $p[0] === null ? ($idea['buy_from'] ?? null) == ($p[4] ?? -1) : ($idea['buy_from'] ?? null) === null;
            if (!str_contains($q, $p[0] === null ? 'lic_idee.buy_from = ?' : 'lic_idee.buy_from IS NULL')) { throw new LogicException('reservation_condition'); }
            if ($idea && $list['lien_partage'] === $p[2] && $list['deleted_to'] === null && $idea['deleted_to'] === null && !$idea['is_buy'] && $available && $this->active($p[3]) && ($right['type'] ?? '') === 'lecteur') {
                $this->data['reservations'][$p[1]]['buy_from'] = $p[0]; $this->affected = 1;
            }
            return array();
        }
        if (str_starts_with($q, 'SELECT id FROM lic_compte WHERE mail')) {
            foreach ($this->data['accounts'] as $account) { if ($account['mail'] === $p[0] && $account['id'] != $p[1] && $this->active($account['id'])) { return array(array('id' => $account['id'])); } }
            return array();
        }
        if (str_starts_with($q, 'UPDATE lic_compte')) {
            if (!str_contains($q, 'WHERE id = ? AND deleted_to IS NULL')) { throw new LogicException('wrong_profile_selector'); }
            if (empty($config['zero_update'])) {
                $id = $p[count($p) - 1];
                $fields = str_contains($q, 'motdepasse =') ? array('prenom', 'nom', 'motdepasse', 'mail') : array('prenom', 'nom', 'mail');
                foreach ($fields as $i => $field) { $this->data['accounts'][$id][$field] = $p[$i]; }
                $this->affected = 1;
            }
            $this->fault('update_profile'); return array();
        }
        if (str_starts_with($q, 'SELECT lic_autorisation.type as droit')) {
            foreach ($this->data['lists'] as $list) {
                if ($list['lien_partage'] === $p[1] && $list['deleted_to'] === null) {
                    $right = $this->right($p[0], $list['id']);
                    return $right ? array(array('droit' => $right['type'])) : array();
                }
            }
            return array();
        }
        if (str_starts_with($q, 'SELECT id FROM lic_liste')) {
            foreach ($this->data['lists'] as $list) { if ($list['lien_partage'] === $p[0] && $list['deleted_to'] === null) { return array(array('id' => $list['id'])); } }
            return array();
        }
        if (str_starts_with($q, 'INSERT INTO lic_idee')) {
            $this->data['ideas'][] = $p; $this->fault('insert_idea'); return array();
        }
        throw new LogicException('unhandled_fixture_query');
    }
}
class TestStatement extends PDOStatement {
    private $db;
    private $query;
    private $rows = array();
    private $affected = 0;
    public function __construct($db, $query) { $this->db = $db; $this->query = preg_replace('/\s+/', ' ', trim($query)); }
    public function execute(?array $params = null): bool { $this->rows = $this->db->executeQuery($this->query, $params ?? array()); $this->affected = $this->db->affected; return true; }
    public function fetch(int $mode = PDO::FETCH_DEFAULT, int $cursorOrientation = PDO::FETCH_ORI_NEXT, int $cursorOffset = 0): mixed { return array_shift($this->rows) ?? false; }
    public function rowCount(): int { return $this->affected; }
    public function closeCursor(): bool { return true; }
}
$bdd = new TestPDO();
register_shutdown_function(static function () use ($bdd) {
    global $info, $alert;
    file_put_contents(__DIR__ . '/result.json', json_encode(array('before' => $bdd->before, 'after' => $bdd->data,
        'events' => $bdd->events, 'failure' => $bdd->failure, 'info' => $info ?? false, 'alert' => $alert ?? false)));
});
set_error_handler(static function ($severity, $message) { throw new ErrorException($message, 0, $severity); });
try { require __DIR__ . '/controller.php'; } catch (Throwable $e) { $bdd->failure = $e->getMessage(); }
'''


def run_checks():
    if not PHP:
        raise SystemExit("PHP CLI is required")
    checked = 0
    with tempfile.TemporaryDirectory(prefix="lic-security-") as temporary:
        directory = Path(temporary)
        (directory / "sessions").mkdir()
        (directory / "session.php").write_bytes((ROOT / "scripts/session/index.php").read_bytes())
        (directory / "csrf.php").write_bytes((ROOT / "scripts/csrf/index.php").read_bytes())
        # Only the pure helper section is copied, never the PDO configuration.
        verifier = (ROOT / "scripts/verif/index.php").read_text(encoding="utf-8")
        helpers = verifier[verifier.index("/// Helper: decode stored HTML"):]
        assert "new PDO" not in helpers and "mysql:" not in helpers
        (directory / "helpers.php").write_text("<?php\n" + helpers, encoding="utf-8")
        (directory / "run.php").write_text(HARNESS, encoding="utf-8")
        helper_checks = r'''<?php
        require __DIR__ . '/helpers.php'; require __DIR__ . '/csrf.php';
        $_SESSION = array(); $_POST = array(); $_SERVER = array();
        $token = csrf_token();
        if (strlen($token) !== 64 || csrf_token() !== $token || csrf_request_is_valid()) { throw new LogicException('csrf_generation'); }
        $_POST['_csrf_token'] = $token;
        if (!csrf_request_is_valid()) { throw new LogicException('csrf_valid'); }
        foreach (array('invalid', array($token), '') as $bad) {
            $_POST['_csrf_token'] = $bad;
            if (csrf_request_is_valid()) { throw new LogicException('csrf_invalid'); }
        }
        $_POST = array(); $_SERVER['HTTP_X_CSRF_TOKEN'] = $token;
        if (!csrf_request_is_valid()) { throw new LogicException('csrf_header'); }
        foreach (array('javascript:alert(1)', 'data:text/html,test', 'ftp://example.invalid', '//example.invalid', 'https://user@example.invalid') as $bad) {
            if (is_safe_http_url($bad)) { throw new LogicException('url_scheme'); }
        }
        if (!is_safe_http_url('https://example.invalid/path?x=1&y=2') || !is_safe_http_url('http://example.invalid/')) { throw new LogicException('url_valid'); }
        if (validated_http_url('https://example.invalid/?x=1&amp;y=2') !== 'https://example.invalid/?x=1&y=2') { throw new LogicException('url_legacy'); }
        $escaped = safe_output('"<script>alert(1)</script>');
        if (str_contains($escaped, '<') || str_contains($escaped, '"') || safe_output("\xFF") === '') { throw new LogicException('html_escape'); }
        if (safe_output('Id&eacute;e') !== "Id\xC3\xA9e") { throw new LogicException('html_legacy'); }
        '''
        (directory / "helper_checks.php").write_text(helper_checks, encoding="utf-8")
        result = subprocess.run([PHP, "-d", "auto_prepend_file=", "-d", "auto_append_file=", str(directory / "helper_checks.php")],
                                cwd=directory, capture_output=True, timeout=10,
                                creationflags=getattr(subprocess, "CREATE_NO_WINDOW", 0))
        if result.returncode:
            raise AssertionError("CSRF/HTML/URL helper checks failed")

        def case(page, post, **options):
            nonlocal checked
            source = (ROOT / "pages" / page / "index.php").read_text(encoding="utf-8").split("?>", 1)[0]
            if page == "idees":
                source = source[:source.index("$sql = 'SELECT id, nom")]
            source = "\n".join(line for line in source.splitlines() if not line.strip().startswith("include("))
            (directory / "controller.php").write_text(source, encoding="utf-8")
            config = {"post": post, "get": {"liste": "a" * 32}, **options}
            (directory / "config.json").write_text(json.dumps(config), encoding="utf-8")
            result = subprocess.run([PHP, "-d", "auto_prepend_file=", "-d", "auto_append_file=", "-d",
                                     "session.save_path=" + str(directory / "sessions"), str(directory / "run.php")],
                                    cwd=directory, capture_output=True, timeout=10,
                                    creationflags=getattr(subprocess, "CREATE_NO_WINDOW", 0))
            if result.returncode:
                raise AssertionError("Fixture process failed: " + page)
            checked += 1
            return json.loads((directory / "result.json").read_text(encoding="utf-8"))

        create = {"Nom": "Test", "Partage": "public", "Co-propri\u00e9taire": "2"}
        result = case("modif-listes", create)
        assert result["failure"] is None and result["events"][-1] == "commit"
        rights = [row for row in result["after"]["rights"] if row["id_liste"] == 77]
        assert {(row["id_compte"], row["type"]) for row in rights} == {(1, "proprietaire"), (2, "proprietaire"), (3, "moderateur")}
        assert len(rights) == 3 and len(result["after"]["lists"]["77"]["lien_partage"]) == 32
        assert result["after"]["lists"]["10"] == result["before"]["lists"]["10"]
        for stage in ("insert_list", "insert_owner", "insert_coowner", "insert_admin"):
            result = case("modif-listes", create, fail=stage)
            assert result["failure"] == "injected:" + stage and result["before"] == result["after"] and result["events"][-1] == "rollback"
        for options in ({"invalid_insert_id": True},):
            result = case("modif-listes", create, **options)
            assert result["failure"] and result["before"] == result["after"] and result["events"][-1] == "rollback"
        result = case("modif-listes", {**create, "Co-propri\u00e9taire": "4"})
        assert result["failure"] and result["before"] == result["after"]
        for visibility in ("prive", "limite", "public"):
            for target in ("prive", "limite", "public"):
                post = {"Nom": "After", "save": "10", "Partage": target}
                result = case("modif-listes", post, visibility=visibility)
                assert result["failure"] is None and result["events"][-1] == "commit"
                assert result["after"]["lists"]["10"]["partage"] == target
                owners = [r for r in result["after"]["rights"] if r["id_liste"] == 10 and r["type"] == "proprietaire"]
                assert len(owners) == 2 and result["after"]["lists"]["20"] == result["before"]["lists"]["20"]
        update = {"Nom": "After", "save": "10", "Partage": "public"}
        for stage in ("insert_admin", "update_list"):
            result = case("modif-listes", update, visibility="prive", fail=stage)
            assert result["failure"] == "injected:" + stage and result["before"] == result["after"]
        result = case("modif-listes", {**update, "Partage": "prive"}, fail="update_list")
        assert result["before"] == result["after"] and result["events"][-1] == "rollback"
        for post in (update, {"delete": "10"}):
            for extra in ({"lost_right": True}, {"user": 5}):
                result = case("modif-listes", post, **extra)
                assert result["failure"] is None and result["before"] == result["after"]
        for stage in ("delete_rights", "soft_delete"):
            result = case("modif-listes", {"delete": "10"}, fail=stage)
            assert result["failure"] == "injected:" + stage and result["before"] == result["after"]
        result = case("modif-listes", {"delete": "10"})
        assert result["failure"] is None and result["after"]["lists"]["10"]["deleted_to"]
        assert not any(r["id_liste"] == 10 for r in result["after"]["rights"])
        for post in ({"delete": "20"}, {**update, "save": "20"}):
            result = case("modif-listes", post)
            assert result["before"] == result["after"] and not result["events"]

        for new_password in ("", "new-fixture-password"):
            profile = {"Mail": "free@example.invalid", "MDP": "fixture-password", "NewMDP": new_password, "Pr\u00e9nom": "After", "Nom": "After"}
            result = case("compte", profile)
            assert result["failure"] is None and result["after"]["accounts"]["1"]["mail"] == profile["Mail"]
            assert result["before"]["accounts"]["2"] == result["after"]["accounts"]["2"]
            for extra in ({"zero_update": True}, {"account_deleted": True}):
                result = case("compte", profile, **extra)
                assert result["before"] == result["after"] and result["info"] != "Vos informations ont bien \u00e9t\u00e9 modifi\u00e9es."
            for change in ({"Mail": "user2@example.invalid"}, {"MDP": "wrong"}):
                result = case("compte", {**profile, **change})
                assert result["before"] == result["after"] and not result["events"]

        idea = {"Nom": "Idea", "Commentaire": "", "Lien": "", "Prix": ""}
        for user in (3, 5):
            result = case("modif-idees", idea, user=user)
            assert result["failure"] is None and not result["after"]["ideas"]
        for reserved in (False, True):
            result = case("modif-idees", {**idea, **({"AchatFrom": "1"} if reserved else {})}, user=5, moderator=True)
            assert result["failure"] is None and len(result["after"]["ideas"]) == 1
            assert result["after"]["ideas"][0][4] == (5 if reserved else None)
        result = case("modif-idees", idea)
        assert result["failure"] is None and len(result["after"]["ideas"]) == 1

        reserve = {"AchatFrom": "101", "reservation": "1"}
        result = case("idees", reserve, user=5)
        assert result["failure"] is None and result["after"]["reservations"]["101"]["buy_from"] == 5
        for extra in ({"user": 1}, {"user": 5, "moderator": True}, {"user": 5, "list_deleted": True},
                      {"user": 5, "reservation_state": {"buy_from": 6}}, {"user": 5, "reservation_state": {"is_buy": 1}},
                      {"user": 5, "reservation_state": {"deleted_to": "2020-01-01"}}):
            result = case("idees", reserve, **extra)
            assert result["failure"] is None and result["before"] == result["after"]
        result = case("idees", {"AchatFrom": "102", "reservation": "1"}, user=5)
        assert result["failure"] is None and result["before"] == result["after"]
        for reserver in (5, 6):
            result = case("idees", {"AchatFrom": "101"}, user=5, reservation_state={"buy_from": reserver})
            assert result["failure"] is None and result["after"]["reservations"]["101"]["buy_from"] == (None if reserver == 5 else 6)
        print(f"OK: {checked} controller scenarios + CSRF/HTML/URL helpers; PDO test double, no SQL engine or mail")


if __name__ == "__main__":
    run_checks()
