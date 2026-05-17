---
name: visual-reconnaissance
description: Inspect, audit, and debug the visual layer of frontend code — CSS, layout, spacing, typography, colors, responsive breakpoints, design tokens, and accessibility contrast. Use for UI analysis or visual consistency checks.
---

# Visual Reconnaissance

Systematic inspection of a frontend's visual surface: what the user sees, how it's built in CSS, and whether it's consistent.

## When to Use

- "Check if this page follows our design system"
- "Why does this layout break at tablet size?"
- "Audit the color palette and contrast ratios"
- "Find unused or duplicated CSS rules"
- "Verify responsive breakpoint behavior"
- "Check spacing and alignment consistency"

## Workflow

### 1. Map the visual inventory

- Enumerate all CSS files and inline `<style>` blocks
- List all custom properties (`--var-*`) defined vs used
- Identify layout strategies (flexbox, grid, float, positioning)
- Note any hardcoded color/length values outside design tokens

### 2. Analyze layout structure

- Trace the box model for key containers
- Verify spacing uses a consistent unit/scale
- Check z-index stacking context for overlaps
- Confirm `overflow` behavior on critical containers

### 3. Validate design tokens

| Property | Check |
|---|---|
| Colors | Every hex/rgb/hsl vs defined palette; count unique values |
| Typography | Font families, sizes, line heights, weights — any orphans? |
| Spacing | Gaps, margins, paddings — consistent step scale? |
| Borders | Width, style, color, radius — uniform or ad-hoc? |
| Shadows | Elevation levels — defined system or scattered values? |

### 4. Responsive review

- List all `@media` breakpoints — are they in a system or arbitrary?
- Check mobile-first vs desktop-first approach
- Identify elements without responsive treatment
- Flag fixed-width containers that don't scale

### 5. Accessibility check

- `color` + `background-color` contrast ratio (aim for 4.5:1+)
- `:focus` / `:focus-visible` styles present
- `font-size` in relative units (`rem`/`em`) where appropriate
- `aria-*` attributes match visual state

## Analysis Levels

| Level | Scope |
|---|---|
| `quick` | Inventory CSS files, list tokens, count hardcoded values |
| `standard` | Full token audit + layout check + responsive breakpoints |
| `deep` | Accessibility contrast audit + unused CSS detection + stacking context review |

## Output

Return a report with:

1. Visual inventory summary (files, tokens, layout approaches)
2. Token compliance score (x% of values come from variables)
3. Responsive breakpoint map
4. Accessibility findings (contrast, focus, relative units)
5. Concrete fixes (file:line for each issue)

## Best Practices

- Compare against `DESIGN_SYSTEM.md` if one exists — don't invent standards
- Distinguish between intentional one-off overrides and drift
- For this project: use CSS custom properties from `dashboard.css:root` as the token source of truth
- Do not redesign — only audit, diagnose, and report
