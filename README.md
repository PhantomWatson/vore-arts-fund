# Vore Arts Fund

This code base is for the not-for-profit Vore Arts Fund corporation's website.

The live website can be found at https://voreartsfund.org/.

## Contents

- [Testing](#testing)
- [Deployment](#deployment)
- [Contributing](#contributing)
- [Authors](#authors)
- [License](#license)

## Using Docker
1. In a CLI, navigate to `/docker` and run `./build.sh` to build Docker images and start containers
1. Add the following files to your `hosts` file
   ```
   127.0.0.1	vore.test
   127.0.0.1	www.vore.test
   ```
1. In a browser, open `http://vore.test:9000`

If the SSL certificate is working, `https://` can be used instead.

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

## Webpack
- To use the Webpack dev server, run `npm run dev` for the React project in question, then append
  `?webpack-dev=localhost:3000` to your URL or visit [localhost:3000](http://localhost:3000) (which loads the project's
  `/public/index.html` file).

## Deployment

- Upon every push to the development and master branches, [Deploy-bot](https://github.com/PhantomWatson/deploybot)
automatically deploys to the [staging](https://staging.voreartsfund.org/) and [production](https://voreartsfund.org/)
servers.
- Deployment includes `npm install` and `npm run prod` for React projects

## Contributing

To contribute, please contact [Graham Watson](mailto:graham@phantomwatson.com).

## Authors

- **Graham Watson** - *Lead Developer* - [PhantomWatson](https://github.com/PhantomWatson)
- **Alec Schimmel** - *Initial Work with BSU CS Capstone Group* - [aschimmel](https://github.com/aschimmel)
- **Dakota Savage** - *Initial Work with BSU CS Capstone Group* - [djsavage2](https://github.com/djsavage2)
- **Madison Turley** - *Initial Work with BSU CS Capstone Group* - [madisonturley](https://github.com/madisonturley)
- **Sean Wolfe** - *Initial Work with BSU CS Capstone Group* - [SeanW98](https://github.com/SeanW98)

## License

This project is licensed under the MIT License - see the [LICENSE.md](https://github.com/BallStateCBER/vore-arts-fund/LICENSE.md) file for details.
