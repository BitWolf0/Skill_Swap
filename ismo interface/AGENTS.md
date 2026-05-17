# ISMO-SkillSwap — Agent Guide

## Project

Multi-role (stagiaire/mentor/formateur/admin) skill-swap platform. **Pure static frontend** — HTML, CSS, JS. No build system, no package manager, no bundler, no tests, no CI.

All source lives under `ismo interface/`.

OpenCode config is at `ismo interface/.opencode/opencode.json`. Add custom skills under `.opencode/skills/<name>/SKILL.md`.

## Dev workflow

```powershell
# Preview (VS Code Live Server on port 5501)
# Configured in .vscode/settings.json
code "ismo interface" && start http://localhost:5501
```

No build, lint, typecheck, or test commands exist. Open any `.html` file directly in browser or through Live Server.

## Key files & directories

| Path | Purpose |
|---|---|
| `project_overview.json` | Project description & file inventory (source of truth for structure) |
| `DESIGN_SYSTEM.md` | Design tokens, layout, component specs |
| `TODO` | Remaining work roadmap |
| `PROJECT_REPORTS_MERGED.md` | Completed fixes & open items (consolidated from multiple reports) |
| `assets/css/dashboard.css` | Main stylesheet — design tokens (CSS vars), sidebar/topbar, responsive |
| `assets/js/dashboard.js` | Shared JS patterns (dropdown, sidebar toggle, toast, search) |
| `pages_stagiaire/` | Stagiaire pages (dashboard, login, profile, requests, etc.) |
| `pages_mentor/` | Mentor pages (dashboard, marketplace, requests, etc.) |
| `pages_admin/` | Admin pages (accounts, moderation, dashboard, stats) |
| `formateur_pages/` | Trainer pages (catalogue, validation, dashboard, stats) |
| `ismo_skillswap.sql` | MySQL 8.0+ schema (18 tables, utf8mb4) |

## Conventions

- **Language**: French UI, mixed French/English filenames (e.g., `mentor_apply.html`, `mes_demandes.html`). Don't rename without explicit request.
- **Layout**: Fixed sidebar (248px) + sticky topbar (64px) + main content. `dashboard.css` provides shared tokens.
- **Font**: Inter (Google Fonts). Inline SVG icons (stroke-based, 20x20, `currentColor`).
- **CSS**: All colors/spacing via `--var-name` tokens defined in `dashboard.css:root`. Do not hardcode colors.
- **JS**: `'use strict'`, DOM references at top, event listeners below. Toast system, dropdown, sidebar toggle shared across all pages.
- **Accessibility**: `aria-current="page"` on active nav item, `aria-label` on sidebar/topbar.

## Known quirks

- `pages_stagiere/` is a misspelling (should be `pages_stagiaire`). Some files use `stagiere` vs `stagiaire` inconsistently. Don't fix unless asked.
- `tableu_de_bord` and `statisque_adm` are misspelled filenames. Don't rename unless asked.
- `inscription.html` immediately redirects to `login.html?show=signup`.
- No backend — all pages are static. Client-side JS simulates interactions (toasts, dropdowns, modals).
- `project_overview.json` lists files but may be slightly stale.
