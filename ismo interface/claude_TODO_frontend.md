# ISMO-SkillSwap — Frontend TODO (Based on Repo Audit)
> Generated from: `/ismo interface` folder analysis — what actually exists vs what's missing or broken.

---

## 🔴 CRITICAL — Broken / Missing Files

### 1. Missing JS files
- [ ] Create `assets/js/nouvelle_demande.js` — `nouvelle_demande.html` has no script (form does nothing)
- [ ] Create `assets/js/statistique.js` — both `formateur_pages/statistique.html` and `pages_admin/statistique_adm.html` have no JS
- [ ] Create `assets/js/inscription.js` — registration page doesn't exist at all yet (see below)

### 2. Missing HTML pages (required by spec but absent from repo)
- [ ] Create `pages_stagiaire/inscription.html` — **no registration page exists for any role**
- [ ] Create `pages_stagiaire/recherche.html` — OF8 (skill search engine) completely missing
- [ ] Create `pages_mentor/recherche.html` — copy of above with mentor sidebar
- [ ] Create `formateur_pages/recherche.html` — copy of above with formateur sidebar
- [ ] Create `pages_stagiaire/classement.html` — Top Mentors / filière ranking (OF7) missing
- [ ] Create `pages_mentor/classement.html` — same, mentor version
- [ ] Create `pages_mentor/notification.html` — exists for stagiaire, missing for mentor
- [ ] Create `pages_mentor/parametres.html` — settings page missing for mentor role
- [ ] Create `formateur_pages/parametres.html` — settings page missing for formateur role
- [ ] Create `pages_admin/parametres.html` — settings page missing for admin role
- [ ] Create `pages_admin/catalogue_admin.html` — admin must manage skill catalogue (spec), only formateur version exists

### 3. Missing JS + CSS pairs for new pages
- [ ] Create `assets/js/inscription.js` — form validation + role selector + submit logic
- [ ] Create `assets/js/recherche.js` — live search, filters by level / filière / availability
- [ ] Create `assets/css/recherche.css` — mentor card grid, search bar, filter row
- [ ] Create `assets/js/classement.js` — leaderboard table render, tab switch (Top Mentors / Filière)
- [ ] Create `assets/css/classement.css` — table styles, tab buttons
- [ ] Create `assets/js/catalogue_admin.js` — CRUD for skill catalogue (add / edit / disable / delete)

---

## 🔴 HIGH — Broken Navigation & Links

### 4. Mentor sidebar broken links
> All `pages_mentor/*.html` sidebars link to pages that don't exist in that folder
- [ ] Fix sidebar in `pages_mentor/dashboard.html`
- [ ] Fix sidebar in `pages_mentor/marketplace.html`
- [ ] Fix sidebar in `pages_mentor/mes_aides.html`
- [ ] Fix sidebar in `pages_mentor/mes_badges.html`
- [ ] Fix sidebar in `pages_mentor/mes_competances.html`
- [ ] Fix sidebar in `pages_mentor/mes_demandes.html`
- [ ] **Decision needed:** redirect mentor links to `../pages_stagiaire/*.html` OR create dedicated mentor pages for each

### 5. Formateur dashboard broken links
- [ ] Fix `formateur_pages/tableau_de_bord.html` — `#validation` and `#statistics` are broken placeholders
- [ ] Fix `formateur_pages/validation_demande.html` — `#statistics` and `#catalog` broken
- [ ] Fix `formateur_pages/statistique.html` — `#catalog` broken

### 6. Admin sidebar broken
- [ ] Fix `pages_admin/tableau_de_bord.html` — "Statistiques" link points to wrong filename (actual file is `statistique_adm.html`)

### 7. Placeholder `#` links across all pages
- [ ] Fix `pages_stagiaire/login.html` — "Conditions d'utilisation" and "Politique de confidentialité" are `href="#"`
- [ ] Fix `pages_stagiaire/parametres.html` footer — links point to `/pages/` folder that doesn't exist
- [ ] Add real `href` for "Mon profil", "Paramètres", "Déconnexion" in all role sidebars (currently `#`)
- [ ] Add link from `login.html` → `inscription.html` ("Pas encore de compte ?")

---

## 🟡 MEDIUM — File/Folder Naming & Consistency

### 8. Typo renames — update ALL `<link>`, `<script>`, `<a href>` references after each rename
- [ ] Rename `formateur_pages/tableu_de_bord.html` → `tableau_de_bord.html`
- [ ] Rename `assets/css/tableu_de_bord.css` → `tableau_de_bord.css` (if file exists with typo)
- [ ] Rename `assets/js/tableu_de_bord.js` → `tableau_de_bord.js` (if file exists with typo)
- [ ] Rename `pages_admin/statisque_adm.html` → `statistique_adm.html`

