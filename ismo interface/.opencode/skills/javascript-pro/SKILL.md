---
name: javascript-pro
description: Writes, debugs, and refactors JavaScript code using modern ES2023+ features, async/await patterns, ESM module systems, and Node.js APIs. Use when building vanilla JavaScript applications, implementing Promise-based async flows, optimising browser or Node.js performance, working with Web Workers or Fetch API, or reviewing .js/.mjs/.cjs files for correctness and best practices.
license: MIT
compatibility: opencode
metadata:
  author: https://github.com/Jeffallan
  version: 1.1.0
  domain: language
  triggers: JavaScript, ES2023, async await, Node.js, vanilla JavaScript, Web Workers, Fetch API, browser API, module system
  role: specialist
  scope: implementation
  output-format: code
  related-skills: fullstack-guardian, vue-expert-js
---

# JavaScript Pro

## Project Context: ISMO-SkillSwap

This project uses **vanilla JavaScript in the browser** — no Node.js, no npm, no bundler. JS files are loaded via `<script>` tags in HTML. There is no ESLint, no Jest, no package.json. All JS follows `'use strict'` with DOM queries collected at the top of each script and event listeners below.

When working in this project: use ES features supported broadly (no CJS/ESM modules), avoid Node.js APIs, and structure code as standalone script files. Skip eslint/Jest steps — they don't apply here.

## When to Use This Skill

-   Building vanilla JavaScript applications
-   Implementing async/await patterns and Promise handling
-   Working with modern module systems (ESM/CJS)
-   Optimizing browser performance and memory usage
-   Developing Node.js backend services
-   Implementing Web Workers, Service Workers, or browser APIs

## Core Workflow

1.  **Analyze requirements** — Review `package.json`, module system, Node version, browser targets; confirm `.js`/`.mjs`/`.cjs` conventions
2.  **Design architecture** — Plan modules, async flows, and error handling strategies
3.  **Implement** — Write ES2023+ code with proper patterns and optimisations
4.  **Validate** — Run linter (`eslint --fix`); if linter fails, fix all reported issues and re-run before proceeding. Check for memory leaks with DevTools or `--inspect`, verify bundle size; if leaks are found, resolve them before continuing
5.  **Test** — Write comprehensive tests with Jest achieving 85%+ coverage; if coverage falls short, add missing cases and re-run. Confirm no unhandled Promise rejections

## Constraints

### MUST DO

-   Use ES2023+ features exclusively
-   Use optional chaining (`?.`) and nullish coalescing (`??`)
-   Use async/await for all asynchronous operations
-   Use ESM (`import`/`export`) for new projects
-   Implement proper error handling with try/catch
-   Add JSDoc comments for complex functions
-   Follow functional programming principles

### MUST NOT DO

-   Use `var` (always use `const` or `let`)
-   Use callback-based patterns (prefer Promises)
-   Mix CommonJS and ESM in the same module
-   Ignore memory leaks or performance issues
-   Skip error handling in async functions
-   Use synchronous I/O in Node.js
-   Mutate function parameters
-   Create blocking operations in the browser

## Key Patterns with Examples

### Async/Await Error Handling

```javascript
async function fetchUser(id) {
  try {
    const response = await fetch(`/api/users/${id}`);
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    return await response.json();
  } catch (err) {
    console.error("fetchUser failed:", err);
    return null;
  }
}
```

### Optional Chaining & Nullish Coalescing

```javascript
const city = user?.address?.city ?? "Unknown";
```

### Avoid var / Prefer const

```javascript
const MAX_RETRIES = 3;
let attempts = 0;
```
