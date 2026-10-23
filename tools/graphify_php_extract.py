import argparse
import json
import re
import shutil
import subprocess
import sys
from collections import Counter
from pathlib import Path
from urllib.parse import urlsplit


CUSTOM_PREFIXES = ("custom_table_", "custom_session_", "custom_route_", "custom_file_", "custom_infra_")
TOOL_ONLY_IMPORTS = {"argparse", "collections", "json", "pathlib", "re", "shutil", "subprocess", "sys", "urllib_parse"}
EXCLUDED_DIRS = {".git", ".github", "graphify-out", "node_modules", "tools", "vendor"}
CUSTOM_RELATIONS = {"includes", "reads_table", "writes_table", "reads_session", "sets_session", "redirects_to", "calls_route", "resolves_to", "links_to", "calls_function", "uses_shared_infrastructure"}
OWN_HYPEREDGE_IDS = {"auth_flow", "list_lifecycle", "notification_flow", "shared_infra", "core_logic"}
INFRA_PATH = "scripts/verif/index.php"
INFRA_NODE = "custom_infra_shared"
HELPER_CALL_LIMIT = 8
EDGE_WEIGHTS = {"writes_table": 2.0, "calls_function": 1.5, "reads_table": 1.0, "includes": 0.5, "links_to": 0.5}
DOMAINS = [
    ("auth", "Authentification et comptes"),
    ("lists", "Listes et idées"),
    ("notify", "Notifications et courriels"),
    ("admin", "Administration et maintenance"),
    ("public", "Pages publiques et widgets"),
    ("infra", "Infrastructure partagée"),
    ("docs", "Documentation"),
]
DOMAIN_INDEX = {key: index for index, (key, _) in enumerate(DOMAINS)}
PATH_RULES = [
    ("pages/connexion", "auth"), ("pages/inscription", "auth"), ("pages/compte", "auth"),
    ("pages/reset-password", "auth"), ("scripts/deconnexion", "auth"),
    ("pages/listes", "lists"), ("pages/idees", "lists"), ("pages/modif-listes", "lists"),
    ("pages/modif-idees", "lists"), ("pages/print-liste", "lists"),
    ("js/notifications.js", "notify"), ("scripts/mail", "notify"), ("scripts/mark-notif-read", "notify"),
    ("widgets/notif-toast", "notify"), ("pages/admin", "admin"), ("scripts/clean", "admin"),
    ("pages/accessibilite", "public"), ("pages/cgu", "public"), ("pages/mentions-legales", "public"),
    ("pages/plan-site", "public"), ("pages/erreur", "public"), ("widgets/import", "public"),
    ("widgets/navbar", "public"), ("widgets/footer", "public"), ("images", "public"),
    ("index.php", "public"), ("scripts/verif", "infra"), ("AGENTS.md", "docs"), ("README.md", "docs"),
]
ID_RULES = {
    "custom_table_lic_compte": "auth", "custom_table_lic_connexion": "auth",
    "custom_session_id_compte": "auth", "custom_session_fonction": "auth",
    "custom_table_lic_liste": "lists", "custom_table_lic_idee": "lists",
    "custom_table_lic_autorisation": "lists", "custom_table_lic_notif": "notify",
    INFRA_NODE: "infra",
}
SQL_TABLE = re.compile(
    r"\b(?P<verb>DELETE\s+FROM|FROM|JOIN|INTO|UPDATE)\s+`?(?P<table>lic_[a-zA-Z0-9_]+)`?",
    re.IGNORECASE,
)
SESSION_KEY = re.compile(r"\$_SESSION\s*\[\s*(['\"])([a-zA-Z0-9_-]+)\1\s*\]")
INCLUDE = re.compile(
    r"\b(?:include|include_once|require|require_once)\s*(?:\(\s*)?(['\"])([^'\"]+)\1",
    re.IGNORECASE,
)
REDIRECT = re.compile(r"\bheader\s*\(\s*(['\"])Location:\s*([^'\"]+)\1", re.IGNORECASE)
FETCH = re.compile(r"\bfetch\s*\(\s*(['\"])(/[^'\"]+)\1")
INTERNAL_LINK = re.compile(
    r"\b(?:href|action)\s*=\s*(['\"])(?P<url>(?:https://family\.matthieudevilliers\.fr)?/(?:pages|scripts)/[^'\"?#]*)\1",
    re.IGNORECASE,
)


