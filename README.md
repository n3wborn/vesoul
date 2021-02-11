# Vesoul

Vesoul is a Symfony project built to replace an old e-commerce site owned by a
French book label called [Vesoul-Edition](https://vesouledition.fr/).


## Install

**Download or clone the repository**

```bash
git clone https://github.com/n3wborn/vesoul.git
```


**Get in the git repo and install dependencies**

```bash
cd vesoul && composer install && yarn install
```


**Note**: If you're a Vagrant (or another virtal environment) user, consider trying
**--no-bin-links** yarn option if problems were encountered during install.


Edit **.env** (or better, create a .env.local) file to update variables needed
to access the dbms.

**Note**:

You can follow the next step to build the project but there's a more convenient way.
Check out [prepare section](https://github.com/n3wborn/vesoul/blob/main/README.md#prepare)


**Create the database**

```bash
php bin/console doctrine:database:create
```


**Prepare to migrate database**

```bash
php bin/console make:migration
```


**Migrate**

```bash
php bin/console doctrine:migrations:migrate
```


**Next, if you want fixtures**

```bash
php bin/console doctrine:fixtures:load
```


**Let Webpack build the assets**

```bash
yarn build
```


**During development, keep watching your files and compile when needed with**

```bash
yarn run watch
```


## Prepare

If you look at composer.json you'll see 2 scripts, prepare and prepare-test.
Thanks to this, it will be easier to prepare dev and test environment.

**prepare dev environment** :

```bash
docker-compose exec sh -c "cd vesoul; composer prepare"
```


**prepare test environment** :

```bash
docker-compose exec sh -c "cd vesoul; composer prepare-test"
```


Of course, this can be done directly from inside the www container too.
You just have to `docker-compose exec www bash` and you're in. `cd vesoul` to be
into the correct folder and now you can build the environment with just
`composer prepare` or `composer prepare-test` scripts.
Now you're inside the www container, if you want to exit you just have `exit`
or `ctrl+d` and you're back into your preferred OS.

**Each command will do everything needed from creating the database to making
fixtures, so stop wasting your time, now you know what to do**.


## Conclusion

Now, you're ready to go. Check user fixtures if you want to log in (as a user
or as the administrator). If you're not using fixtures, you must create an
administrator (ie: ['ROLE_ADMIN']) by hand and insert a bcrypt hash for it's
password. You can get one using php from cli with :

```bash
php -r "echo password_hash('ThePassword', PASSWORD_BCRYPT) . PHP_EOL;"
$2y$10$SzgYG0QZh8TcW851Q.ADru.lOtngwYGsnx.crY4ls64ILP2qyY0oK
```
