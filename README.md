# Liste d'idee cadeaux

## Sécurité et Mises à jour

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
- `X-XSS-Protection: 1; mode=block` - Protection XSS supplémentaire
- `Referrer-Policy: strict-origin-when-cross-origin` - Contrôle des informations de référence
- `Permissions-Policy` - Restriction des API sensibles

#### Protection contre les attaques

- **XSS (Cross-Site Scripting):** Utilisation systématique de `htmlspecialchars()` avec `ENT_QUOTES` pour l'échappement des sorties
- **IP Spoofing:** Priorisation de `REMOTE_ADDR` dans la fonction `getIp()`
- **Tokens de réinitialisation:** Utilisation de `random_bytes()` au lieu de `md5(rand())`
- **Validation des entrées:** Vérification des paramètres GET/POST requis
- **Null checks:** Vérification des résultats de `fetch()` avant utilisation

### Bonnes pratiques

- Les mots de passe sont hashés avec `password_hash()` (bcrypt par défaut)
- Les requêtes SQL utilisent des requêtes préparées (protection contre les injections SQL)
- Les suppressions sont "soft" (champ `deleted_to`)
- HTTPS est requis pour toutes les connexions

### Notes pour les développeurs

- **Credentials:** Les identifiants de base de données et les clés API restent en dur dans le code (architecture existante)
- **CSRF:** Protection CSRF basique via validation de session (amélioration recommandée avec tokens)
- **Tests locaux:** Voir `.github/copilot-instructions.md` pour les instructions de développement local
