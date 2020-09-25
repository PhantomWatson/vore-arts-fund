# Vore Arts Fund

[![Maintainability](https://api.codeclimate.com/v1/badges/ca66ee05a477d522df82/maintainability)](https://codeclimate.com/github/BallStateCBER/vore-arts-fund/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/ca66ee05a477d522df82/test_coverage)](https://codeclimate.com/github/BallStateCBER/vore-arts-fund/test_coverage)
[![Build Status](https://travis-ci.org/BallStateCBER/vore-arts-fund.svg?branch=development)](https://travis-ci.org/BallStateCBER/vore-arts-fund)

This code base is for the not-for-profit Vore Arts Fund corporation's website, a project by Ball State's [Center for Business and Economic Research](https://www.bsu.edu/academics/centersandinstitutes/cber) and [Computer Science](https://www.bsu.edu/academics/collegesanddepartments/computer-science) students.

The live website can be found at https://voreartsfund.org/.

## Contents

- [Getting Started](#getting-started)
- [Testing](#testing)
- [Deployment](#deployment)
- [Contributing](#contributing)
- [Authors](#authors)
- [License](#license)

## Getting Started

Follow these instructions to get a copy of the website running on your machine for development and testing purposes. See [Deployment](#deployment) for instructions on deploying the project to a live system.

### Prerequisites

- [Composer](https://book.cakephp.org/3/en/installation.html#installing-composer)
    - see `composer.phar` to see the components Composer will provide you with
- IDE of choice - [PhpStorm](https://www.jetbrains.com/phpstorm/) or [Visual Studio Code](https://code.visualstudio.com/) recommended
- **Linux:** MySQL DBMS, Apache HTTP Server
- **Windows and MacOS:** [MAMP](https://www.mamp.info/en/mamp)

### Installing

1. Clone the repo.
    ```bash
    $ git clone https://github.com/BallStateCBER/vore-arts-fund.git
    ```
1. Create the file `config/.env` using `config/.env.default` as a template. Be sure to fill out empty export fields in your new file.
    ```bash
    $ cp config/.env.default config/.env
    ```
1. Use Composer to install CakePHP, PHP, Twilio SDK.
    ```bash
    $ composer install && composer update && composer dump-autoload --optimize
    ```
1. Set up your database structure. Be sure to have your MySQL instance running.
    ```bash
    $ bin\cake migrations migrate
    ```
1. Give executable permissions to `bin/cake`
    - Linux/MacOS:
        ```bash
        $ chmod +x bin/cake
        ```
    - Windows: the provided .bat file should be executable without changes
1. Start your [development server](https://book.cakephp.org/3/en/installation.html#development-server)
    ```bash
    $ bin/cake server
    ```

## Testing

### End-to-End Testing

1. Set the 3 TESTING_DATABASE_... fields in your `config/.env` file.
    ```php
    export TESTING_DATABASE_NAME=""
    export TESTING_DATABASE_USERNAME=""
    export TESTING_DATABASE_PASSWORD=""
    ```
1. Run the tests through phpunit (installed for you by Composer).
    ```bash
    $ vendor/bin/phpunit
    ```

### Coding Style Tests

TODO: add coding style tests and their instructions here.

## Deployment

- [Travis CI](https://travis-ci.org/BallStateCBER/vore-arts-fund)
- [Staging](https://staging.voreartsfund.org/)

## Contributing

To contribute, please contact [Graham Watson](mailto:gtwatson@bsu.edu).

## Authors

- **Graham Watson** - *Project Client and Senior Developer* - [PhantomWatson](https://github.com/PhantomWatson)
- **Alec Schimmel** - *Initial Work with BSU CS Capstone Group* - [aschimmel](https://github.com/aschimmel)
- **Dakota Savage** - *Initial Work with BSU CS Capstone Group* - [djsavage2](https://github.com/djsavage2)
- **Madison Turley** - *Initial Work with BSU CS Capstone Group* - [madisonturley](https://github.com/madisonturley)
- **Sean Wolfe** - *Initial Work with BSU CS Capstone Group* - [SeanW98](https://github.com/SeanW98)

## License

This project is licensed under the MIT License - see the [LICENSE.md](https://github.com/BallStateCBER/vore-arts-fund/LICENSE.md) file for details.
