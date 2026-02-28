# User Preferences Feature - Visual Overview

## Feature Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                         USER JOURNEY                                 │
└─────────────────────────────────────────────────────────────────────┘

┌──────────────┐
│ User Logs In │
└──────┬───────┘
       │
       ▼
┌─────────────────────────┐
│ Homepage (/pages/listes)│
│ - Shows lists           │
│ - Banner appears?       │◄──── if no preferences
└──────┬──────────────────┘
       │
       ├─────► "Compléter mon profil" ─────┐
       │                                    │
       ├─────► "Plus tard" (dismiss)       │
       │                                    │
       ▼                                    ▼
   Continue                    ┌────────────────────────────┐
   browsing                    │ Gift Profile Page          │
       │                       │ (/pages/profil-cadeau)     │
       │                       │                            │
       │                       │ 4 SECTIONS:                │
       │                       │ 1. Les Essentiels          │
       │                       │ 2. Univers & Style         │
       │                       │ 3. Petits plaisirs         │
       │                       │ 4. Anti-Déception          │
       │                       │                            │
       │                       │ [Save Button]              │
       │                       └──────────┬─────────────────┘
       │                                  │
       │                                  │ Submit
       │                                  ▼
       │                       ┌──────────────────────────┐
       │                       │ Preferences Saved        │
       │                       │ - Banner won't show      │
       │                       │ - Visible on lists       │
       │                       └──────────┬───────────────┘
       │                                  │
       └──────────────┬───────────────────┘
                      │
                      ▼
       ┌──────────────────────────────┐
       │ Browse Lists                 │
       │ (/pages/idees/?liste=xyz)    │
       │                              │
       │ ┌──────────────────────────┐ │
       │ │ ✨ Préférences de John  │ │◄──── if owner has preferences
       │ │ Couleur: Blue • Loisirs  │ │
       │ │ [Voir toutes]            │ │
       │ └──────────┬───────────────┘ │
       │            │                  │
       │            │ Click            │
       │            ▼                  │
       │ ┌────────────────────────┐   │
       │ │ MODAL: Full Details    │   │
       │ │ - All 4 sections       │   │
       │ │ - Complete info        │   │
       │ │ [Close]                │   │
       │ └────────────────────────┘   │
       │                              │
       │ [Gift Ideas List Below]      │
       └──────────────────────────────┘
```

## Page Structure

### 1. Homepage (/pages/listes/)
```
┌─────────────────────────────────────────────┐
│ Navigation Bar                              │
│ [Logo] [Mes listes] [Mon Profil Cadeau] .. │
├─────────────────────────────────────────────┤
│                                             │
│ ┌─────────────────────────────────────────┐ │
│ │ ✨ BANNER (if no preferences)          │ │
│ │ Aidez vos proches à vous surprendre!   │ │
│ │ [Compléter] [Plus tard]                │ │
│ └─────────────────────────────────────────┘ │
│                                             │
│ Mes listes                                  │
│ [Créer une liste] [Créer liste duo]        │
│                                             │
│ ┌──────┐ ┌──────┐ ┌──────┐                 │
│ │List 1│ │List 2│ │List 3│  ...            │
│ └──────┘ └──────┘ └──────┘                 │
└─────────────────────────────────────────────┘
```

### 2. Gift Profile Page (/pages/profil-cadeau/)
```
┌─────────────────────────────────────────────┐
│ Mon Profil Cadeau ✨                       │
│ "Envie d'être surpris?"                     │
│ Description...                              │
├─────────────────────────────────────────────┤
│ ┌─────────────────────────────────────────┐ │
│ │ SECTION 1: Les Essentiels [BLUE]       │ │
│ │ □ Allergies?                            │ │
│ │ □ Couleur préférée?                     │ │
│ │ □ Taille vêtements?                     │ │
│ └─────────────────────────────────────────┘ │
│                                             │
│ ┌─────────────────────────────────────────┐ │
│ │ SECTION 2: Univers & Style [GREEN]     │ │
│ │ □ Centres d'intérêt?                    │ │
│ │ □ Style décoration?                     │ │
│ │ □ Objet ou expérience?                  │ │
│ └─────────────────────────────────────────┘ │
│                                             │
│ ┌─────────────────────────────────────────┐ │
│ │ SECTION 3: Petits plaisirs [CYAN]      │ │
│ │ □ Péché mignon?                         │ │
│ │ □ Obsession du moment?                  │ │
│ │ □ Genres lecture/musique/films?         │ │
│ └─────────────────────────────────────────┘ │
│                                             │
│ ┌─────────────────────────────────────────┐ │
│ │ SECTION 4: Anti-Déception [YELLOW]     │ │
│ │ □ Pas recevoir?                         │ │
│ │ □ Déjà en trop grande quantité?         │ │
│ └─────────────────────────────────────────┘ │
│                                             │
│        [Enregistrer mes préférences]        │
└─────────────────────────────────────────────┘
```

### 3. List View with Preferences (/pages/idees/)
```
┌─────────────────────────────────────────────┐
│ 🔒 Liste de John                            │
│                                             │
│ ┌─────────────────────────────────────────┐ │
│ │ ✨ Préférences de John                  │ │
│ │ Couleur: Bleu • Loisirs: Yoga •         │ │
│ │ Gourmandise: Chocolat noir              │ │
│ │           [Voir toutes les préférences] │ │
│ └─────────────────────────────────────────┘ │
│                                             │
│ ┌─────────────────────────────────────────┐ │
│ │ Gift Ideas Table                        │ │
│ │ ┌────┬──────────┬──────┬──────┬───────┐ │ │
│ │ │Nom │Commentair│Lien  │Prix  │Actions│ │ │
│ │ ├────┼──────────┼──────┼──────┼───────┤ │ │
│ │ │... │...       │...   │...   │...    │ │ │
│ │ └────┴──────────┴──────┴──────┴───────┘ │ │
│ └─────────────────────────────────────────┘ │
└─────────────────────────────────────────────┘
```

## Database Schema

```
lic_compte                    lic_preferences
┌──────────┐                 ┌────────────────────────────┐
│ id       │◄────────────────│ id_compte (FK, UNIQUE)     │
│ prenom   │                 │ allergies                  │
│ nom      │                 │ couleur_preferee           │
│ mail     │                 │ taille_vetements           │
│ ...      │                 │ centres_interet            │
└──────────┘                 │ style_decoration           │
                             │ objet_ou_experience        │
                             │ peche_mignon               │
                             │ obsession_moment           │
                             │ genres_lecture_musique_film│
                             │ pas_recevoir               │
                             │ deja_trop                  │
                             │ created_at                 │
                             │ updated_at                 │
                             │ deleted_to                 │
                             └────────────────────────────┘

