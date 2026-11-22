# SymSvel Countries

Portage du référentiel **249 pays et territoires** de XCore.

Module prévu comme **natif obligatoire** de la distribution, installé avec
Languages, quelle que soit la configuration linguistique. L'accès à
l'administration des pays est conditionné par le paramètre Settings
`languages.multilingual` ; l'accès aux données App/Front ne l'est pas.

**État : développement sur `dev`, PR draft.** Le module n'est pas encore
inscrit au manifeste natif : recette, versions de migrations, intégration
multi-SGBD, CI et CT XT restent à vérifier avant publication.

Voir `docs/dev/index.md` et `docs/admin/index.md`.

## Intégration continue GitHub-hosted (22/11/2026)

Le workflow actif `.github/workflows/qa.yml` s'exécute désormais sur **GitHub-hosted Ubuntu 24.04**, sans runner Docker local. Les tests Composer et PHP 8.4 sont conservés. L'ancien `qa.yml.stub`, lorsqu'il existe, est une archive inactive.

Les runners auto-hébergés restent réservés aux dépôts privés `symsvel-packages` et `symsvel-recipes`. Les crash-tests locaux restent requis indépendamment de la CI.

## Licence MIT et contributions

Ce dépôt est distribué sous [licence MIT](LICENSE). Le code peut être utilisé, modifié et redistribué, y compris dans des projets commerciaux ou propriétaires, dans les conditions de cette licence (notamment la conservation de la notice de copyright et de licence).

**Contribuer :** créer un fork, puis proposer une pull request vers `dev` lorsque cette branche existe, sinon vers la branche par défaut. Expliquer la modification et fournir les tests et la documentation concernés. Les mainteneurs conservent la maîtrise des revues, des validations (CI et crash-tests), de l'intégration et des releases officielles. Une PR ne garantit pas son acceptation.

**Versions officielles :** seules les versions diffusées par l'organisation [Symsvel-Framework](https://github.com/Symsvel-Framework) sont officielles. Les forks sont libres sous MIT, mais ne doivent pas être présentés comme officiellement approuvés par les mainteneurs. Cette distinction n'ajoute aucune restriction aux droits accordés par la licence MIT sur le code.
