-- Create table for user preferences
CREATE TABLE IF NOT EXISTS `lic_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_compte` int(11) NOT NULL,
  `allergies` text,
  `couleur_preferee` text,
  `taille_vetements` text,
  `centres_interet` text,
  `style_decoration` text,
  `objet_ou_experience` text,
  `peche_mignon` text,
  `obsession_moment` text,
  `genres_lecture_musique_film` text,
  `pas_recevoir` text,
  `deja_trop` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_to` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_id_compte` (`id_compte`),
  FOREIGN KEY (`id_compte`) REFERENCES `lic_compte` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
