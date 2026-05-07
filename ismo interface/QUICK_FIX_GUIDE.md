# Quick Fix Guide - Link Issues

## 🔴 CRITICAL FIXES REQUIRED (5 Issues)

### Issue #1: formateur/statistique.html - Broken navigation link
**Location:** Line 74
**Current:** `<a href="#catalog" class="nav-item" id="nav-catalog">`
**Fix:** `<a href="catalogue.html" class="nav-item" id="nav-catalog">`

---

### Issue #2: formateur/tableu_de_bord.html - Broken navigation link
**Location:** Line 49
**Current:** `<a href="#validation" class="nav-item" id="nav-validation">`
**Fix:** `<a href="validation_demande.html" class="nav-item" id="nav-validation">`

---

### Issue #3: formateur/tableu_de_bord.html - Broken navigation link
**Location:** Line 61
**Current:** `<a href="#statistics" class="nav-item" id="nav-statistics">`
**Fix:** `<a href="statistique.html" class="nav-item" id="nav-statistics">`

---

### Issue #4: formateur/validation_demande.html - Broken navigation links (2 instances)
**Location 1:** Line 61
**Current:** `<a href="#statistics" class="nav-item" id="nav-statistics">`
**Fix:** `<a href="statistique.html" class="nav-item" id="nav-statistics">`

**Location 2:** Line 74
**Current:** `<a href="#catalog" class="nav-item" id="nav-catalog">`
**Fix:** `<a href="catalogue.html" class="nav-item" id="nav-catalog">`

---

## 🟡 IMPORTANT FIX REQUIRED (1 Issue)

### Issue #5: pages_stagiere/mes_demandes.html - Missing JavaScript file
**Location:** End of file, before `</body>`
**Current:** Missing `<script src="../assets/js/mes_demandes.js"></script>`
**Fix:** Add this line before closing body tag:
```html
<script src="../assets/js/dashboard.js"></script>
<script src="../assets/js/mes_demandes.js"></script>
```

Note: Line 518 has dashboard.js but mes_demandes.js is missing.

---

## 🟠 OPTIONAL FIX (1 Issue - Spelling Consistency)

### Issue #6: pages_mentor/ files - Misspelled file reference
**Files affected:**
- pages_mentor/marketplace.html
- pages_mentor/mes_aides.html
- pages_mentor/mes_badges.html

**Current:** `href="mes_competance.html"` (missing 's')
**File actually named:** `mes_competances.html` (in pages_stagiere/)

**Options:**
1. **Option A:** Update links in pages_mentor/ files
   - Change `mes_competance.html` to `mes_competances.html`
   - Update path to: `../pages_stagiere/mes_competances.html` if needed

2. **Option B:** Rename the file
   - Rename `pages_stagiere/mes_competances.html` to `mes_competance.html`
   - Update CSS and JS files accordingly

**Recommendation:** Choose Option A (update links) as Option B would require more changes.

---

## Summary of Changes

| Priority | Count | Type | Action |
|----------|-------|------|--------|
| Critical | 5 | Broken links | Replace # with .html filenames |
| Important | 1 | Missing file | Add script tag |
| Optional | 1 | Spelling | Update filename references |
| **TOTAL** | **7** | **Issues** | **Fix for full functionality** |

---

## Testing Checklist After Fixes

- [ ] Test all navigation links in formateur/ section
  - [ ] Dashboard → Validation
  - [ ] Dashboard → Statistique
  - [ ] Statistique → Catalogue
  - [ ] Validation → Statistique
  - [ ] Validation → Catalogue

- [ ] Test mes_demandes.js loads without console errors
  - [ ] Open pages_stagiere/mes_demandes.html in browser
  - [ ] Check browser console for JavaScript errors

- [ ] Test cross-folder navigation in pages_mentor/ section
  - [ ] marketplace.html → mes_competances.html
  - [ ] mes_aides.html → mes_competances.html
  - [ ] mes_badges.html → mes_competances.html

---

## File Modification Summary

```
Files to modify: 4
  1. formateur/statistique.html (1 change)
  2. formateur/tableu_de_bord.html (2 changes)
  3. formateur/validation_demande.html (2 changes)
  4. pages_stagiere/mes_demandes.html (1 addition)

Optional files to modify: 3
  5. pages_mentor/marketplace.html (1 change)
  6. pages_mentor/mes_aides.html (1 change)
  7. pages_mentor/mes_badges.html (1 change)

Total lines to change: 9 (11 if including optional fixes)
```

---

## Quick Copy-Paste Fixes

### Fix 1 - formateur/statistique.html
Replace this (line 74):
```html
<a href="#catalog" class="nav-item" id="nav-catalog">
```
With:
```html
<a href="catalogue.html" class="nav-item" id="nav-catalog">
```

### Fix 2 - formateur/tableu_de_bord.html (First instance, line 49)
Replace:
```html
<a href="#validation" class="nav-item" id="nav-validation">
```
With:
```html
<a href="validation_demande.html" class="nav-item" id="nav-validation">
```

### Fix 3 - formateur/tableu_de_bord.html (Second instance, line 61)
Replace:
```html
<a href="#statistics" class="nav-item" id="nav-statistics">
```
With:
```html
<a href="statistique.html" class="nav-item" id="nav-statistics">
```

### Fix 4 - formateur/validation_demande.html (First instance, line 61)
Replace:
```html
<a href="#statistics" class="nav-item" id="nav-statistics">
```
With:
```html
<a href="statistique.html" class="nav-item" id="nav-statistics">
```

### Fix 5 - formateur/validation_demande.html (Second instance, line 74)
Replace:
```html
<a href="#catalog" class="nav-item" id="nav-catalog">
```
With:
```html
<a href="catalogue.html" class="nav-item" id="nav-catalog">
```

### Fix 6 - pages_stagiere/mes_demandes.html (Line 518, add after dashboard.js)
Add this line:
```html
<script src="../assets/js/mes_demandes.js"></script>
```

### Fix 7 (Optional) - pages_mentor/marketplace.html (Line 89)
Replace:
```html
<a href="mes_competance.html" class="nav-item" id="nav-competences">
```
With:
```html
<a href="mes_competances.html" class="nav-item" id="nav-competences">
```

### Fix 8 (Optional) - pages_mentor/mes_aides.html (Line 89)
Replace:
```html
<a href="mes_competance.html" class="nav-item" id="nav-competences">
```
With:
```html
<a href="mes_competances.html" class="nav-item" id="nav-competences">
```

### Fix 9 (Optional) - pages_mentor/mes_badges.html (Line 78)
Replace:
```html
<a href="mes_competance.html" class="nav-item" id="nav-competences">
```
With:
```html
<a href="mes_competances.html" class="nav-item" id="nav-competences">
```

---

**Priority:** Fixes 1-6 are critical for full functionality. Fixes 7-9 are optional but recommended for consistency.
