---
name: web-artifacts-builder
description: Build self-contained HTML/CSS/JS web artifacts — widgets, charts, cards, interactive demos, embedded tools, landing sections, or any reusable frontend component. Use when the task involves generating a deliverable web artifact for embedding, sharing, or standalone use.
license: Complete terms in LICENSE.txt
---

# Web Artifacts Builder

## Purpose

Build self-contained, production-grade web artifacts using vanilla HTML, CSS, and JS — no framework dependencies. Artifacts are single-file or minimal-file deliverables suitable for embedding, preview, or standalone use.

## Principles

- **Self-contained** — no external runtime dependencies (CDN links allowed for libraries like Chart.js, Alpine.js, etc.)
- **Responsive** — works on mobile through desktop
- **Accessible** — semantic HTML, ARIA attributes, keyboard navigable
- **Theme-aware** — respects `prefers-color-scheme` or accepts a theme token object

## Output Formats

| Format | When to use |
|---|---|
| Single `.html` file | Embeddable widget, demo, preview |
| `.html` + `.css` + `.js` | Complex artifact with separation of concerns |
| Inline snippet | Chat previews, small UI examples |

## Project Context: ISMO-SkillSwap

When building artifacts for this project, use the existing design tokens (`--var-*` in `dashboard.css`) and the Inter font family. Match the sidebar/topbar layout patterns from `pages_*/` directories.
