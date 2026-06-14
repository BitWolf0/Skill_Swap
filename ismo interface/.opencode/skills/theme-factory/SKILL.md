---
name: theme-factory
description: Generate, export, and apply design theme configurations — color palettes, typography scales, spacing systems, and CSS custom property sets. Use when creating new visual themes, converting between theme formats, or generating CSS token files from design specs.
license: Complete terms in LICENSE.txt
---

# Theme Factory

## Purpose

Generate complete design theme packages from high-level descriptions. Produces CSS custom property sets, JS theme objects, and design-token JSON.

## Outputs

| Format | Description |
|---|---|
| CSS `:root` vars | Token set ready for `dashboard.css` or any stylesheet |
| JSON tokens | Platform-agnostic design token file |
| JS module | `export default { ... }` for JS-driven theming |
| Tailwind/other | Framework-specific config when requested |

## ISMO-SkillSwap Context

This project uses CSS custom properties (`--var-*`) in `assets/css/dashboard.css`. New themes must:
- Prefix all tokens with `--` 
- Match the existing token naming convention (e.g., `--primary`, `--surface`, `--text`)
- Include `--font-family` using Inter (Google Fonts)
- Pass WCAG AA contrast ratios for text on surface colors

## Theme Generation Process

1. Accept a description (mood, brand colors, use case)
2. Propose a 5-color palette + neutral scale + semantic colors
3. Generate typography scale using Inter or specified font
4. Output as CSS `:root` block with all tokens
5. Verify contrast ratios meet WCAG AA
