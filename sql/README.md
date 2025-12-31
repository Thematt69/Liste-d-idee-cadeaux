# User Preferences Feature - Installation Instructions

## Database Migration

Before using the user preferences feature, you need to create the `lic_preferences` table in the database.

### Option 1: Using MySQL command line

```bash
mysql -h 185.98.131.128 -u matth1371558 -p matth1371558 < sql/create_preferences_table.sql
```

### Option 2: Using phpMyAdmin or another database client

1. Connect to the database
2. Open and execute the SQL file: `sql/create_preferences_table.sql`

### Option 3: Manual execution

Copy and paste the following SQL query into your database client:

```sql
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
```

## Features Implemented

### 1. Gift Profile Page (`/pages/profil-cadeau/`)
- Users can fill out their preferences in 4 sections:
  - **Section 1: Les Essentiels** (allergies, favorite color, clothing sizes)
  - **Section 2: Univers & Style de vie** (interests, decoration style, object vs experience)
  - **Section 3: Petits plaisirs & Passions** (treats, current obsessions, media preferences)
  - **Section 4: Guide "Anti-Déception"** (things not to receive, things already owned)

### 2. Banner on Homepage
- A dismissible banner appears on `/pages/listes/` for users who haven't completed their preferences
- The banner can be dismissed with "Plus tard" button
- Once dismissed, it won't appear again in the current session

### 3. Navigation Link
- Added "Mon Profil Cadeau" link to the main navbar for easy access

### 4. Preferences Display on List Pages
- List owners' preferences are displayed at the top of `/pages/idees/` pages
- Shows a summary card with key preferences
- Includes a "Voir toutes les préférences" button that opens a modal with full details
- Organized in color-coded sections matching the input form

## Usage

1. **As a list owner:** 
   - Click "Mon Profil Cadeau" in the navigation
   - Fill out any or all sections (all fields are optional)
   - Click "Enregistrer mes préférences"

2. **As a gift giver:**
   - When viewing someone's list, their preferences (if filled) will appear at the top
   - Click "Voir toutes les préférences" to see complete details
   - Use this information to find the perfect gift

## Technical Notes

- All user inputs are sanitized using `htmlentities()` before storage
- Outputs use `safe_output()` helper function for proper encoding
- Follows existing repository patterns for sessions, database queries, and redirects
- Soft delete pattern supported with `deleted_to` field
- One preferences record per user (enforced by UNIQUE constraint)
