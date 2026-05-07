# Navigation Structure Diagram

## 1. formateur/ Folder Navigation

```
┌─────────────────────────────────────────────────┐
│  formateur/ Section - Teacher/Trainer Pages     │
└─────────────────────────────────────────────────┘

                    ┌─────────────────────┐
                    │ tableu_de_bord.html │ (Dashboard)
                    │ 🔴 Issues: 2 broken │
                    │ links (#validation, │
                    │ #statistics)        │
                    └────────────┬────────┘
                                 │
              ┌──────────────────┼──────────────────┐
              │                  │                  │
         ✅ Works          ❌ BROKEN           ❌ BROKEN
              ↓                  ↓                  ↓
    ┌──────────────────┐ ┌─────────────────┐ ┌─────────────┐
    │validation_demande│ │  statistique    │ │  catalogue  │
    │.html (nav only)  │ │.html (self nav) │ │.html (nav)  │
    │ 🔴 Issues: 2     │ │ 🔴 Issues: 1    │ │✅ Working   │
    │ broken links     │ │ broken link     │ │             │
    │ (#statistics,   │ │ (#catalog)      │ │             │
    │ #catalog)       │ │                 │ │             │
    └──────────────────┘ └─────────────────┘ └─────────────┘

All pages have internal navigation menu linking to each other.
Pages in this folder: 4 total
```

## 2. pages_stagiere/ Folder Navigation

```
┌──────────────────────────────────────────────────────────┐
│ pages_stagiere/ Section - Intern/Student Pages (10 pages)│
└──────────────────────────────────────────────────────────┘

                    ┌─────────────────┐
                    │  dashboard.html │
                    │  ✅ Working     │
                    └────────┬────────┘
                             │
     ┌───────────────────────┼───────────────────────┐
     │                       │                       │
     ↓                       ↓                       ↓
┌──────────────┐      ┌─────────────┐       ┌─────────────┐
│mes_demandes  │      │ marketplace │       │ mes_badges  │
│.html         │      │.html        │       │.html        │
│🔴 Missing JS │      │✅ Working   │       │✅ Working   │
│(mes_demandes │      │             │       │             │
│.js)          │      │             │       │             │
└──────────────┘      └─────────────┘       └─────────────┘
     │                       │                       │
     │                       │                       │
     └───────────────────────┼───────────────────────┘
                             │
              ┌──────────────┴──────────────┐
              │                             │
              ↓                             ↓
      ┌──────────────┐            ┌──────────────────┐
      │ notification │            │  parametres      │
      │.html         │            │.html             │
      │✅ Working    │            │✅ Working        │
      │              │            │                  │
      └──────────────┘            └──────────────────┘
              │                             │
              │                             │
              └──────────┬──────────────────┘
                         │
                  ┌──────┴──────┐
                  │             │
                  ↓             ↓
           ┌────────────┐  ┌─────────────┐
           │ profile    │  │mentor_apply │
           │.html       │  │.html        │
           │✅ Working  │  │✅ Working   │
           │            │  │             │
           └────────────┘  └─────────────┘
                  │             │
                  │             │
                  └──────┬──────┘
                         │
                         ↓
                ┌─────────────────┐
                │ passeport_pdf   │
                │.html            │
                │✅ Working       │
                │                 │
                └─────────────────┘

Plus: login.html (entry point)

All pages navigate to each other via sidebar menu.
Common navigation pattern: dashboard → mes_demandes, marketplace, mes_badges, mentor_apply
```

## 3. pages_mentor/ Folder Navigation

```
┌──────────────────────────────────────────────────┐
│ pages_mentor/ Section - Mentor Pages (3 pages)    │
└──────────────────────────────────────────────────┘

            ┌─────────────────┐
            │  dashboard.html │
            │  ✅ Working     │
            └────────┬────────┘
                     │
      ┌──────────────┼──────────────┐
      │              │              │
      ↓              ↓              ↓
┌──────────────┐ ┌────────────┐ ┌─────────────┐
│ mes_demandes │ │marketplace │ │ mes_badges  │
│.html         │ │.html       │ │.html        │
│✅ Working    │ │✅ Working  │ │✅ Working   │
│ (but refs    │ │            │ │(uses        │
│ mis-spelled  │ │            │ │mes_badges_  │
│ file)        │ │            │ │mentor.css/js)
└──────────────┘ └────────────┘ └─────────────┘
      │              │              │
      │              │              │
      └──────────────┼──────────────┘
                     │
                     ↓
          ┌────────────────────┐
          │ mes_competance.html│
          │ ⚠️ SPELLING ISSUE  │
          │                    │
          │ File is actually:  │
          │ mes_competances.   │
          │ html (with 's')    │
          │ in pages_stagiere/ │
          │                    │
          │ Cross-folder ref!  │
          └────────────────────┘

All pages link to mes_competance.html (misspelled)
Actual file: pages_stagiere/mes_competances.html
```