def slug(value):
    return re.sub(r"[^a-zA-Z0-9]+", "_", value).strip("_").lower()


def strip_comments(source):
    output = list(source)
    index = 0
    state = "code"
    while index < len(source):
        char = source[index]
        next_char = source[index + 1] if index + 1 < len(source) else ""
        if state == "code":
            if char in "'\"`":
                state = char
            elif (char == "/" and next_char == "/") or (char == "#" and next_char != "["):
                state = "line"
                output[index] = " "
                if char == "/":
                    output[index + 1] = " "
                    index += 1
            elif char == "/" and next_char == "*":
                state = "block"
                output[index] = output[index + 1] = " "
                index += 1
        elif state in ("'", '"', "`"):
            if char == "\\":
                index += 1
            elif char == state:
                state = "code"
        elif state == "line":
            if char == "\n":
                state = "code"
            else:
                output[index] = " "
        elif state == "block":
            if char == "*" and next_char == "/":
                output[index] = output[index + 1] = " "
                index += 1
                state = "code"
            elif char != "\n":
                output[index] = " "
        index += 1
    return "".join(output)


def line_number(source, offset):
    return source.count("\n", 0, offset) + 1


def relative_path(path, root):
    return path.relative_to(root).as_posix()


def file_id(rel):
    return slug(str(Path(rel).with_suffix(""))) or "custom_file_" + slug(rel)


def display_label(rel):
    parts = Path(rel).with_suffix("").parts
    if rel == "index.php":
        return "Page accueil"
    if parts[0] == "js":
        return "JS " + parts[-1]
    kind = {"pages": "Page", "scripts": "Script", "widgets": "Widget"}.get(parts[0])
    if not kind:
        return rel
    name = parts[1] if parts[-1] == "index" else " ".join(parts[1:])
    return f"{kind} {name}"


def normalized_route(value):
    parsed = urlsplit(value.strip())
    path = parsed.path or "/"
    return "/" + path.lstrip("/")


def route_target(route, root):
    parts = [part for part in route.strip("/").split("/") if part]
    if not parts or parts[0] not in {"pages", "scripts"}:
        return None
    candidate = root.joinpath(*parts)
    if candidate.suffix.lower() == ".php":
        target = candidate
    else:
        target = candidate / "index.php"
    return target if target.is_file() else None


HYPEREDGE_SPECS = [
    ("auth_flow", "Flux d'authentification", [
        "pages/connexion/index.php", "pages/inscription/index.php", "pages/reset-password/index.php",
        "scripts/deconnexion/index.php", "custom_table_lic_compte", "custom_session_id_compte",
    ]),
    ("list_lifecycle", "Cycle de vie d'une liste", [
        "pages/listes/index.php", "pages/modif-listes/index.php", "pages/idees/index.php",
        "pages/modif-idees/index.php", "pages/print-liste/index.php", "custom_table_lic_liste",
        "custom_table_lic_idee", "custom_table_lic_autorisation",
    ]),
    ("notification_flow", "Notifications", [
        "js/notifications.js", "widgets/notif-toast/index.php", "scripts/mark-notif-read/index.php",
        "scripts/mail/index.php", "custom_table_lic_notif",
    ]),
]


