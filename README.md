# Vore Arts Fund

[![Maintainability](https://api.codeclimate.com/v1/badges/ca66ee05a477d522df82/maintainability)](https://codeclimate.com/github/BallStateCBER/vore-arts-fund/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/ca66ee05a477d522df82/test_coverage)](https://codeclimate.com/github/BallStateCBER/vore-arts-fund/test_coverage)
[![Build Status](https://travis-ci.org/BallStateCBER/vore-arts-fund.svg?branch=development)](https://travis-ci.org/BallStateCBER/vore-arts-fund)

This is the code base for the not-for-profit Vore Arts Fund corporation's website, a project by Ball State's [Center for Business and Economic Research](https://www.bsu.edu/academics/centersandinstitutes/cber) and [Computer Science](https://www.bsu.edu/academics/collegesanddepartments/computer-science) students.

## Getting Started

Follow these instructions to get a copy of the project running on your machiene for development and testing purposes. See [Deployment](#deployment) for instructions on deploying the project to a live system.

### Prerequisites

- [Composer](https://book.cakephp.org/3/en/installation.html#installing-composer)
    - see `composer.phar`
- IDE of choice - PhpStorm or VSCode recommended
- **Linux:** MySQL DBMS, Apache HTTP Server
- **Windows and MacOS:** [MAMP](https://www.mamp.info/en/mamp)

### Installing

1. Clone the repo
    ```bash
    git clone https://github.com/BallStateCBER/vore-arts-fund.git
    ```
1. Create the file `config/.env` using `config/.env.default` as a template
    - Fill all uncommented fields set to empty strings in `.env.default`
        ```bash
        cp config/.env.default config/.env
        ```
1. Use Composer to install CakePHP, PHP, Twilio SDK 
    ```bash
    composer install && composer update && composer dump-autoload --optimize
    ```
1. Set up your database structure
    ```bash
    bin\cake migrations migrate
    ```
1. Give executable permissions to `bin/cake`
    - Linux/MacOS:
        ```bash
        chmod +x bin/cake
        ```
    - Windows: .bat file should be executable without changes
1. Start your [development server](https://book.cakephp.org/3/en/installation.html#development-server_)
    ```bash
    bin/cake server
    ```

## Testing

### End-to-end Testing

1. Be sure to set the 3 TESTING_DATABASE fields in your `config/.env` file
    ```php
    export TESTING_DATABASE_NAME=""
    export TESTING_DATABASE_USERNAME=""
    export TESTING_DATABASE_PASSWORD=""
    ```
1. Run the tests using phpunit installed by Composer
    ```bash
    vendor/bin/phpunit
    ```

### Coding Style Tests

TODO: add coding style tests and their instructions here

## Deployment

- [Travis CI](https://travis-ci.org/BallStateCBER/vore-arts-fund)
- [Vore Arts Fund Staging](http://staging.voreartsfund.org/)

## Contibuting

To contribute, please contact [Graham Watson](mailto:gtwatson@bsu.edu).

## Authors

- **Graham Watson** - *Project Client and Senior Developer* - [PhatomWatson](https://github.com/PhantomWatson)
- **Alec Schimmel** - *Initial Work with BSU CS Capstone Group* - [aschimmel](https://github.com/aschimmel)
- **Dakota Savage** - *Initial Work with BSU CS Capstone Group* - [djsavage2](https://github.com/djsavage2)
- **Madison Turley** - *Initial Work with BSU CS Capstone Group* - [madisonturley](https://github.com/madisonturley)
- **Sean Wolfe** - *Initial Work with BSU CS Capstone Group* - [SeanW98](https://github.com/SeanW98)

## License

This project is licensed under the MIT License - see the [LICENSE.md](https://github.com/BallStateCBER/vore-arts-fund/LICENSE.md) file for details