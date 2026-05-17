---
name: dom-hydration-debug
description: Diagnose and fix DOM manipulation bugs and static-content hydration failures — elements not found, events not firing, dynamic content not rendering, selector mismatches, and timing/race conditions in vanilla JS frontends.
---

# DOM Manipulation & Hydration Debugging

Debug problems where JavaScript interacts with the DOM — elements missing, events not binding, dynamic content failing to appear, or static HTML templates not properly "hydrating" with interactive behavior.

## When to Use

- "This button does nothing when clicked"
- "DOM element is null / not found in JS"
- "Dynamic content renders blank"
- "Event listeners only work after page refresh"
- "Script runs before the element exists"
- "Appended HTML has no event handlers"
- "Multiple elements share the same ID"
- "Content flashes then disappears"

## Common Root Causes

### 1. Timing (script vs DOM ready)

| Symptom | Likely Cause |
|---|---|
| `getElementById` returns `null` | Script runs before element exists |
| Events work on reload but not first load | Async content + synchronous listener |
| Partial content renders, rest missing | Progressive rendering + race condition |

### 2. Selector problems

| Symptom | Likely Cause |
|---|---|
| Only first element affected | `querySelector` instead of `querySelectorAll` |
| Wrong element targeted | ID collision or stale class name |
| Works in dev, fails in prod | Minifier renamed class or removed attribute |

### 3. Event delegation failures

| Symptom | Likely Cause |
|---|---|
| Existing elements work, new ones don't | Direct binding vs delegation |
| Click works, dynamic types don't | Event type typo or not bubbling |
| Touch works, click doesn't (or vice versa) | Mobile vs desktop event model |

### 4. Hydration (static → interactive)

| Symptom | Likely Cause |
|---|---|
| Rendered content, no behavior | Content injected after event binding |
| Form doesn't submit | Dynamic form missing hidden fields |
| Dropdown/accordion stays collapsed | JS not re-initialized on dynamic content |

## Workflow

### 1. Reproduce and isolate

- Find the exact user interaction that fails
- Check browser console for errors
- Identify the JS function responsible
- Determine if the element exists at the time of execution

### 2. Trace the DOM state

- `document.readyState` — is DOMContentLoaded fired?
- `document.querySelector('<selector>')` — null at execute time?
- Check element in DevTools Elements panel — is it where JS expects it?
- Verify parent hierarchy for delegated events

### 3. Check the event pipeline

- Is the event name correct (typos: `onclick` vs `click`)?
- Is the listener attached before the event fires?
- For dynamically added elements: is delegation used?
- Is `stopPropagation` or `preventDefault` blocking elsewhere?

### 4. Fix hydration gaps

For static HTML "hydrated" by JS:

| Pattern | Fix |
|---|---|
| Inline `<script>` at end of body | Move to DOMContentLoaded |
| `innerHTML` / `insertAdjacentHTML` | Re-bind listeners after injection |
| Event on dynamic children | Use delegation: `parent.addEventListener('click', selector, handler)` |
| Race with async data | Use Promise/then chain before attaching |

### 5. Verify

- Reload the page fresh (not cached)
- Test with slow network throttling (timing issues)
- Test on target browsers (selector support differences)
- Confirm no console errors

## Decision Tree

```
DOM element missing?
├─ null at query time → script timing (move to DOMContentLoaded)
├─ exists but wrong element → selector specificity / ID collision
└─ exists in HTML but not in JS → case sensitivity / whitespace

Event not firing?
├─ Binding on element that doesn't exist yet → use delegation
├─ Binding on selector that matches nothing → check class/ID names
├─ Event type wrong → verify spelling and case
└─ Another handler stopped propagation → check ancestor listeners

Dynamic content has no behavior?
├─ Injected via innerHTML → re-bind or use delegation
└─ Injected via JS template → initialize after append
```

## Best Practices for Vanilla JS

- Always wait for `DOMContentLoaded` before querying
- Prefer event delegation for lists / dynamic containers
- Use `querySelectorAll` + iteration (not single `querySelector`) when multiple elements match
- Cache DOM queries that run frequently
- Use `data-*` attributes instead of classes for JS hooks (avoids style/script collision)
- Destroy and re-create observers (MutationObserver, IntersectionObserver) when swapping DOM