def extract(root, functions=None):
    nodes = {}
    edges = {}
    unresolved_includes = 0
    inferred_includes = 0
    file_ids = {}
    functions = functions or {}
    call_sites = {}
    infra_members = set()

    def add_node(node_id, label, file_type, source_file=None, source_location=None):
        node = {
            "id": node_id,
            "label": label,
            "file_type": file_type,
            "_origin": "ast",
        }
        if source_file:
            node["source_file"] = source_file
        if source_location:
            node["source_location"] = source_location
        nodes.setdefault(node_id, node)

    def file_node(path):
        rel = relative_path(path, root)
        if rel in file_ids:
            return file_ids[rel]
        node_id = file_id(rel)
        file_ids[rel] = node_id
        add_node(node_id, display_label(rel), "code", rel, "L1")
        return node_id

    def add_edge(source, target, relation, rel, line, context, confidence="EXTRACTED", score=1.0):
        key = (source, target, relation)
        if key in edges:
            edges[key]["evidence_count"] += 1
            return
        edges[key] = {
            "source": source,
            "target": target,
            "relation": relation,
            "_origin": "ast",
            "confidence": confidence,
            "confidence_score": score,
            "context": context,
            "source_file": rel,
            "source_location": f"L{line}",
            "evidence_count": 1,
            "weight": EDGE_WEIGHTS.get(relation, 1.0),
            "weight": EDGE_WEIGHTS.get(relation, 1.0),
        }

    def add_route(source_id, route, rel, line, relation):
        route = normalized_route(route)
        if route == "/":
            return
        route_id = "custom_route_" + slug(route)
        add_node(route_id, route, "route", rel, f"L{line}")
        add_edge(source_id, route_id, relation, rel, line, "route")
        target = route_target(route, root)
        if target:
            target_rel = relative_path(target, root)
            target_id = file_node(target)
            add_edge(route_id, target_id, "resolves_to", rel, line, "route_target")

    files = sorted(
        path for path in root.rglob("*")
        if path.is_file()
        and path.suffix.lower() in {".php", ".js"}
        and not EXCLUDED_DIRS.intersection(path.relative_to(root).parts)
        and path.name not in {"jquery.min.js", "bootstrap.bundle.min.js", "bootstrap.min.js"}
    )

    for path in files:
        rel = relative_path(path, root)
        source = path.read_text(encoding="utf-8", errors="replace")
        code = strip_comments(source)
        source_id = None

        if path.suffix.lower() == ".php":
            for match in INCLUDE.finditer(code):
                literal = match.group(2).strip()
                if "$" in literal or "{" in literal:
                    continue
                candidates = ((path.parent / literal).resolve(), (root / literal).resolve())
                target = next(
                    (item for item in candidates if item.is_file() and item.is_relative_to(root)),
                    None,
                )
                confidence = "EXTRACTED"
                score = 1.0
                if target is None:
                    stripped = re.sub(r"^(?:\.\.?/)+", "", literal)
                    inferred_target = (root / stripped).resolve()
                    if stripped != literal and inferred_target.is_file() and inferred_target.is_relative_to(root):
                        target = inferred_target
                        confidence = "INFERRED"
                        score = 0.8
                        inferred_includes += 1
                    else:
                        unresolved_includes += 1
                        continue
                source_id = source_id or file_node(path)
                target_id = file_node(target)
                line = line_number(code, match.start())
                if relative_path(target, root) == INFRA_PATH:
                    infra_members.add((source_id, rel, line))
                else:
                    add_edge(source_id, target_id, "includes", rel, line, "include", confidence, score)

            for match in SQL_TABLE.finditer(code):
                verb = re.sub(r"\s+", " ", match.group("verb").upper())
                relation = "writes_table" if verb in {"INTO", "UPDATE", "DELETE FROM"} else "reads_table"
                table = match.group("table").lower()
                table_id = "custom_table_" + table
                source_id = source_id or file_node(path)
                line = line_number(code, match.start())
                add_node(table_id, table, "database_table", rel, f"L{line}")
                add_edge(source_id, table_id, relation, rel, line, f"sql_{verb.lower().replace(' ', '_')}")

            for match in SESSION_KEY.finditer(code):
                key = match.group(2)
                tail = code[match.end():].lstrip()
                prefix = code[:match.start()].rstrip()
                writes = bool(re.match(r"(?:\+\+|--|(?:\.|\+|-|\*|/|%|&|\||\^)?=(?!=|>))", tail))
                writes = writes or prefix.endswith(("++", "--"))
                relation = "sets_session" if writes else "reads_session"
                key_id = "custom_session_" + slug(key)
                source_id = source_id or file_node(path)
                line = line_number(code, match.start())
                add_node(key_id, key, "session_key", rel, f"L{line}")
                add_edge(source_id, key_id, relation, rel, line, "session")

            for match in REDIRECT.finditer(code):
                if not urlsplit(match.group(2).strip()).path:
                    continue
                source_id = source_id or file_node(path)
                add_route(source_id, match.group(2), rel, line_number(code, match.start()), "redirects_to")

            for match in INTERNAL_LINK.finditer(source):
                target = route_target(normalized_route(match.group("url")), root)
                if target and target != path:
                    source_id = source_id or file_node(path)
                    add_edge(source_id, file_node(target), "links_to", rel, line_number(source, match.start()), "href_or_action")

            for name, function_id in functions.items():
                if re.search(rf"\bfunction\s+&?{re.escape(name)}\s*\(", source):
                    continue
                pattern = re.compile(rf"(?<![\w$>:]){re.escape(name)}\s*\(")
                for match in pattern.finditer(code):
                    call_sites.setdefault(name, {}).setdefault(rel, line_number(code, match.start()))

        else:
            for match in FETCH.finditer(code):
                source_id = source_id or file_node(path)
                add_route(source_id, match.group(2), rel, line_number(code, match.start()), "calls_route")

    for name, files_by_name in call_sites.items():
        if len(files_by_name) > HELPER_CALL_LIMIT:
            continue
        for rel, line in files_by_name.items():
            add_edge(file_node(root / rel), functions[name], "calls_function", rel, line, "call")

    hyperedges = []
    if infra_members:
        infra_file = file_id(INFRA_PATH)
        add_node(INFRA_NODE, "Infrastructure partagée", "concept", INFRA_PATH, "L1")
        add_edge(INFRA_NODE, infra_file, "implemented_by", INFRA_PATH, 1, "shared_infrastructure")
        for source_id, rel, line in infra_members:
            add_edge(source_id, INFRA_NODE, "uses_shared_infrastructure", rel, line, "shared_infrastructure")
        hyperedges.append({
            "id": "shared_infra", "label": "Infrastructure partagée (session, PDO, helpers)",
            "nodes": sorted({INFRA_NODE, infra_file} | {source for source, _, _ in infra_members}),
            "relation": "participate_in", "confidence": "EXTRACTED", "confidence_score": 1.0,
            "source_file": INFRA_PATH,
        })
    for hyperedge_id, label, members in HYPEREDGE_SPECS:
        hyperedges.append({
            "id": hyperedge_id, "label": label,
            "nodes": [file_id(member) if "." in member else member for member in members],
            "relation": "participate_in", "confidence": "EXTRACTED", "confidence_score": 1.0,
            "source_file": next((member for member in members if "." in member), ""),
        })

    return {
        "nodes": list(nodes.values()),
        "edges": list(edges.values()),
        "hyperedges": hyperedges,
        "unresolved_includes": unresolved_includes,
        "inferred_includes": inferred_includes,
    }


