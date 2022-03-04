
## Mettre à jour les packages `composer update`
## Nettoyer le cache `php bin/console c:c`
## Migrations

Créer une BDD vierge

Modifier la chaine de connexion dans le fichier __.env__ pour cibler la bonne BDD

Exécuter la commande `php bin/console doctrine:migration:migrate` pour jouer les migrations sur la BDD

Lorsque les entités sont modifiées, `php bin/console doctrine:migration:diff` pour créer une migration à partir de ces changements

`php bin/console doctrine:migration:up-to-date` pour savoir si des migrations n'ont pas été jouées  
`php bin/console doctrine:migration:status` pour faire un point sur les migrations. Cela peut servir à vérifier si des migrations sont prêtes à être créées.