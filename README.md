# oauth 2.0 API

- Symfony
- league/oauth2-server

# Features

- RESTful API
- oauth 2.0 
- PHPUnit test
- SwaggerUI documentation

# Installation

1. Install project
```sh
    git clone https://github.com/a000011/symfony-API-oauth2.0.git
```
2.  Add .env file
```env
    APP_ENV=dev

    DATABASE_URL="mysql://user:password@127.0.0.1:3306/DB_name?serverVersion=5.7"
```
3. Install all packages
```sh
    composer i --dev
```
4. generate public and private key
```sh
    cd var/oauth
    openssl genrsa -out private.key 2048
    openssl rsa -in private.key -pubout -out public.key
    chmod private.key 600
```
5. Add keys pathes to .env <br>
    For example:
```env
    OAUTH2_PUBLIC_KEY="path/to/public.key"
    OAUTH2_PRIVATE_KEY="path/to/private.key"
```

6. Update the database
```sh
    php bin/console doctrine:schema:update --force
```

7. If you want you can add in your database test data by running the migration
```sh
    php bin/console doctrine:migrations:migrate 
```
8. Create client
```sh
    php bin/console trikoder:oauth2:create-client 
```

# Docs

    You can find documentation on /api/doc or /api/doc.json endpoints