def existing_graph_extraction(raw_graph):
    removed, canonical, callable_ids = set(), {}, {}
    for node in raw_graph.get("nodes", []):
        node_id, label = node.get("id", ""), node.get("label", "")
        source = node.get("source_file", "").replace("\\", "/")
        if node_id.startswith(CUSTOM_PREFIXES) or source.startswith(("tools/", ".github/")):
            removed.add(node_id)
        elif re.fullmatch(r"\{closure#\d+\}\(\)", label) or (label == "String" and not source):
            removed.add(node_id)
        elif node_id in TOOL_ONLY_IMPORTS and node.get("file_type") == "concept" and not source:
            removed.add(node_id)
        elif node.get("_callable") and label.endswith("()"):
            previous = callable_ids.setdefault(label, node_id)
            if previous != node_id:
                canonical[node_id] = previous
                removed.add(node_id)

    nodes = [node for node in raw_graph.get("nodes", []) if node.get("id") not in removed]
    retained_ids = {node["id"] for node in nodes}
    for node in nodes:
        source = node.get("source_file", "").replace("\\", "/")
        if source.endswith((".php", ".js")) and node.get("id") == file_id(source):
            node["label"] = display_label(source)
            node["norm_label"] = node["label"].lower()
    edges, seen = [], set()
    for edge in raw_graph.get("links", []):
        source = canonical.get(edge.get("source"), edge.get("source"))
        target = canonical.get(edge.get("target"), edge.get("target"))
        edge_source = edge.get("source_file", "").replace("\\", "/")
        if source not in retained_ids or target not in retained_ids or source == target:
            continue
        if edge_source.startswith(("tools/", ".github/")):
            continue
        if edge.get("relation") in CUSTOM_RELATIONS and edge.get("_origin") == "ast":
            continue
        key = (source, target, edge.get("relation"))
        if key not in seen:
            edges.append({**edge, "source": source, "target": target})
            seen.add(key)

    hyperedges = [item for item in raw_graph.get("hyperedges", []) if item.get("id") not in OWN_HYPEREDGE_IDS]
    return {"nodes": nodes, "edges": edges, "hyperedges": hyperedges}


