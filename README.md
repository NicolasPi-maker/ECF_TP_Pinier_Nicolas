
# ECF - Pinier Nicolas - JustSport

Permissions manager application for franchise and structure for our brand.

**Visit our website** [https://www.justsport.fr]([https://www.justsport.fr)

## Getting Started localhost

First, install composer

Open [https://getcomposer.org/download/](https://getcomposer.org/download/) to install.

### Set you Symfony project in your project folder

```bash
$ composer create-project symfony/skeleton your_project_name
```
Open [https://www.apachefriends.org/fr/index.html](https://www.apachefriends.org/fr/index.html) to Install xampp

Set `Apache` and `MySQL` to start on xampp controller pannel.

Open [http://localhost](http://localhost:3000) with your browser to see the result.

## Heroku deployment

-  First step, you need to have an account on Heroku [https://id.heroku.com/login](https://id.heroku.com/login)

-  Download and install [**Heroku CLI**](https://devcenter.heroku.com/articles/heroku-cli) on your device.

-  Init project on your git repository

```bash
$ heroku login
$ heroku create
$ echo 'web: heroku-php-apache2 public/' > Procfile
```

### Database configuration

Our project use **JawsDB MySQL** heroku add-on for database with MariaDB

```bash
 $ heroku addons:create jawsdb:kitefin
 ```

**Take care**, the main difference between this add-on and the classic command for PostgreSql is the database url configuration.

1. Get your generated database url from JawsDB

```bash
$ heroku config:get JAWSDB_URL
 ```

2. Manually set config for your database url project depends of your generated url

```bash
$ heroku config:set DATABASE_URL=
mysql://i4g30du3z5m4uzjr:t2mwnao4232duo8u@r98du2bxwqkq3shg.cbetxkdyxstsb.us-east-1.rds.amazonaws.com:3306/ezc80oszha8ueviu
 ```

3. During project compilation we want to import our database migration. Then, we need to add a custom script to composer.json file **"scripts"** part

```php
"compile" :  [
    "php bin/console doctrine:migration:migrate"
]
 ```
4. Generate htaccess file in public folder

```bash
$ composer require symfony/apache-pack
 ```

### Environment Variables

To deploy this project, you will need to switch the following environment variables to your .env file

`APP_ENV=prod`

or on shell :

```bash
$ heroku config:set APP_ENV=prod
```

### Deployment

Push your latest version on your repository
 ```bash
$ git add .
$ git commit -m "add config heroku"
$ git push
```

**Let's go !**

```bash
$ git push heroku master
```

## Add admin user in local database

You will need to install Symfony maker bundle

```bash
$ composer require --dev symfony/maker-bundle
```
Then, in your .env file you should config your database url

DATABASE_URL="postgresql://user@127.0.0.1:3306/database_name?serverVersion=14&charset=utf8"

### Create your user entity

```bash
$ symfony console make:User
```

On xampp controller pannel click on MySQL `admin` button to get access to PhpMyAdmin.

That will open your database controller their [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/)

In your database insert the following SQL command :

```sql
INSERT INTO user (email, roles, password, token)
VALUES (
    'admin@email.com',
    "['ROLE_ADMIN']", 
    '$2a$12$XSxLs43czhAvDGW85xE3Fud.iFuusX2FdARoI/M8Zh2rkzsjxt2b2', 
    'c91a53ca-e50c-4e30-a70f-41ba1facf6d6'
 );
```

## Add admin user in prod database

First step, to get access to your database, install [HeidiSQL](https://www.heidisql.com/) and open it.

fill needed informations (**find on** https://mysql.jawsdb.com/resource/dashboard) :
- Host
- Username
- password
- Port
- Database name

Then, in user table insert corresponding information for admin user like in local.