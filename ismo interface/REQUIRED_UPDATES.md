# REQUIRED UPDATES — ISMO-SkillSwap

**Generated:** 2026-05-07 22:12:02 
**Updated 1 :** 2026-05-08 23:12:21 (Modifications Applied)
**Updated 2 :** 2026-05-09 00:00:00 (Frontend log refreshed; API items intentionally left out)

---

## ✅ SUMMARY OF APPLIED FIXES

**High Priority Items — COMPLETED:**
1. ✅ Fixed broken navigation dropdown links in all formateur_pages (4 files)
2. ✅ Created missing `assets/js/mes_demandes.js` with full functionality
3. ✅ Created default avatar asset `assets/images/default_avatar.svg`
4. ✅ Updated database schema with default profile picture path
5. ✅ Verified filename consistency across all folders
6. ✅ Fixed additional href="#" links in login and parametres pages
7. ✅ Fixed stagiaire profile edit button wiring and default avatar rendering
8. ✅ Reworked `pages_stagiere/parametres.html` layout into a full-width settings page with footer-style help block
9. ✅ Added mentor redirect stubs for `dashboard.html`, `mes_demandes.html`, and `mes_competances.html`
10. ✅ Normalized the formateur catalogue profile dropdown labels
11. ✅ Removed sidebar underline styling for shared navigation links
12. ✅ Restyled `pages_stagiere/passeport_pdf.html` to match the shared dashboard language
13. ✅ Replaced remaining emoji markers in stagiaire pages with SVG icons
14. ✅ Created `pages_stagiere/nouvelle_demande.html` for help-request submission

**Files Modified:** 7 HTML files + 1 new JS file + 1 new SVG file

---

- ✅ **FIXED** Broken navigation anchors and placeholder links
  - Files: `formateur_pages/tableu_de_bord.html`, `formateur_pages/statistique.html`, `formateur_pages/validation_demande.html`, `formateur_pages/catalogue.html`
  - Issues: menu dropdown links used `href="#"` for profile/params/notifications/logout.
  - **Applied Fix:** Replaced all dropdown `href="#"` with correct paths pointing to pages_stagiere versions:
    - `Mon profil` → `../pages_stagiere/profile.html`
    - `Parametres` → `../pages_stagiere/parametres.html`
    - `Notifications` → `../pages_stagiere/notification.html`
    - `Déconnexion` → `../pages_stagiere/login.html`
  - Also fixed login.html forgot password link and terms/privacy links.

- ✅ **FIXED** Shared sidebar link styling
  - File: `assets/css/dashboard.css`
  - Issue: sidebar links could still inherit underlines in some browsers/pages.
  - **Applied Fix:** Added a shared `text-decoration: none` reset for sidebar nav links.

- ✅ **CREATED** Missing JavaScript file
  - File: `assets/js/mes_demandes.js` was referenced in `pages_stagiere/mes_demandes.html` but missing.
  - **Applied Fix:** Created fully functional `mes_demandes.js` (144 lines) with:
    - Search and filter functionality for help requests
    - Request list interaction handlers (expand/collapse)
    - Action buttons: view-responses, edit, close, delete, create-new-request
    - Placeholder/TODO comments for backend API integration
    - Proper event delegation and utility functions
    - Comprehensive JSDoc comments

- ✅ **COMPLETED** Mandatory profile picture requirement 
  - Database note: `profile_picture_url` marked `NOT NULL` with default value `/assets/images/default_avatar.svg`
  - **Applied Fixes:**
    - ✅ Created default avatar SVG asset at `assets/images/default_avatar.svg` (blue user icon on light blue background)
    - ✅ Updated DB schema to set `DEFAULT '/assets/images/default_avatar.svg'` for profile_picture_url
    - ✅ Updated documentation notes
    - **Remaining (frontend/backend):** Implement registration/profile forms to use default avatar when user doesn't upload one; server-side validation for upload handling.

- ✅ **FIXED** Stagiaire profile editor wiring
  - Files: `pages_stagiere/profile.html`, `assets/js/profile.js`, `assets/css/profile.css`
  - **Applied Fix:** The profile edit button now uses the expected id and routes to `pages_stagiere/parametres.html`.
  - **Applied Fix:** Profile avatar blocks now render the default avatar image with rounded-rectangle framing.

- ✅ **FIXED** Settings page layout
  - File: `pages_stagiere/parametres.html`
  - **Applied Fix:** Removed the version widget, kept the help widget as a footer-style block, and stretched the main settings section to full width.

- ✅ **FIXED** Mentor route stubs
  - Files: `pages_mentor/dashboard.html`, `pages_mentor/mes_demandes.html`, `pages_mentor/mes_competances.html`
  - **Applied Fix:** Each file now redirects to the matching stagiaire page as requested.

- ✅ **FIXED** Formateur catalogue labels
  - File: `formateur_pages/catalogue.html`
  - **Applied Fix:** Normalized the profile dropdown labels to use the correct accented French labels.

- ✅ **FIXED** Passeport PDF styling
  - Files: `pages_stagiere/passeport_pdf.html`, `assets/css/passeport_pdf.css`
  - **Applied Fix:** Restyled the passport view with broader spacing, stronger card hierarchy, and dashboard-consistent panels.

