-- À exécuter uniquement après validation du schéma et sauvegarde vérifiée.
-- Le moteur transactionnel est requis pour garantir l'usage unique des jetons.
CREATE TABLE lic_password_reset (
    token_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    id_compte INT(11) NOT NULL,
    expires_at DATETIME NOT NULL,
    PRIMARY KEY (token_hash),
    INDEX idx_password_reset_compte (id_compte),
    INDEX idx_password_reset_expiration (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
