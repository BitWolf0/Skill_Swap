---
name: canvas-design
description: Design and generate canvas-based layouts, diagrams, wireframes, flowcharts, infographics, and visual mockups using HTML Canvas, SVG, or CSS. Use when creating visual diagrams, network graphs, org charts, data flow visualizations, or any 2D vector/rendered graphic.
license: Complete terms in LICENSE.txt
---

# Canvas Design

## Purpose

Create visual diagrams, wireframes, flowcharts, infographics, and data visualizations using HTML Canvas 2D, SVG, or pure CSS layout techniques.

## Tool Selection

| Technique | Best for |
|---|---|
| **SVG** | Diagrams, icons, charts, responsive graphics, accessibility (DOM nodes) |
| **Canvas 2D** | Dense data viz, pixel manipulation, game-like graphics, real-time updates |
| **CSS** | Wireframes, layout mockups, pure UI compositions (no runtime drawing) |
| **Combined** | Interactive visualizations (SVG overlays on Canvas, etc.) |

## Project Context: ISMO-SkillSwap

When designing in-canvas visuals for this project:
- Use the design token colors from `dashboard.css` (`--primary`, `--secondary`, etc.)
- Match the Inter font family for any text rendered in canvas/SVG
- Follow the existing dashboard layout proportions (sidebar 248px, topbar 64px)

## Deliverables

- Self-contained HTML file with embedded or linked SVG/Canvas
- Responsive by default (use `viewBox` for SVG, devicePixelRatio-aware for Canvas)
- Include legend and labels for any data-driven graphic
