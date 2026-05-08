# ISMO-SkillSwap Design System
## Theme, Sidebar & Topbar Guidelines

---

## 📋 Table of Contents
1. [Design System Overview](#design-system-overview)
2. [Color Palette](#color-palette)
3. [Typography](#typography)
4. [Sidebar Structure](#sidebar-structure)
5. [Topbar Structure](#topbar-structure)
6. [Component Guidelines](#component-guidelines)
7. [Responsive Behavior](#responsive-behavior)

---

## 🎨 Design System Overview

**ISMO-SkillSwap** uses a modern, clean design system with:
- **Font Family**: Inter (primary), Fira Code (code blocks)
- **Base Colors**: Blue palette with supporting neutral grays
- **Design Tokens**: Predefined CSS variables for consistency
- **Layout System**: Sidebar + Topbar + Main Content
- **Spacing**: Based on 4px grid system
- **Border Radius**: Tiered system (sm: 8px, md: 12px, lg: 16px)

---

## 🎭 Color Palette

### Primary Color (Blue)
Used for active states, primary buttons, and brand elements.

```css
--blue-50:  #EFF6FF    /* Lightest - hover backgrounds */
--blue-100: #DBEAFE   /* Light backgrounds */
--blue-500: #3B82F6   /* Standard blue */
--blue-600: #2563EB   /* Primary brand color - MAIN */
--blue-700: #1D4ED8   /* Darker blue */
--blue-800: #1E40AF   /* Dark interactions */
--blue-900: #1E3A8A   /* Darkest - text */
```

### Accent Colors
```css
--orange-500:  #F97316   /* Badges & notifications */
--orange-600:  #EA580C   /* Hover state */
--orange-100:  #FFEDD5   /* Light backgrounds */

--green-500:   #22C55E   /* Success states */
--green-600:   #16A34A   /* Darker success */
--green-100:   #DCFCE7   /* Light backgrounds */

--red-500:     #EF4444   /* Error states */
--pink-500:    #EC4899   /* Secondary accent */
```

### Neutral Colors (Gray)
Used for text, borders, and backgrounds.

```css
--gray-50:     #F8FAFC   /* Page background */
--gray-100:    #F1F5F9   /* Hover backgrounds, section bg */
--gray-200:    #E2E8F0   /* Borders */
--gray-300:    #CBD5E1   /* Light dividers */
--gray-400:    #94A3B8   /* Icon colors, muted text */
--gray-500:    #64748B   /* Secondary text */
--gray-600:    #475569   /* Body text */
--gray-700:    #334155   /* Strong text */
--gray-800:    #1E293B   /* Heading text */
--gray-900:    #0F172A   /* Darkest text */
--white:       #FFFFFF   /* White */
```

---

## ✍️ Typography

### Font System
```css
font-family: 'Inter', system-ui, -apple-system, sans-serif;
```

### Weight Levels
- **Regular**: 400
- **Medium**: 500
- **Semibold**: 600
- **Bold**: 700
- **Extrabold**: 800

### Font Sizes (Scalable)
```css
1.05rem   /* Large brand text */
0.9rem    /* Body text & nav items */
0.85rem   /* Secondary text */
0.82rem   /* Small text */
0.73rem   /* Extra small text */
0.7rem    /* Badges & tags */
```

---

## 📐 Layout System

### Fixed Dimensions
```css
--sidebar-w: 248px      /* Sidebar width */
--topbar-h: 64px       /* Header height */
```

### Spacing Scale
```css
Gap/Padding: 2px, 4px, 8px, 10px, 12px, 14px, 16px, 20px, 24px
```

### Border Radius
```css
--radius-sm:   8px
--radius-md:   12px
--radius-lg:   16px
--radius-xl:   20px
--radius-full: 9999px (50%)
```

### Shadows
```css
--shadow-sm: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.05)
--shadow-md: 0 4px 16px rgba(0,0,0,.10), 0 2px 4px rgba(0,0,0,.06)
--shadow-lg: 0 10px 40px rgba(0,0,0,.14), 0 4px 8px rgba(0,0,0,.08)
```

### Transitions
```css
--transition: 180ms cubic-bezier(.4, 0, .2, 1)
```

---

## 🔲 Sidebar Structure

### Overview
- **Fixed Position**: Left side, full height (100vh)
- **Width**: 248px (responsive: collapses on mobile)
- **Z-index**: 100 (content), 95 (overlay)
- **Background**: White with subtle border
- **Sections**: Brand → Navigation → Footer

### HTML Structure
```html
<aside class="sidebar" id="sidebar" aria-label="Navigation principale">
  <!-- Brand Section -->
  <div class="sidebar-brand">
    <div class="brand-icon-wrap"><!-- SVG logo --></div>
    <span class="brand-text">ISMO<span>-SkillSwap</span></span>
  </div>

  <!-- Navigation Section -->
  <nav class="sidebar-nav" aria-label="Menu principal">
    <ul role="list">
      <li>
        <a href="page.html" class="nav-item" id="nav-id">
          <span class="nav-icon" aria-hidden="true"><!-- SVG --></span>
          <span class="nav-label">Label</span>
          <span class="nav-badge">3</span>  <!-- Optional badge -->
        </a>
      </li>
    </ul>
  </nav>

  <!-- Footer Section -->
  <div class="sidebar-footer">
    <div class="sidebar-user-mini">
      <div class="mini-avatar"><!-- SVG --></div>
      <div class="mini-info">
        <span class="mini-name">Name</span>
        <span class="mini-role">Role</span>
      </div>
    </div>
  </div>
</aside>

<!-- Sidebar Overlay (Mobile) -->
<div class="sidebar-overlay" id="sidebar-overlay" aria-hidden="true"></div>
```

### Sidebar Sections Detail

#### Brand Section
```css
.sidebar-brand {
  padding: 20px 20px 16px;
  border-bottom: 1px solid var(--gray-100);
  display: flex;
  align-items: center;
  gap: 10px;
}
```
- **Logo Icon**: 28×28px SVG, blue gradient background
- **Brand Text**: 1.05rem, weight 800, dark blue
- **Accent**: "SkillSwap" portion in primary blue

#### Navigation Items
```css
.nav-item {
  padding: 10px 12px;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--gray-500);
}

.nav-item:hover {
  background: var(--gray-100);
  color: var(--gray-700);
}

.nav-item.active {
  background: var(--blue-600);
  color: white;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.30);
}
```

**Features:**
- Icon (20×20px) + Label + Optional Badge
- Hover: Gray background, darker text
- Active: Blue background, white text, shadow
- Badge: Orange background, small white text (7px), rounded

#### Sidebar Footer
```css
.sidebar-footer {
  padding: 12px 16px;
  border-top: 1px solid var(--gray-100);
}
```
- **Avatar**: 32×32px circle, blue background
- **Name**: 0.82rem, weight 600, dark gray
- **Role**: 0.73rem, muted gray
- Used to show current user info (Stagiaire/Formateur)

---

## 🔝 Topbar Structure

### Overview
- **Fixed Position**: Sticky top (z-index: 90)
- **Height**: 64px
- **Width**: Full width minus sidebar (responsive: full on mobile)
- **Background**: White with subtle border & shadow
- **Layout**: Search (left) → Spacing → Icons & Profile (right)

### HTML Structure
```html
<header class="topbar" role="banner">
  <!-- Mobile Menu Button -->
  <button class="icon-btn btn-menu" id="btn-menu" aria-label="Ouvrir le menu">
    <!-- SVG: hamburger menu -->
  </button>

  <!-- Search Bar -->
  <div class="search-wrap">
    <span class="search-icon" aria-hidden="true"><!-- SVG: magnifier --></span>
    <input type="search" class="search-input" placeholder="Rechercher..." />
    <kbd class="search-kbd">⌘K</kbd>
  </div>

  <!-- Right Section -->
  <div class="topbar-right">
    <!-- Notification Bell -->
    <button class="icon-btn notif-btn" id="btn-notif" aria-label="Notifications">
      <!-- SVG: bell -->
      <span class="notif-dot" aria-label="Nouvelles notifications"></span>
    </button>

    <!-- Profile Button (opens dropdown) -->
    <button class="profile-btn" id="btn-profile" aria-label="Menu profil">
      <div class="profile-info">
        <span class="profile-name">Sophie Martin</span>
        <span class="profile-score">Score: 85</span>
      </div>
      <div class="profile-avatar"><!-- SVG: user --></div>
    </button>

    <!-- Profile Dropdown Menu -->
    <div class="profile-dropdown" id="profile-dropdown" role="menu" hidden>
      <!-- Header with avatar -->
      <div class="dropdown-header">
        <div class="dropdown-avatar"><!-- SVG --></div>
        <div class="dropdown-user-info">
          <span class="dropdown-name">Sophie Martin</span>
          <span class="dropdown-rep">Réputation: 85</span>
        </div>
      </div>

      <!-- Menu Items -->
      <ul class="dropdown-menu" role="none">
        <li role="none"><a href="profile.html" class="dropdown-item">Mon profil</a></li>
        <li role="none"><a href="mes_badges.html" class="dropdown-item">Mes badges</a></li>
        <li role="none"><a href="notification.html" class="dropdown-item">Notifications</a></li>
        <li role="none"><a href="parametres.html" class="dropdown-item">Paramètres</a></li>
      </ul>

      <!-- Divider & Logout -->
      <div class="dropdown-divider"></div>
      <a href="login.html" class="dropdown-item dropdown-logout">Déconnexion</a>
    </div>
  </div>
</header>
```

### Topbar Components Detail

#### Search Bar
```css
.search-wrap {
  flex: 1;
  max-width: 480px;
  position: relative;
  display: flex;
  align-items: center;
}

.search-input {
  padding: 9px 40px 9px 42px;
  border: 1.5px solid var(--gray-200);
  border-radius: 9999px;
  font-size: 0.9rem;
  background: var(--gray-50);
}

.search-input:focus {
  border-color: var(--blue-600);
  background: white;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}
```
- **Max Width**: 480px
- **Icon**: Left side (14px from edge), gray
- **Keyboard Hint**: Right side (⌘K)
- **Focus State**: Blue border, white background, blue shadow ring

#### Icon Buttons
```css
.icon-btn {
  width: 38px;
  height: 38px;
  border-radius: 9999px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--gray-500);
}

.icon-btn:hover {
  background: var(--gray-100);
  color: var(--gray-700);
}
```
- **Size**: 38×38px circles
- **Icons**: 20×20px centered SVG
- **Hover**: Gray background
- **Notification Dot**: Orange (8×8px) top-right with white border

#### Profile Button
```css
.profile-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 5px 8px 5px 14px;
  border-radius: 9999px;
  background: var(--gray-50);
  border: 1.5px solid var(--gray-200);
}

.profile-btn:hover {
  background: white;
  border-color: var(--blue-600);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
}

.profile-avatar {
  width: 34px;
  height: 34px;
  background: var(--blue-600);
  color: white;
  border-radius: 9999px;
  box-shadow: 0 2px 6px rgba(37, 99, 235, 0.35);
}
```
- **Profile Info**: Text right-aligned (name + score)
- **Avatar**: 34×34px circle, blue background
- **Hover**: White background, blue border, subtle blue shadow
- **Click**: Opens profile dropdown menu

#### Profile Dropdown Menu
```css
.profile-dropdown {
  position: absolute;
  top: calc(100% + 10px);
  right: 0;
  width: 240px;
  background: white;
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-lg);
  border: 1px solid var(--gray-200);
  animation: dropIn 0.18s cubic-bezier(0.4, 0, 0.2, 1);
}

.dropdown-header {
  background: var(--blue-600);
  color: white;
  padding: 20px 18px 16px;
}
```
- **Position**: Anchored to profile button, 10px gap
- **Width**: 240px
- **Animation**: Slide down + scale (0.97 → 1) in 180ms
- **Header**: Blue background with user avatar + info
- **Menu Items**: Light gray, hover: blue text
- **Divider**: Subtle gray line before logout

---

## 🧩 Component Guidelines

### Navigation Items Structure
**Current Sidebar Menu** (pages_stagiere):
1. ✅ Tableau de bord → `dashboard.html`
2. ✅ Mes Demandes → `mes_demandes.html` (with badge)
3. ✅ Mes Compétences → `mes_competances.html`
4. ✅ Passeport PDF → `passeport_pdf.html`
5. ✅ Devenir Mentor → `mentor_apply.html`

### Icon Guidelines
- **Size**: 20×20px for sidebar, topbar
- **Stroke Width**: 2 (usually)
- **Color**: Inherits from text color (changes on hover/active)
- **Format**: Inline SVG (not imports) for better performance
- **Fill**: "none", stroke-based (outline icons)

### Active State Styling
- **Sidebar**: Blue background + white text + shadow box
- **Topbar**: Current page title/section highlighted or navigation tabs
- Use `aria-current="page"` attribute for accessibility

### Badges
- **Background**: Orange (#F97316)
- **Text**: White, 0.7rem, weight 700
- **Padding**: 1px 7px
- **Border Radius**: Full circle (50%)
- **Line Height**: 1.6
- **Use Case**: Notification counts on nav items

---

## 📱 Responsive Behavior

### Desktop (>1024px)
- ✅ Sidebar visible (fixed left, 248px)
- ✅ Topbar full width minus sidebar
- ✅ Search bar visible at full width
- ✅ Mobile menu button hidden

### Tablet (768px - 1023px)
- ✅ Sidebar collapsible or narrower
- ✅ Topbar mobile menu button visible
- ✅ Search bar may be icon-only
- ✅ Overlay appears when sidebar opens

### Mobile (<768px)
- ✅ Sidebar hidden by default (off-screen left)
- ✅ Overlay shown when sidebar open
- ✅ Topbar full width with menu button
- ✅ Search icon-only (dropdown menu)
- ✅ Profile info simplified

### CSS Media Queries
```css
@media (max-width: 1024px) {
  /* Sidebar adjustments */
}

@media (max-width: 768px) {
  .btn-menu { display: flex; }
  .sidebar { transform: translateX(-100%); }
  .sidebar.show { transform: translateX(0); }
  .search-wrap { max-width: none; }
}
```

---

## 🔄 Transitions & Animations

### Standard Transition
```css
--transition: 180ms cubic-bezier(0.4, 0, 0.2, 1)
```
Applied to: hover states, color changes, background changes

### Profile Dropdown Animation
```css
@keyframes dropIn {
  from {
    opacity: 0;
    transform: translateY(-8px) scale(0.97);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}
```
Duration: 180ms, Timing: cubic-bezier

---

## ✅ Best Practices

### Do's ✓
- Use design tokens (CSS variables) consistently
- Maintain 4px grid alignment for spacing
- Always include `aria-*` attributes for accessibility
- Use SVG icons (inline) for quality at any size
- Apply transitions to interactive elements
- Keep sidebar & topbar consistent across all pages
- Test responsive behavior on mobile

### Don'ts ✗
- Don't hardcode colors (use CSS variables)
- Don't mix font families (Inter only for UI)
- Don't skip accessibility attributes
- Don't create custom buttons without hover states
- Don't change sidebar/topbar structure per page
- Don't use lorem ipsum for user-facing text
- Don't exceed color palette without team approval

---

## 📝 Common CSS Classes

### Sidebar
```css
.sidebar                   /* Main container */
.sidebar-brand            /* Logo section */
.sidebar-nav              /* Navigation list */
.nav-item                 /* Individual nav link */
.nav-item.active          /* Current page */
.nav-icon                 /* Icon wrapper */
.nav-label                /* Text label */
.nav-badge                /* Notification badge */
.sidebar-footer           /* User info section */
.sidebar-overlay          /* Mobile backdrop */
```

### Topbar
```css
.topbar                   /* Main header */
.btn-menu                 /* Mobile menu toggle */
.search-wrap              /* Search container */
.search-input             /* Input field */
.topbar-right             /* Actions section */
.icon-btn                 /* Icon buttons */
.notif-btn                /* Notifications button */
.notif-dot                /* Notification indicator */
.profile-btn              /* Profile button */
.profile-dropdown         /* Dropdown menu */
.dropdown-header          /* Menu header */
.dropdown-menu            /* Menu items list */
```

---

## 🎯 Implementation Checklist

When creating or updating pages:

- [ ] Copy sidebar structure from existing page
- [ ] Update navigation links to match current page
- [ ] Add `aria-current="page"` to active nav item
- [ ] Copy topbar structure unchanged
- [ ] Ensure main content has `margin-left: var(--sidebar-w)`
- [ ] Use design tokens for all colors
- [ ] Test responsive breakpoints
- [ ] Verify accessibility (ARIA labels, keyboard nav)
- [ ] Check console for CSS warnings
- [ ] Test on mobile device or browser DevTools

---

## 📖 Resources

- **CSS File**: `assets/css/dashboard.css` (main design tokens)
- **Font**: Inter (imported from Google Fonts)
- **Icons**: Inline SVG (24px viewBox)
- **Files Updated**: All files in `pages_stagiere/` folder
- **Last Updated**: May 2026
- **Theme Version**: 1.0

---

**Version**: 1.0 | **Last Updated**: May 8, 2026
