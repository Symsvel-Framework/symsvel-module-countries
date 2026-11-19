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

## Intégration et validations restantes (19/11/2026)

Countries est désormais déclaré **natif** dans
`symsvel-back/config/symsvel-native-modules.json`, immédiatement après
Languages. La recette Flex publiée dans `symsvel-recipes/main` déclare
`CountriesBundle` et importe le dossier réel
`back/src/Controller/`. Le fichier `recipe/recipe.json` reste son modèle
dans le module : c'est la recette du catalogue qui est consommée par Flex.

**La publication de la recipe ne valide pas son exécution.**
Avant de passer la PR Countries de draft à prête pour revue, vérifier sur
XT, en installation neuve `--dev` :
- la résolution Composer et l'application de la recipe (bundle + routes) ;
- l'ordre et l'exécution des migrations et des seeds des 249 pays ;
- les quatre moteurs PostgreSQL, MySQL, MariaDB et SQLite ;
- l'accès `/apiopen/countries` sans multilinguisme, les ACL et
  la visibilité conditionnelle du menu BO ;
- la navigation après activation du multilinguisme et les tests E2E.

La CI du module est verte au 19/11/2026, mais le crash-test utilisateur
et la validation du workflow distribué restent nécessaires. La PR
reste **draft** jusqu'à ces vérifications.