def path_domain(path):
    path = path.replace("\\", "/").lstrip("/")
    for prefix, domain in PATH_RULES:
        if path == prefix or path.startswith(prefix + "/"):
            return domain
    return None


def fixed_partition(graph):
    assigned = {}
    for node_id, data in graph.nodes(data=True):
        domain = ID_RULES.get(node_id)
        if domain is None and data.get("label") == "cleanup_old_connexions()":
            domain = "admin"
        if domain is None and data.get("file_type") == "route":
            domain = path_domain(data.get("label", ""))
        if domain is None and data.get("source_file"):
            domain = path_domain(data["source_file"])
        if domain is None and data.get("file_type") == "document":
            domain = "docs"
        if domain:
            assigned[node_id] = domain

    for _ in range(4):
        changed = False
        for node_id in graph.nodes:
            if node_id in assigned:
                continue
            votes = Counter(assigned[neighbor] for neighbor in graph.neighbors(node_id) if neighbor in assigned)
            if votes:
                assigned[node_id] = min(votes, key=lambda key: (-votes[key], DOMAIN_INDEX[key]))
                changed = True
        if not changed:
            break
    for node_id, data in graph.nodes(data=True):
        assigned.setdefault(node_id, "docs" if data.get("file_type") == "document" else "public")

    communities, labels = {}, {}
    for key, label in DOMAINS:
        members = sorted(node for node, domain in assigned.items() if domain == key)
        if members:
            community_id = len(communities)
            communities[community_id] = members
            labels[community_id] = label
    return communities, labels


def callable_functions(graph):
    functions = {}
    for node in graph.get("nodes", []):
        label = node.get("label", "")
        if node.get("_callable") and label.endswith("()") and not label.startswith("{"):
            functions.setdefault(label[:-2], node["id"])
    return functions


def run_graphify(root, *arguments):
    command = shutil.which("graphify")
    if not command:
        print(f"Avertissement: graphify absent; export ignoré: {' '.join(arguments)}")
        return
    result = subprocess.run([command, *arguments], cwd=root)
    if result.returncode:
        print(f"Avertissement: export graphify échoué ({result.returncode}): {' '.join(arguments)}")