lic_liste                     lic_autorisation
┌──────────┐                 ┌──────────┐
│ id       │◄────────────────│ id_liste │
│ nom      │                 │ id_compte│──┐
│ ...      │                 │ type     │  │
└──────────┘                 └──────────┘  │
                                           │
                                           └──► Used to find list owners
                                                and display their preferences
```

## Component Interaction

```
┌────────────────────────────────────────────────────────────┐
│                     Component Flow                         │
└────────────────────────────────────────────────────────────┘

pages/listes/index.php
  └──► includes widgets/preferences-banner/index.php
         ├──► Queries: SELECT from lic_preferences
         ├──► Checks: $_SESSION['preferences_banner_dismissed']
         └──► Action: Link to /pages/profil-cadeau/

pages/profil-cadeau/index.php
  ├──► Form: 11 input fields (textareas)
  ├──► POST: Saves to lic_preferences
  │    ├──► Check if exists (SELECT)
  │    ├──► UPDATE if exists
  │    └──► INSERT if new
  └──► Success: Shows confirmation message

pages/idees/index.php
  └──► includes widgets/preferences-display/index.php
         ├──► Queries: Find list owners
         ├──► Queries: Get their preferences (single IN query)
         ├──► Display: Summary card for each owner
         └──► Modal: Full details on click

widgets/navbar/index.php
  └──► Link: "Mon Profil Cadeau" → /pages/profil-cadeau/

widgets/preferences-banner/dismiss.php
  ├──► Validates: $_SESSION['id_compte']
  └──► Sets: $_SESSION['preferences_banner_dismissed'] = true
```

## Color Scheme

```
Section 1: Les Essentiels
  Header: Bootstrap Primary (Blue) #0d6efd
  
Section 2: Univers & Style de vie
  Header: Bootstrap Success (Green) #198754
  
Section 3: Petits plaisirs & Passions
  Header: Bootstrap Info (Cyan) #0dcaf0
  
Section 4: Anti-Déception
  Header: Bootstrap Warning (Yellow/Amber) #ffc107

Modal Headers:
  Gradient: Purple-blue gradient
  From: #667eea To: #764ba2
```

## Security Flow

```
┌────────────────────────────────────────────┐
│         Security Checkpoints               │
└────────────────────────────────────────────┘

1. Page Access
   ├─ Session check: isset($_SESSION['id_compte'])
   └─ Redirect to login if not authenticated

2. Input Processing
   ├─ Sanitize: htmlentities($_POST['field'])
   ├─ Parameterized: $bdd->prepare($sql)
   └─ Execute: with array of parameters

3. Output Display
   ├─ Decode: html_entity_decode (in safe_output)
   ├─ Escape: htmlspecialchars (in safe_output)
   └─ Safe: nl2br + safe_output for display

4. Banner Dismissal
   ├─ Validate: Session exists
   └─ HTTP 401 if unauthorized
```

## Key Technical Decisions

1. **Why textareas instead of structured fields?**
   - More flexible for users
   - Allows natural language descriptions
   - Easier to add/read

2. **Why session for banner dismissal?**
   - Simple implementation
   - No database writes needed
   - Reappears each session (gentle reminder)
   - Can be changed to permanent if needed

3. **Why soft delete?**
   - Follows existing codebase pattern
   - Allows recovery of data
   - Maintains referential integrity

4. **Why single query with IN clause?**
   - Performance optimization
   - Avoids N+1 query problem
   - Handles multiple list owners efficiently

5. **Why modal for full details?**
   - Keeps list page clean
   - Progressive disclosure
   - Better UX than inline expansion

## Performance Characteristics

```
Operation                      Queries    Impact
─────────────────────────────────────────────────
Load homepage (no prefs)       1          Low
Load homepage (with prefs)     0          None
Load gift profile page         1          Low
Save preferences               2-3        Low
Load list with 1 owner         2          Low
Load list with 2 owners        2          Low
Banner dismiss                 0          None
```

## Future Enhancement Ideas

- [ ] Preference completion badge (e.g., "80% complete")
- [ ] Share preferences via unique link
- [ ] Import preferences from other users as template
- [ ] Preference tags for quick filtering
- [ ] AI-powered gift suggestions based on preferences
- [ ] Preference update notifications to family
- [ ] Seasonal preference variations
- [ ] Gift history tracking against preferences

---

This visual overview provides a comprehensive understanding of the user preferences feature implementation.
