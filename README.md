# SymSvel Countries

> [!IMPORTANT]
> **Vous souhaitez utiliser SymSvel ? Rendez-vous sur [SymSvel CLI](https://github.com/Symsvel-Framework/symsvel-cli).** Ce dépôt est un **composant du framework**, pas un point d'installation autonome. Création de projets, installation des modules et mises à jour passent par la CLI. Les éventuelles commandes Composer, npm ou Symfony décrites ci-dessous concernent le travail des mainteneurs, sauf indication explicite contraire.

> [!WARNING]
> **SymSvel est en cours de développement.** Les fonctionnalités et procédures peuvent évoluer ou présenter des anomalies. **Utilisation à vos risques et périls.**

Portage du référentiel **249 pays et territoires** de XCore.

Module prévu comme **natif obligatoire** de la distribution, installé avec
Languages, quelle que soit la configuration linguistique. L'accès à
l'administration des pays est conditionné par le paramètre Settings
`languages.multilingual` ; l'accès aux données App/Front ne l'est pas.

**Historique d'intégration :** la recette, les migrations, la compatibilité multi-SGBD et les crash-tests doivent être vérifiés pour chaque version publiée. Le statut d'une ancienne branche de développement n'est pas un indicateur de disponibilité de la release actuelle.

Voir `docs/dev/index.md` et `docs/admin/index.md`.

## Distribution stable v1.0.0 (20/11/2026)

Le manifeste Composer du tag stable déclare exclusivement des contraintes `^1.0` pour ses dépendances SymSvel : `symsvel-framework/module-languages`, `symsvel-framework/module-settings`, `symsvel-framework/module-acls`. Les références `dev-dev` restent réservées à la branche `dev` et à l'installation de maintenance avec `--dev`. Le tag `v1.0.0` n'est à publier **qu'après** le tag stable de chaque dépendance, son indexation dans Satis et la réussite des contrôles CI ; aucune release n'est produite par ce commit.

---

## Intégration continue GitHub-hosted (22/11/2026)

Le workflow actif `.github/workflows/qa.yml` s'exécute désormais sur **GitHub-hosted Ubuntu 24.04**, sans runner Docker local. Les tests Composer et PHP 8.4 sont conservés. L'ancien `qa.yml.stub`, lorsqu'il existe, est une archive inactive.

Les runners auto-hébergés restent réservés aux dépôts privés `symsvel-packages` et `symsvel-recipes`. Les crash-tests locaux restent requis indépendamment de la CI.

## Licence MIT et contributions

Ce dépôt est distribué sous [licence MIT](LICENSE). Le code peut être utilisé, modifié et redistribué, y compris dans des projets commerciaux ou propriétaires, dans les conditions de cette licence (notamment la conservation de la notice de copyright et de licence).

**Contribuer :** créer un fork, puis proposer une pull request vers `dev` lorsque cette branche existe, sinon vers la branche par défaut. Expliquer la modification et fournir les tests et la documentation concernés. Les mainteneurs conservent la maîtrise des revues, des validations (CI et crash-tests), de l'intégration et des releases officielles. Une PR ne garantit pas son acceptation.

**Versions officielles :** seules les versions diffusées par l'organisation [Symsvel-Framework](https://github.com/Symsvel-Framework) sont officielles. Les forks sont libres sous MIT, mais ne doivent pas être présentés comme officiellement approuvés par les mainteneurs. Cette distinction n'ajoute aucune restriction aux droits accordés par la licence MIT sur le code.

## Participer et soutenir le projet

SymSvel est un projet en cours de développement. Pour contribuer, proposer une amélioration ou échanger avec le mainteneur : **[xavier@xavier-tezza.fr](mailto:xavier@xavier-tezza.fr)**.

SymSvel vous est utile et vous souhaitez me remercier ? **Offrez-moi une bière ! 🍺** (lien PayPal à compléter avant l'ouverture publique).
