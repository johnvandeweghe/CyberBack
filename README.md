# CyberBack - CyberWars Backend

This is the backend codebase for the CyberWars game.

For the front end of the game check out [CyberFront](https://github.com/JonHarder/CyberFront).

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

- Composer 
- PHP 7.1+
- Some PHP extensions you probably have (composer will tell you if you don't when installing)
  - php-curl
  - php-mbstring
  - php-zip
  - php-xml

### Installing

Run the following to initialize the project:
```bash
git clone git@github.com:johnvandeweghe/CyberBack.git
cd CyberBack
composer install
```

Then for development set the config in .env.
Example .env:
```ini
APP_ENV=dev
APP_SECRET=eb106562e67ed9f80b2c2ed8e2d9a5dd
DATABASE_URL=sqlite:///tmp/db_name
PUSHER_APP_ID=
PUSHER_KEY=
PUSHER_SECRET=
```

Then you can setup your database by running the migrations with the following:
```bash
./bin/console doctrine:migrations:migrate
```

## Start a test server
To start a test server to hit with API requests run the following:
```bash
php -S localhost:8080 -t public/
```

## Running the tests
Simply execute the following to run all unit tests:
```bash
./bin/phpunit
```
Note: Test server does not need to be running to run the tests.

## Deployment

Setup is similar to development, but .env should instead be set as environment variables with production values.


## Contributing
Contributions are welcome, just drop a Pull Request.

## Versioning

We use [SemVer](http://semver.org/) for versioning. This project is currently under version 1.0 and is subject to change drastically still (as is allowed in SemVer).

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
