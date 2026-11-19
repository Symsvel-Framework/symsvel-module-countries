# Countries : administration (draft)

Countries sera installé automatiquement avec Languages lors de l'installation
native, y compris dans un projet monolingue. Sa présence en base n'implique
**pas** l'affichage du menu BO.

L'entrée **Pays** ne doit apparaître qu'avec
`languages.multilingual = 1` dans Settings, et si l'administrateur possède
l'ACL `countries:list`. Sans ce mode, le BO ne présente pas Countries.
La lecture publique des pays actifs continue de fonctionner.

Une fois visible, l'écran BO permet la recherche, le tri et la pagination
du référentiel, ainsi que l'activation/désactivation des pays sous
`countries:manage`. Les codes ISO, noms natifs, devises et langues
officielles sont non éditables. Les sauvegardes mettent à jour la ligne
sans recharger la page.

L'affichage immédiat du nouveau lien au changement du mode multilingue
et le crash-test XT doivent être vérifiés durant l'intégration.
