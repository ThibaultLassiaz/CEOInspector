# CEO Inspector

[![PHP Version](https://img.shields.io/badge/PHP-8.1.5-green.svg)](https://www.php.net/releases/8.1.5.php)
[![PHPUnit Version](https://img.shields.io/badge/PHPUnit-9.6.15-green.svg)](https://phpunit.de/)
[![Symfony Version](https://img.shields.io/badge/Symfony-6.2.14-blue.svg)](https://symfony.com/releases/6.0.20)
[![Symfony CLI Version](https://img.shields.io/badge/SymfonyCLI-5.4.8-blue.svg)](https://symfony.com/releases/6.0.20)

## The Principle

The main purpose of this application is to provide the first and last names of company owners by providing the company name and zip code.

We're using this [French government open API](https://api.gouv.fr/les-api/api-recherche-entreprises) to do it

## How to use

### Create the database
```Bash
php bin/console doctrine:migrations:migrate
```

### Launch the web server
```Bash
symfony server:start
```
or host it on you domain, it comes with an Apache dependency and preconfigured [.htaccess](https://github.com/ThibaultLassiaz/CEOInspector/blob/master/public/.htaccess)

Once the server is started you can check on your [localhost](http://127.0.0.1:8000) or on your web domain ex : https://my-domain.com

### Start the workers

In order to start the workers you'll have to start both file and company queues using [Symfony Messenger Bundler](https://symfony.com/doc/current/messenger.html)


Start file worker to consume file messages
```Bash
php bin/console messenger:consume --time-limit=55 file
```

Start company worker to consume file messages

```Bash
php bin/console messenger:consume --time-limit=55 company
```

Note that the government API can handle 7 requests per seconde maximum, so I recommend to run maximum 5 to 7 worker max at the same time 

### Input format

The application currently only support xlsx files, as it was created to handle this format and specified columns

You can find a file example [here](https://github.com/ThibaultLassiaz/CEOInspector/blob/master/src/Dummy/test_file.xlsx)


## Dev

### Static analysis

```Bash
composer run phpstan
```

### Linter

```Bash
composer run linter
```

### Unit tests

```Bash
composer run unit
```