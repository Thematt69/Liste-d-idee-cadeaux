# Liste d'idee cadeaux

## Sécurité et Mises à jour

Le [suivi de remédiation P0](docs/security/plan-remediation.md) distingue les correctifs présents dans le dépôt, les tests restant à exécuter et les opérations d'hébergement. Les mécanismes décrits ci-dessous ne constituent pas une validation de la version déployée ni une garantie de couverture de tous les parcours.

### Version des composants

- **Bootstrap:** v5.3.8
- **jQuery:** v3.7.1

### Améliorations de sécurité implémentées

#### Configuration des sessions

- Cookies sécurisés avec `httponly`, `secure`, et `SameSite=Strict`
- Mode strict pour les sessions
- Régénération des IDs de session lors du changement de mot de passe

#### En-têtes de sécurité HTTP (.htaccess)

- `X-Frame-Options: SAMEORIGIN` - Protection contre le clickjacking
- `X-Content-Type-Options: nosniff` - Prévention du MIME-sniffing
- `Strict-Transport-Security` - Demande au navigateur de privilégier HTTPS
- `Referrer-Policy: strict-origin-when-cross-origin` - Contrôle des informations de référence
- `Permissions-Policy` - Restriction des API sensibles

#### Protection contre les attaques

- **Sorties HTML :** helper `safe_output()` avec échappement des guillemets et prise en charge des entités historiques ; couverture à vérifier par contexte
- **IP Spoofing:** Priorisation de `REMOTE_ADDR` dans la fonction `getIp()`
- **Tokens de réinitialisation:** Utilisation de `random_bytes()` au lieu de `md5(rand())`
- **Validation des entrées :** contrôles GET/POST par parcours ; validation HTTP(S) des liens d'idées
- **Résultats SQL :** vérifier chaque résultat de `fetch()` avant utilisation ; la présence de contrôles dans certains parcours ne prouve pas leur couverture globale

### Bonnes pratiques

- Les mots de passe sont hashés avec `password_hash()` (bcrypt par défaut)
- Les requêtes SQL utilisent des requêtes préparées (protection contre les injections SQL)
- Les suppressions sont "soft" (champ `deleted_to`)
- HTTPS est requis pour toutes les connexions

### Notes pour les développeurs

- **Credentials:** Les identifiants de base de données et les clés API restent en dur dans le code (architecture existante)
- **CSRF :** jeton de session vérifié pour les POST via le bootstrap partagé, champs de formulaires et en-tête AJAX ; la déconnexion en GET reste à corriger
- **Tests locaux :** voir [AGENTS.md](AGENTS.md) et la [politique opérationnelle](docs/security/politique-agents.md) ; isoler SQL et mail avant d'exécuter l'application