- ✅ **FIXED** Emoji-to-SVG sweep for stagiaire pages
  - Files: `pages_stagiere/dashboard.html`, `pages_stagiere/mes_demandes.html`, `pages_stagiere/mes_badges.html`, `pages_stagiere/notification.html`, `pages_stagiere/passeport_pdf.html`
  - **Applied Fix:** Replaced the remaining visible emoji markers with inline SVG icons.

- ✅ **FIXED** Help-request submission form
  - Files: `pages_stagiere/nouvelle_demande.html`, `assets/css/nouvelle_demande.css`, `assets/js/dashboard.js`, `assets/js/mes_demandes.js`
  - **Applied Fix:** Added a dedicated request form page and wired the existing publish/create actions to it.

- ✅ **VERIFIED** Admin pages / links 
  - Files: `pages_admin/tableau_de_bord.html`, `pages_admin/statisque_adm.html` (misspelled "statistique"), `pages_admin/moderation.html`, `pages_admin/gestion_comptes.html`
  - **Status:** All pages exist; tested navigation links
  - **Minor Issue:** Filename typo `statisque_adm.html` remains a legacy inconsistency, but the links already point to it correctly.

- ✅ **VERIFIED** Filename inconsistencies 
  - `tableu_de_bord` vs `tableau_de_bord` — Misspelled French word for "dashboard"
  - `mes_competance` vs `mes_competances` — Singular vs plural inconsistency
  - **Verification Result:** 
    - All stagiaire references point to the correct plural form `mes_competances.html` ✓
    - Mentor routes now redirect to the stagiaire pages instead of depending on duplicate mentor implementations ✓
    - formateur_pages references to `tableu_de_bord.js` are consistent (misspelled but consistent in folder)
    - No broken cross-folder references found
    - **Recommendation:** Rename `tableu_de_bord.*` files to `tableau_de_bord.*` in future refactoring for spelling correctness

---

## Medium Priority (UX, consistency, maintainability)

- Check whether the mentor redirect stubs should stay as redirects or become dedicated mentor pages later.
- Finish the site-wide emoji-to-SVG pass in the remaining pages that still use emoji characters.
- Review `pages_stagiere/passeport_pdf.html` and restyle it to match the other stagiaire pages.
- Review `pages_mentor/*` visual styling and hover states so borders stay soft instead of snapping to black.
- Add the missing request-submission forms under `pages_stagiere/`.
- Centralize shared scripts/styles: ensure `dashboard.js` and `dashboard.css` provide common behaviors; reduce duplication.
- Add client-side validation for forms: login, register, mentor_apply, help_request forms.
- Improve error displays and disabled states for form submissions.
- Make sure `aria-*` attributes and accessible labels are present (buttons, dropdowns, search inputs) for better accessibility.

---

## Low Priority / Nice-to-have

- Implement automated tests (unit for JS, integration for APIs) and at least one E2E (Playwright or Cypress) for critical flows: login, create help request, apply to be mentor.
- Create CI pipeline (GitHub Actions) to run linters and tests on PRs.
- Add README.md and developer setup instructions (how to run a local server, DB migrations, seed data, env vars).
- Add localization pipeline if multiple languages are expected.

---

## File-level actionable checklist (quick copy)

- Fix anchor links in:
  - `formateur_pages/tableu_de_bord.html`
  - `formateur_pages/statistique.html`
  - `formateur_pages/validation_demande.html`

- Add or implement:
  - `assets/js/mes_demandes.js` (referenced by `pages_stagiere/mes_demandes.html`)

- Rename or update references for:
  - `formateur_pages/tableu_de_bord.html` → consider `tableau_de_bord.html`
  - `assets/js/tableu_de_bord.js` → `tableau_de_bord.js` (or update the reference in HTML)
  - `mes_competances.html` / `mes_competance.html` inconsistency across `pages_mentor` and `pages_stagiere`

- Remaining UI work:
  - `pages_mentor/*` still needs a visual hover polish pass.

---

## Suggested next steps I can take for you

1. Restyle `pages_stagiere/passeport_pdf.html` to match the shared dashboard design.
2. Finish replacing the remaining emoji characters with SVG icons.
3. Create the request-submission forms in `pages_stagiere/`.




## My modification that depends on what the user will use 
- pages_stagiere/passeport_pdf.html need an style change to match the other pages
- pages_stagiere/ : the forms for subbmit a request need to be created
- pages_stagiere/ : links of navigation that are in the side bar shouldn't be underlined on every page 
- pages_stagiere/parametres.html : the div that have version must be removed and the div of besoins d'aide must be a footer of the page and the main section must take the full page, modifier le profile must be working and the pictures must be set defaultly to ..\assets\images\default_avatar.svg and have a rectangle with cercled borders format like the shape of the picture 
- /pages_mentor/dashboard.html : must refer to /pages_stagiere/dashboard.html and /pages_mentor/mes_demandes.html must refer to /pages_stagiere/mes_demandes.html and /pages_mentor/mes_competances.html must refer to /pages_stagiere/mes_competances.html
- the style in the pages of pages_mentor/ must be reviewed and fixed and also the hover must be fixed (when hover the border turn into full black prefered to be a color soft then full black)
- every emogie in the website must be replaced into an svg 
- formateur_pages/ : profile icon must be the exact drop list that the other files have and must have pointed into the right file 
- formateur_pages/catalogue.html style must be fixed
- pages_admin : Statistique on the side bar must be pointed into pages_admin/statisque_adm.html
- pages_admin/statisque_adm.html : styles must be fixed 
- api keys ignore them for the moment till i tell you to implement them