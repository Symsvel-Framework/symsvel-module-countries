# Countries : fonctionnement (chantier draft)

Le module fournit le référentiel natif des 249 pays et territoires de XCore :
code ISO 3166 alpha-2, nom natif, devise, langues officielles et état actif.
Le seed est installé **même en site monolingue**. Sa migration de structure
utilise Doctrine Schema ; la migration suivante contient uniquement les
insertions de référence. Ces sources appartiennent à Countries, pas au Core.

Les noms affichés sont résolus dans la locale demandée par `Symfony Intl`,
avec repli sur le nom natif. Le module ne recopie pas les très nombreuses
traductions historiques de XCore : le contrat linguistique reste celui de
Languages et de `LanguageLocaleResolver`. Aucun téléchargement de données
n'est effectué à l'exécution. Les ISO et métadonnées de référence ne sont
pas modifiables depuis le BO.

## Interfaces

- `GET /apiopen/countries` (Front) et `GET /api/countries` (App) :
  pays actifs, y compris lorsque le multilinguisme est désactivé.
- `GET /apibo/countries` : liste complète, ACL `countries:list`.
- `PATCH /apibo/countries/{code}` : booléen `active` uniquement,
  ACL `countries:manage`.
- Les endpoints BO retournent 404 lorsque `languages.multilingual`
  n'est pas `1`. Ainsi, la sonde de navigation BO existante masque
  automatiquement Countries. Aucune condition d'installation ne dépend
  de la valeur de ce paramètre.
- L'interface BO utilise `DataTable`, `Pagination` et les jetons
  graphiques existants, sans copie de composants.

## Travaux d'intégration encore nécessaires

Avant activation dans le manifeste des modules natifs du Setup, valider :
la résolution Composer des dépendances privées, l'enregistrement du bundle et
des routes par la recipe Flex, la réservation des versions des migrations dans
Tooling, l'installation et les migrations sur PostgreSQL, MySQL, MariaDB et
SQLite, les contrats API/ACL, la navigation après changement du paramètre
multilingue et les tests E2E XT. Ne pas déclarer la release terminée avant
CI verte, documentation et crash-tests utilisateur.

Le statut reste **draft** tant que ces vérifications ne sont pas réalisées.

La recette `recipe/recipe.json` du package reflète le chemin de contrôleurs
réel et le `ref` du catalogue source de `symsvel-recipes`. Ce fichier reste
un modèle interne : seule la recette aplatie effectivement publiée par le
catalogue Flex est consommée par une installation.