def rebuild(root, custom, output_path=None):
    from graphify.analyze import god_nodes, suggest_questions, surprising_connections
    from graphify.build import build_from_json
    from graphify.cluster import score_all
    from graphify.detect import detect
    from graphify.export import to_json
    from graphify.paths import out_path
    from graphify.report import generate
    from graphify.watch import _read_build_excludes, _read_build_gitignore

    graph_path = root / "graphify-out" / "graph.json"
    if not graph_path.is_file():
        raise SystemExit("graphify-out/graph.json absent; lancez d'abord graphify sur le projet.")
    raw_graph = json.loads(graph_path.read_text(encoding="utf-8"))
    base = existing_graph_extraction(raw_graph)
    extraction = {
        "nodes": base["nodes"] + custom["nodes"],
        "edges": base["edges"] + custom["edges"],
        "hyperedges": base["hyperedges"] + custom["hyperedges"],
        "input_tokens": 0,
        "output_tokens": 0,
    }
    graph = build_from_json(extraction, root=root, directed=raw_graph.get("directed", False))
    present_ids = set(graph.nodes)
    graph.graph["hyperedges"] = [
        {**edge, "nodes": [node for node in edge.get("nodes", []) if node in present_ids]}
        for edge in graph.graph.get("hyperedges", [])
    ]
    communities, labels = fixed_partition(graph)
    cohesion = score_all(graph, communities)
    gods = god_nodes(graph)
    surprises = surprising_connections(graph, communities)
    questions = suggest_questions(graph, communities, labels)
    detection = detect(
        root,
        extra_excludes=_read_build_excludes(out_path()),
        gitignore=_read_build_gitignore(out_path()),
    )

    report_path = root / "graphify-out" / "GRAPH_REPORT.md"
    old_report = report_path.read_text(encoding="utf-8") if report_path.exists() else ""
    token_match = re.search(r"Token cost:\s*([\d,]+) input\s*[·|/]\s*([\d,]+) output", old_report)
    tokens = {
        "input": int(token_match.group(1).replace(",", "")) if token_match else 0,
        "output": int(token_match.group(2).replace(",", "")) if token_match else 0,
    }
    to_json(graph, communities, str(graph_path), force=True, community_labels=labels)
    report_path.write_text(
        generate(graph, communities, cohesion, labels, gods, surprises, detection, tokens, str(root), suggested_questions=questions),
        encoding="utf-8",
    )
    labels_path = root / "graphify-out" / ".graphify_labels.json"
    labels_path.write_text(json.dumps({str(key): value for key, value in labels.items()}, ensure_ascii=False), encoding="utf-8")
    analysis_path = root / "graphify-out" / ".graphify_analysis.json"
    analysis_path.write_text(json.dumps({
        "communities": {str(key): value for key, value in communities.items()},
        "cohesion": {str(key): value for key, value in cohesion.items()},
        "gods": gods,
        "surprises": surprises,
        "questions": questions,
    }, indent=2, ensure_ascii=False), encoding="utf-8")

    run_graphify(root, "export", "html")
    run_graphify(root, "export", "callflow-html", str(root), "--output", str(root / "graphify-out" / "callflow.html"))
    print(f"Graphe reconstruit: {graph.number_of_nodes()} nœuds, {graph.number_of_edges()} liens, {len(communities)} communautés.")
    for community_id, label in labels.items():
        print(f"  {label}: {len(communities[community_id])} nœuds, cohésion {cohesion.get(community_id, 0):.2f}")


def main():
    parser = argparse.ArgumentParser(description="Extrait les relations PHP/JS utiles au graphe graphify.")
    parser.add_argument("--root", type=Path, default=Path(__file__).resolve().parents[1])
    parser.add_argument("--output", type=Path)
    parser.add_argument("--update", action="store_true", help="Lance graphify update avant d’ajouter les relations PHP/JS.")
    parser.add_argument("--extract-only", action="store_true", help="Écrit l’extraction sans reconstruire le graphe.")
    args = parser.parse_args()
    root = args.root.resolve()
    output = args.output or root / "graphify-out" / ".graphify_custom.json"
    output.parent.mkdir(parents=True, exist_ok=True)
    if args.update:
        graphify_command = shutil.which("graphify")
        if not graphify_command:
            raise SystemExit("Commande graphify introuvable; installez graphify avant --update.")
        subprocess.run([graphify_command, "update", str(root)], cwd=root, check=True)
    graph_path = root / "graphify-out" / "graph.json"
    if not graph_path.is_file():
        raise SystemExit("graphify-out/graph.json absent; lancez d'abord graphify sur le projet.")
    raw_graph = json.loads(graph_path.read_text(encoding="utf-8"))
    custom = extract(root, callable_functions(raw_graph))
    output.write_text(json.dumps(custom, indent=2, ensure_ascii=False), encoding="utf-8")
    print(
        f"Extraction: {len(custom['nodes'])} nœuds, {len(custom['edges'])} liens; "
        f"includes non résolus: {custom['unresolved_includes']}, inférés: {custom['inferred_includes']}."
    )
    if not args.extract_only:
        rebuild(root, custom, output)


if __name__ == "__main__":
    main()