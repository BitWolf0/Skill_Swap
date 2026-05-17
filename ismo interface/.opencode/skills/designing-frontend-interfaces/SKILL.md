---
name: designing-frontend-interfaces
description: Use when building distinctive, production-grade frontend interfaces with high design quality. Triggers include requests to create web components, pages, artifacts, posters, or applications (websites, landing pages, dashboards, React components, HTML/CSS layouts, or styling/beautifying any web UI). Generates creative, polished code and UI design that avoids generic AI aesthetics.
license: Complete terms in LICENSE.txt
---

This skill guides creation of distinctive, production-grade frontend interfaces that avoid generic "AI slop" aesthetics. Implement real working code with exceptional attention to aesthetic details and creative choices.

The user provides frontend requirements: a component, page, application, or interface to build. They may include context about the purpose, audience, or technical constraints.

## Project Context: ISMO-SkillSwap

This is a **pure static frontend** (HTML/CSS/JS) — no build system, no bundler, no framework. All styling uses CSS custom properties defined in `assets/css/dashboard.css`. The project's design system font is **Inter** — do not override it unless explicitly asked. Use the existing `--var-*` design tokens for colors, spacing, and typography. Don't introduce theme files or external font services.

## Design Thinking

Before coding, understand the context and commit to a BOLD aesthetic direction:

-   **Purpose**: What problem does this interface solve? Who uses it?
-   **Tone**: Pick an extreme: brutally minimal, maximalist chaos, retro-futuristic, organic/natural, luxury/refined, playful/toy-like, editorial/magazine, brutalist/raw, art deco/geometric, soft/pastel, industrial/utilitarian, etc. There are so many flavors to choose from. Use these for inspiration but design one that is true to the aesthetic direction.
-   **Constraints**: Technical requirements (framework, performance, accessibility).
-   **Differentiation**: What makes this UNFORGETTABLE? What's the one thing someone will remember?

**CRITICAL**: Choose a clear conceptual direction and execute it with precision. Bold maximalism and refined minimalism both work - the key is intentionality, not intensity.

Then implement working code (HTML/CSS/JS, React, Vue, etc.) that is:

-   Production-grade and functional
-   Visually striking and memorable
-   Cohesive with a clear aesthetic point-of-view
-   Meticulously refined in every detail

## Frontend Aesthetics Guidelines

Focus on:

-   **Typography**: Choose fonts that are beautiful, unique, and interesting. Avoid generic fonts like Arial; opt instead for distinctive choices that elevate the frontend's aesthetics. Pair a distinctive display font with a refined body font. For this project, Inter is the established body font — work with it, don't replace it.
-   **Color & Theme**: Commit to a cohesive aesthetic. Use CSS variables for consistency. Dominant colors with sharp accents outperform timid, evenly-distributed palettes.
-   **Motion**: Use animations for effects and micro-interactions. Prioritize CSS-only solutions for HTML. Focus on high-impact moments: one well-orchestrated page load with staggered reveals (animation-delay) creates more delight than scattered micro-interactions. Use scroll-triggering and hover states that surprise.
-   **Spatial Composition**: Unexpected layouts. Asymmetry. Overlap. Diagonal flow. Grid-breaking elements. Generous negative space OR controlled density.
-   **Backgrounds & Visual Details**: Create atmosphere and depth rather than defaulting to solid colors. Add contextual effects and textures that match the overall aesthetic.

NEVER use generic AI-generated aesthetics like overused font families (Roboto, Arial, system fonts), cliched color schemes (particularly purple gradients on white backgrounds), predictable layouts and component patterns, and cookie-cutter design that lacks context-specific character.

**IMPORTANT**: Match implementation complexity to the aesthetic vision. Maximalist designs need elaborate code with extensive animations and effects. Minimalist or refined designs need restraint, precision, and careful attention to spacing, typography, and subtle details.
