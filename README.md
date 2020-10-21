# Welcome to Vesoul-edition

## Installation notes  

- Download or clone the repository. 
- Run ```composer install``` to install dependencies.
- Run ```yarn install``` to install front-end dependencies. Vagrant user, you should check ```--no-bin-links``` option.
- Edit ```.env``` file to update variables needed to access the database.
- Create the database with ```php bin/console doctrine:database:create```
- Execute database migration with ```php bin/console doctrine:migrations:migrate```
- Fill database's tables ```php bin/console doctrine:fixtures:load```
- During development, let yarn keep watching your files with ```yarn run watch```