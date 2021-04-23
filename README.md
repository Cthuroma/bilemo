# Bilemo

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/94bd9b4740834668abb2b2f956b27457)](https://app.codacy.com/gh/Cthuroma/bilemo?utm_source=github.com&utm_medium=referral&utm_content=Cthuroma/bilemo&utm_campaign=Badge_Grade_Settings)

## Dependencies

Dependency  | Version
------------- | -------------
PHP  | 7.3
Composer  | 1.8

## Getting started

First clone the repository
```git
git clone https://github.com/Cthuroma/bilemo.git
```

Install php dependencies using composer.
```bash
composer install
```

And then create a ".env.local" using the example ".env" file and override the DATABASE_URL variable.
(The default DB Schema name used in test in bilemo)

## Setting the data up

You can use the follwing command to create a db schema following what you put in the DATABASE_URL variable.
```bash
bin/console doctrine:schema:create
```

You can use the migrations to set the database up.
```bash
bin/console doctrine:migrations:migrate
```

After that use the fixtures to get a default set of data.
```bash
bin/console doctrine:fixtures:load
```

## Testing

You can execute the tests by using the following command.
```bash
bin/phpunit
```

If you put bilemo as the schema name in your DATABASE_URL variable in the .env.local file then you can make sure you have the fixated data by running this :
```bash
composer reset
```

## Docs

You can find PUML and PNG files of various diagrams in the /docs/uml directory.
