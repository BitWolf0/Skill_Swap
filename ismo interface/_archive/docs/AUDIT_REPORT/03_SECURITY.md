# PROBLÈMES DE SÉCURITÉ RESTANTS

## 1. Données encore injectées via `innerHTML`
- `assets/js/classement.js` reste construit avec `innerHTML` sur des données statiques.
- Le risque est faible aujourd’hui, mais le code deviendrait fragile si la source passe un jour par une API.

## 2. Validation uniquement côté client
- La connexion, l’inscription, les formulaires de demande et les réglages restent sans validation backend.
- Ce point est acceptable pour une maquette statique, mais il faudra le corriger dès qu’un serveur sera branché.

## 3. Pas de CSP
- Aucune `Content-Security-Policy` n’est déclarée dans les pages.
- À prévoir si le projet passe en production ou commence à recevoir des données dynamiques.
