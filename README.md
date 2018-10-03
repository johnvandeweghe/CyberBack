# CyberBack - CyberWars Backend

This is the backend codebase for the CyberWars game.

For the front end of the game check out [CyberFront](https://github.com/JonHarder/CyberFront).

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

- PHP 7.2+
- Composer https://getcomposer.org/download/
- Some PHP extensions you probably have (composer will tell you if you don't when installing)
  - php-curl
  - php-mbstring
  - php-zip
  - php-xml
  - php-sqlite3

#### Getting PHP 7.2 on ubuntu
Use ondrej's PPA:
```bash
sudo apt-get install python-software-properties
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt-get install -y php7.2 php-curl php-mbstring php-zip php-xml php-sqlite3
```

### Installing

Run the following to initialize the project:
```bash
git clone git@github.com:johnvandeweghe/CyberBack.git
cd CyberBack
composer install
```

Then for development set the config in .env. You can use .env.dist as a starter file.

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

To run the integration tests:
```bash
./bin/phpunit --testsuite=integration
```

## Deployment

Setup is similar to development, but .env should instead be set as environment variables with production values.

## Documentation

See /docs/api_spec_oas3.yml for a OpenAPI Spec 3 doc describing the public API for the backend. 

See [docs/implementation_guide.md](docs/implementation_guide.md) for a guide explaining how to actually use the API to play a game of CyberWars as a player client.

## Contributing
Contributions are welcome, just drop a Pull Request.

## Versioning

We use [SemVer](http://semver.org/) for versioning. This project is currently under version 1.0 and is subject to change drastically still (as is allowed in SemVer).

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
