# INCOHÉRENCES CSS

## 1. Design Tokens Divergents

Deux fichiers définissent leurs propres `:root` avec des valeurs DIFFÉRENTES :

| Variable | `dashboard.css` / `tableau_de_bord.css` | `login.css` |
|----------|----------------------------------------|-------------|
| `--radius-md` | **12px** | **14px** |
| `--radius-lg` | **16px** | **22px** |
| `--radius-xl` | **20px** | **28px** |
| `--shadow-sm` | 2 ombres superposées | 1 ombre seule |
| `--shadow-md` | 2 ombres | 1 ombre large |
| `--shadow-lg` | 2 ombres | 1 ombre large |
| `--transition` | 180ms | 220ms |

**Impact :** Les composants utilisant `var(--radius-md)` auront un arrondi différent selon la page. Les ombres et transitions aussi.

**Fix :** Supprimer le bloc `:root` de `login.css`. Un SEUL source de vérité dans `dashboard.css`.

---

## 2. Couleurs Hardcodées — Catalogue.css (pire cas)

**Fichier :** `assets/css/catalogue.css`

Ce fichier contient ~40 couleurs hardcodées au lieu d'utiliser les variables CSS :

- `background: #f8f9fa;` (L.11) → `var(--gray-50)`
- `border-bottom: 1px solid #e9ecef;` (L.22) → `var(--gray-200)`
- `background: #3b82f6;` (L.40) → `var(--blue-500)`
- `background: #2563eb;` (L.55) → `var(--blue-600)`
- Toutes les couleurs de tabs (`#f3f4f6`, `#4b5563`, `#e5e7eb`) → vars gray
- Tous les backgrounds d'icônes de skills (lignes 129-157) → vars
- Tous les badges de niveau (lignes 180-208) → vars
- Section stats entière avec gradient hardcodé (lignes 320-384)

---

## 3. Couleurs Hardcodées — Autres fichiers

### `validation_demande.css`
- `#3B82F6`, `#F97316`, `#06B6D4` → vars
- Tags `.tag-primary`, `.tag-warning`, `.tag-danger` → vars
- `.btn-success` `background: #10B981` → `var(--green-500)`
- `.btn-danger` `background: #DC2626` → `var(--red-500)`

### `tableau_de_bord.css`
- Boutons : `#10B981`, `#DC2626`, `#059669`, `#B91C1C` → vars
- Badges priorité : `#FEE2E2`, `#FEF3C7`, `#DCFCE7` → vars
- Widget badges : gradient `#F5F3FF, #FAF5FF` → vars
- Activity icons : couleurs hardcodées

### `statistique.css`
- `#10B981`, `#EF4444`, `#06B6D4` → vars
- Graphique gradient `#7C3AED, #6D28D9` → var

### `marketplace.css`
- Badges langages : `#F7DF1E`, `#777BB4`, `#FF6B6B`, `#336791` → vars à créer

### `mes_badges_mentor.css`
- Gradients stats cards, couleurs de thème/rareté (blue, green, orange, purple, pink, common, uncommon, rare, epic, legendary)
- Gradient `#8b5cf6 → #db2777`

### `dashboard.css`
- Avatars gradients initials (avatar-l, avatar-e...) → vars
- `.badge-purple`, `.status-pending`, `.status-suspended` → vars

---

## 4. Classes CSS Dupliquées avec Styles Conflits

| Classe | Définie dans | Problème |
|--------|-------------|----------|
| `.btn` | 4 fichiers | padding/radius/font-size différents |
| `.btn-success` | 3 fichiers | background différent (#10B981 vs vars) |
| `.btn-danger` | 4 fichiers | background différent |
| `.validation-card` | 3 fichiers | layout/hover différents |
| `.badge` | dashboard.css ×2 | display différent selon la section |
| `.stat-card` | 5 fichiers | gradient/padding/layout tous différents |
| `.stat-value` | 5 fichiers | font-size: 1.4rem → 2rem |
| `.stat-label` | 6 fichiers | font-size/weight différents |
| `.widget` | 4 fichiers | padding/radius/shadow différents |
| `.skill-card` | 3 fichiers | layout/radius/padding différents |
| `.progress-fill` | 4 fichiers | gradients/transitions différents |
| `.progress-track` | 4 fichiers | hauteur: 6-10px |
| `.filter-btn` | 2 fichiers | padding/radius/weight différents |
| `.section-title` | 3 fichiers | font-size différents |

---

## 5. Variables CSS Manquantes

Couleurs récurrentes qui n'ont PAS de variable CSS :

| Couleur | Usage | Variable suggérée |
|---------|-------|-------------------|
| `#06B6D4` | Cyan — stats, icônes | `--cyan-500: #06B6D4` |
| `#8B5CF6` | Violet — progress bars, gradients | `--violet-500: #8B5CF6` |
| `#B91C1C` | Rouge foncé — btn-danger hover | `--red-600: #B91C1C` |
| `#059669` | Vert foncé — succès | `--green-600: #059669` |
| `#7C3AED` | Violet — admin gradients | `--violet-600: #7C3AED` |
| `#1E40AF` | Bleu foncé — tag text | `--blue-800: #1E40AF` |

---

## 6. Tailles de police incohérentes

| Élément | Valeur 1 | Valeur 2 | Fichiers |
|---------|----------|----------|----------|
| Page title | 1.45rem | 1.75rem | dashboard.css (×2) |
| Section h2 | 1.375rem | 1.1rem | tableau_de_bord.css vs mes_badges.css |
| Stat number | 2.25rem | 2rem / 1.45rem / 32px | 4 fichiers différents |
| Widget padding | 24px | 22px / 20px / 18px | 4 fichiers différents |
| Card padding | 20px | 24px/32px / 18px/20px | 3 fichiers différents |

---

## 7. Animations sans `prefers-reduced-motion`

**Tous les fichiers** — Aucun fichier CSS n'inclut :

```css
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
```

---

## 8. Pas de Dark Mode

Aucune media query `prefers-color-scheme: dark` n'existe dans aucun fichier.
