# Reserves ton coach

## Commandes utiles

| Description          | Commande                                     |
| :------------------- | -------------------------------------------- | --- |
| lancer les fixtures  | `php bin/console doctrine:fixtures:load`     |
| créer une entité     | `php bin/console make:entity Ticket `        |
| lancer le serveur    | `php bin/console serve `                     |
| créer un formulaire  | `php bin/console make:form createGroup `     |
| créer un controlleur | `php bin/console make:controller Navigation` | -   |
| créer une entité     | `php bin/console make:entity Ticket `        |
| lancer les fixtures  | `php bin/console doctrine:fixtures:load`     |
| créer une entité     | `php bin/console make:entity Ticket `        |
| lancer les fixtures  | `php bin/console doctrine:fixtures:load`     |
| créer une entité     | `php bin/console make:entity Ticket `        |
| lancer les fixtures  | `php bin/console doctrine:fixtures:load`     |
| créer une entité     | `php bin/console make:entity Ticket `        |
| lancer les fixtures  | `php bin/console doctrine:fixtures:load`     |
| créer une entité     | `php bin/console make:entity Ticket `        |
| lancer les fixtures  | `php bin/console doctrine:fixtures:load`     |
| créer une entité     | `php bin/console make:entity Ticket `        |

## Explications

- Quand nous créons un événement, un token est automatiquement généré. Celui-ci est compliqué et à destination du coach.
- Quand un group est crée, on genère un autre token à destination du groupe, afin que ceux-ci puissent accéder à la page de leur groupe, créer des tickets.

`vendor/bin/phpstan analyse -l 6 src`

1. composer install
2. symfony php bin/console doctrine:database:create
3. php bin/console doctrine:migrations:migrate
4. symfony serve
