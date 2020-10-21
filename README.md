# Welcome to Vesoul-edition

## Installation notes  

- Download or clone the repository. 
- Run "composer install" to install dependencies.
- Run "yarn install" and "yarn encore dev" to install font dependencies.
- Edit ".env" file to access to the database. 
- Create the database with "php bin/console doctrine:database:create"
- Execute migration with "php bin/console doctrine:migrations:migrate"
- Fill database's tables "php bin/console doctrine:fixtures:load"