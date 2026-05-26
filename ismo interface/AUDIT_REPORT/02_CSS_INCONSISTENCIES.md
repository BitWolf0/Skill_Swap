# INCOHÉRENCES CSS RESTANTES

## 1. Couleurs hardcodées encore présentes

### `assets/css/catalogue.css`
- Ce fichier reste le principal point de fuite.
- Il contient encore plusieurs couleurs hardcodées dans les onglets, les cartes de compétences et la section stats.
- À migrer vers les variables définies dans `dashboard.css`.

### `assets/css/marketplace.css`
- Les badges de langage utilisent encore des couleurs dédiées.
- Il faut soit ajouter des variables de thème explicites, soit les normaliser avec une palette commune.

## 2. Points de cohérence à surveiller
- Les styles de rareté/gradients dans `mes_badges_mentor.css` restent très spécifiques.
- Les doublons de classes entre fichiers ne sont pas bloquants tant qu’ils sont volontairement locaux à chaque page.

## 3. Recommandation
- Garder `dashboard.css` comme source de vérité pour les tokens.
- N’ajouter de nouvelles couleurs hardcodées qu’en dernier recours.
