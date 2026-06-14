---
name: skill-creator
description: Create, edit, and manage opencode skill files. Use when generating a new skill directory with SKILL.md, updating an existing skill's metadata or instructions, or organizing skills under .opencode/skills/. Includes conventions for frontmatter, description, and trigger phrases.
license: Complete terms in LICENSE.txt
---

# Skill Creator

## Purpose

Create and maintain opencode skills — structured markdown files with YAML frontmatter that teach the AI how to handle specific tasks.

## Convention

Each skill lives in its own directory under `.opencode/skills/<skill-name>/` and contains a `SKILL.md` file with:

```yaml
---
name: <hyphenated-name>
description: |
  One-sentence trigger description. Starts with "Use when..."
  Includes concrete examples of what triggers this skill.
license: Complete terms in LICENSE.txt
---
```

## Directory Structure

```
.opencode/skills/
  <skill-name>/
    SKILL.md
    assets/          # optional: images, templates, references
    scripts/         # optional: helper scripts
```

## Registration

Skills are auto-discovered from the `skills.paths` array in `opencode.json`. No manual registration needed beyond placing the directory under `.opencode/skills/`.

## Best Practices

1. **Descriptions** — Be specific about triggers ("Use when ..."). Include antonyms of what the skill is NOT for.
2. **Project context** — If the skill is project-specific, reference project files, tokens, and conventions.
3. **Licensing** — Keep `license: Complete terms in LICENSE.txt` for consistency.
