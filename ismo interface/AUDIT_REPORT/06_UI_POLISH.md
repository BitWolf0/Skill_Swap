# AMÉLIORATIONS UI / APPARENCE

> ✅ **Les améliorations prioritaires ont été appliquées le 17 Mai 2026.**

## 1. États `:focus` manquants sur éléments interactifs

**Problème général :** La plupart des fichiers retirent `outline: none` sans fournir d'alternative visible. Aucun fichier n'utilise `:focus-visible`.

| Fichier | Éléments sans focus |
|---------|---------------------|
| `catalogue.css` | `.action-btn`, `.btn-add-skill`, `.tab-button` |
| `catalogue_admin.css` | `.filter-tab`, `.skill-action-btn`, `.modal-close` |
| `profile.css` | `.quick-button` (pas de `:active` ni `:focus`) |
| `marketplace.css` | `.filter-btn.active`, `.btn-help` (pas de `:focus`) |
| `parametres.css` | `.toggle-switch`, `.select-input`, `.btn-danger` |
| `notification.css` | `.btn-close`, `.pref-item` |
| `mes_aides.css` | `.tab-btn`, `.btn-continue`, `.btn-secondary` |
| `mes_competances.css` | `.skill-card` (aucun hover state du tout), `.btn-declare` |
| `mes_badges.css` | `.btn-see-more`, `.badge-item.has` |
| `mes_badges_mentor.css` | `.filter-chip`, `.badge-card` |
| `classement.css` | `.tab-btn`, table rows |
| `passeport_pdf.css` | `.btn-download` |
| `mes_demandes.css` | `.modal-close` |
| `mentor_apply.css` | `.skill-pill`, `.btn-apply`, `textarea` |
| `dashboard.css` | `.dropdown-item`, `.action-icon`, `.passport-item` |

**Fix recommandé :**
```css
.element:focus-visible {
  outline: 2px solid var(--blue-500);
  outline-offset: 2px;
}
```

> ✅ **Implémenté globalement dans `dashboard.css`** via `:focus-visible` avec `outline: 2px solid var(--blue-500)` et `button:focus:not(:focus-visible)` pour supprimer l'outline uniquement sur clic souris.

---

## 2. Problèmes de Contraste (Accessibilité WCAG AA)

| Élément | Couleurs | Ratio | Statut |
|---------|----------|-------|--------|
| `.notif-time` | `var(--gray-400)` (#94A3B8) sur blanc | ~3.3:1 | ❌ Échec AA |
| `.tag-warning` | `#FED7AA` bg + `#92400E` text | ~4.2:1 | ❌ Échec AA petit texte |
| `.tag-danger` | `#FECACA` bg + `#7F1D1D` text | ~4.0:1 | ❌ Échec AA petit texte |
| `.badge` (catalogue) | `#fef3c7` bg + `#92400e` text | ~4.2:1 | ❌ Échec AA |
| `.mini-role` | `.73rem` font | ~11.7px | ❌ Trop petit (<12px recommandé) |
| `.badge`, `.task-priority` | `.7rem` font | ~11.2px | ❌ Trop petit |
| `.task-time` | `.7rem` font | ~11.2px | ❌ Trop petit |

---

## 3. Tailles de police trop petites

| Classe | Taille | Px équiv. | Recommandation |
|--------|--------|-----------|----------------|
| `.mini-role` | `.73rem` | ~11.7px | Min 12px (`.75rem`) |
| `.badge` (général) | `.7rem` | ~11.2px | Min 11px, idéal 12px |
| `.task-priority` | `.7rem` | ~11.2px | Min 12px |
| `.task-time` | `.7rem` | ~11.2px | Min 12px |
| `.btn-small` | `.7rem` | ~11.2px | Min 12px |
| `.alert-time` | `.7rem` | ~11.2px | Min 12px |
| `.stat-desc` | `.75rem` | ~12px | Acceptable mais limite |
| `.badge-date` | `.72rem` | ~11.5px | Min 12px |

> ✅ **Corrigé dans `dashboard.css` et `tableau_de_bord.css`** — 12 classes passées à minimum `.75rem` (`.mini-role`, `.nav-badge`, `.passport-item-desc`, `.passport-level`, `.alert-time`, `.btn-small`, `.task-time`, `.activity-user`, `.activity-time`, `.accounts-page .badge`, `.badge`, `.task-priority`).

---

## 4. Breakpoints Responsive Manquants

| Fichier | Breakpoints existants | Manquants |
|---------|----------------------|-----------|
| `legal.css` | Aucun | Tous (mobile, tablet, desktop) |
| `mentor_apply.css` | 1080px | 768px, 640px, 480px |
| `recherche.css` | 1100px, 768px | 480px |
| `classement.css` | 1100px, 768px | 480px (tableau déborde) |
| `mes_aides.css` | 1080px, 640px | 480px |
| `mes_badges_mentor.css` | 1180px, 780px | 480px |
| `mes_demandes.css` | Aucun (modal) | Breakpoints pour le modal |
| `catalogue_admin.css` | 768px | 480px, 1024px |
| `nouvelle_demande.css` | 1080px, 680px | 480px |

---

## 5. Améliorations Visuelles Suggerées

### A. Ajouter une transition de page
Actuellement, la navigation entre pages est instantanée (pas de transition). Une légère fade-in sur `main.content-area` améliorerait la fluidité :
```css
.content-area { animation: fadeIn 0.2s ease; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
```

### B. États de chargement (skeleton screens)
Beaucoup de pages ont des données statiques qui seront un jour dynamiques. Ajouter des états de chargement (skeleton cards) améliorerait l'UX perçue.

### C. États vides (empty states)
Quand une liste est vide (pas de demandes, pas de badges), la page devrait montrer un message + CTA plutôt qu'une page blanche. `mes_demandes.js` définit `showEmpty()` mais ne l'appelle jamais.

### D. Icônes cohérentes
Certaines pages utilisent des emojis (✅, ✨, ⭐) dans les boutons/toasts au lieu des SVG stroke utilisés dans le sidebar et topbar. Standardiser en SVG.

### E. Messages "Fonctionnalité à venir"
17 endroits différents montrent un toast "Fonctionnalité à venir". Envisager une page "Coming Soon" ou un modal centralisé plutôt que des messages dispersés.

### F. `alert()` remplacé par modals/toasts
`catalogue.js` (lignes 68, 70) utilise `alert()` bloquant avec texte en anglais. Remplacer par le système de toast ou un modal de confirmation.

> ✅ **Corrigé** — `alert()` remplacé par `showToast()` avec texte en français.