### 9. Naming convention — decide and apply
- [ ] Pick one: French filenames (`mes_demandes`) OR English (`mentor_apply`) — currently mixed
- [ ] Rename inconsistent files and update all references across the project

---

## 🟡 MEDIUM — Frontend Features Missing

### 10. Mentor rating system (OF3) — zero UI exists
- [ ] Add "⭐ Noter ce mentor" button to resolved request cards in `pages_stagiaire/mes_demandes.html`
- [ ] Build rating modal (stars 1–5 + comment textarea) in `mes_demandes.html`
- [ ] Add star interaction + submission logic to `assets/js/mes_demandes.js`
- [ ] Add modal CSS to `assets/css/mes_demandes.css`

### 11. `nouvelle_demande.html` form — not wired up
- [ ] Verify form fields have correct `id` attributes (`titre`, `description`, `competence`, `urgence`)
- [ ] Write `assets/js/nouvelle_demande.js`: validate fields + show success/error feedback
- [ ] Pre-fill mentor field from URL param `?mentor=` when redirected from search page

### 12. Statistics pages — add HTML hooks for JS
- [ ] Add stat card elements to `formateur_pages/statistique.html` (ids: `stat-demandes`, `stat-resolues`, `stat-competences`, `stat-note`)
- [ ] Add `<canvas id="competences-chart">` to both statistics pages
- [ ] Add stat card elements to `pages_admin/statistique_adm.html` (same + `stat-mentors`, `stat-stagiaires`)
- [ ] Wire both pages to `assets/js/statistique.js` with `<script>` tag

### 13. `passeport_pdf.html` — restyle and wire up
- [ ] Restyle to match dashboard design system (card hierarchy, spacing, colors)
- [ ] Verify it shows: validated skills, badges earned, total helps, avg rating, points
- [ ] Make "Télécharger PDF" button trigger `window.print()` or a backend PDF export

### 14. `parametres.html` layout fixes (stagiaire version)
- [ ] Remove the version display div
- [ ] Move "Besoin d'aide" to page footer
- [ ] Extend main content section to full width
- [ ] Fix profile photo shape if needed (rounded rectangle vs circle)

---

## 🟡 MEDIUM — Responsive / Mobile (currently zero media queries)

### 15. Add responsive CSS
- [ ] Add tablet breakpoint (≤1024px) to `dashboard.css`: collapse sidebar to icon-only, hide nav labels
- [ ] Add mobile breakpoint (≤768px): hide sidebar, show hamburger button
- [ ] Make stat grids and card grids single-column on mobile
- [ ] Hide low-priority table columns on mobile (e.g. leaderboard)

### 16. Add hamburger menu to every page
- [ ] Add hamburger `<button>` HTML to every page's topbar
- [ ] Add hamburger + sidebar overlay CSS to `dashboard.css`
- [ ] Add sidebar toggle JS (in `dashboard.js` or a new shared `mobile.js`)
- [ ] Verify every HTML file has `<meta name="viewport" content="width=device-width, initial-scale=1.0">`

---

## 🟢 LOW — Polish & Cleanup

### 17. Replace emoji with SVG icons
- [ ] `formateur_pages/statistique.html` — replace 🚀, 💎, 📅, 👥, 🏆, 📚 etc.
- [ ] `pages_admin/statistique_adm.html` — same
- [ ] Check remaining pages for stray emoji

### 18. Sidebar link decoration
- [ ] Add `text-decoration: none` on sidebar links in all states (hover, active) — all role folders

### 19. Mentor card hover states
- [ ] Fix hover border color in `pages_mentor/*.html` — snaps to pure black, change to soft accent color

### 20. Default avatar fallback
- [ ] Verify every page that shows a user avatar uses `assets/images/default_avatar.svg` as fallback

### 21. Accessibility basics
- [ ] Add `aria-label` to all icon-only buttons (search, close, hamburger)
- [ ] Add `aria-expanded` to dropdown menus
- [ ] Add `role="alert"` to flash/error messages

---

## Summary

| Priority | Tasks | What it unlocks |
|---|---|---|
| 🔴 Critical | ~14 tasks | Pages that exist but do nothing / navigation completely broken |
| 🔴 High | ~15 tasks | Broken links blocking all role navigation |
| 🟡 Medium | ~25 tasks | Missing features + mobile support |
| 🟢 Low | ~8 tasks | Polish + accessibility |

> **Recommended order:** Fix broken nav links (1–2h, unblocks everything) → add missing HTML pages → wire up missing JS → connect to backend when it's ready.