## 4. Complete Navigation Map

```
START (login.html - pages_stagiere/)
  │
  ├─ TRAINER SECTION (formateur/)
  │   ├─ tableu_de_bord.html ❌ BROKEN LINKS
  │   ├─ validation_demande.html ❌ BROKEN LINKS  
  │   ├─ statistique.html ❌ BROKEN LINK
  │   └─ catalogue.html ✅ WORKING
  │
  ├─ INTERN SECTION (pages_stagiere/)
  │   ├─ dashboard.html ✅
  │   ├─ mes_demandes.html ❌ MISSING JS
  │   ├─ marketplace.html ✅
  │   ├─ mes_badges.html ✅
  │   ├─ mentor_apply.html ✅
  │   ├─ notification.html ✅
  │   ├─ parametres.html ✅
  │   ├─ profile.html ✅
  │   ├─ passeport_pdf.html ✅
  │   └─ mes_competances.html ✅
  │
  └─ MENTOR SECTION (pages_mentor/)
      ├─ dashboard.html ✅
      ├─ mes_demandes.html ✅
      ├─ marketplace.html ✅
      ├─ mes_badges.html ✅
      └─ mes_competance.html ⚠️ MISSPELLED (cross-folder ref)
         (Actually: pages_stagiere/mes_competances.html)
```

## 5. Asset Dependencies Graph

```
┌─────────────────────────────────────────────┐
│          SHARED ASSETS (Base Layer)         │
├─────────────────────────────────────────────┤
│                                             │
│  dashboard.css ────→ Used by 13 pages      │
│  dashboard.js ─────→ Used by 11 pages      │
│                                             │
│  Google Fonts (external) ───→ All pages    │
│                                             │
└─────────────────────────────────────────────┘
              │
              ├──────────────────────────────┐
              │                              │
              ↓                              ↓
    ┌──────────────────┐         ┌──────────────────┐
    │ PAGE-SPECIFIC    │         │ PAGE-SPECIFIC    │
    │ CSS FILES        │         │ JS FILES         │
    ├──────────────────┤         ├──────────────────┤
    │ catalogue.css    │         │ catalogue.js     │
    │ login.css        │         │ login.js         │
    │ marketplace.css  │         │ marketplace.js   │
    │ mentor_apply.css │         │ mentor_apply.js  │
    │ mes_aides.css    │         │ mes_aides.js     │
    │ mes_badges.css   │         │ mes_badges.js    │
    │ mes_badges_      │         │ mes_badges_      │
    │ mentor.css       │         │ mentor.js        │
    │ mes_competances  │         │ mes_competances  │
    │ .css             │         │ .js              │
    │ mes_demandes.css │         │ ❌ mes_demandes  │
    │ notification.css │         │ .js (MISSING)    │
    │ parametres.css   │         │ notification.js  │
    │ passeport_pdf.   │         │ parametres.js    │
    │ css              │         │ passeport_pdf.js │
    │ profile.css      │         │ profile.js       │
    │ statistique.css  │         │ tableu_de_bord.js│
    │ tableu_de_bord.  │         │                  │
    │ css              │         │                  │
    │ validation_      │         │                  │
    │ demande.css      │         │                  │
    │                  │         │                  │
    └──────────────────┘         └──────────────────┘
```

## 6. Issues Heat Map

```
┌────────────────────────────────────────────┐
│            SEVERITY BY SECTION             │
├────────────────────────────────────────────┤
│                                            │
│  formateur/               🔴🔴🔴 CRITICAL  │
│  └─ 5 broken links                         │
│                                            │
│  pages_stagiere/          🟡 MODERATE      │
│  └─ 1 missing JS file                      │
│                                            │
│  pages_mentor/            🟠 LOW-MODERATE  │
│  └─ 1 misspelled file ref (still works)    │
│                                            │
└────────────────────────────────────────────┘
```

---

**Key Findings:**
1. ✅ All CSS files are accessible and properly linked
2. ❌ 5 broken navigation links in formateur/ folder (using # instead of file names)
3. ❌ 1 missing JS file reference (mes_demandes.js)
4. ⚠️ 1 misspelled file reference (mes_competance vs mes_competances)
5. ✅ 11 pages properly load dashboard.js and dashboard.css
