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

## Distribution stable v1.0.0 (20/11/2026)

Le manifeste Composer du tag stable déclare exclusivement des contraintes `^1.0` pour ses dépendances SymSvel : `symsvel-framework/module-languages`, `symsvel-framework/module-settings`, `symsvel-framework/module-acls`. Les références `dev-dev` restent réservées à la branche `dev` et à l'installation de maintenance avec `--dev`. Le tag `v1.0.0` n'est à publier **qu'après** le tag stable de chaque dépendance, son indexation dans Satis et la réussite des contrôles CI ; aucune release n'est produite par ce commit.
