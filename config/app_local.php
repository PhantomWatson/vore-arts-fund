<?php
/**
 * The following configuration values will overwrite the default values in app.php.
 * This arrangement allows app.php to remain unchanged, so that when updates to that file
 * are made available through https://github.com/cakephp/app, it can be easily overwritten.
 */

use Cake\Core\Configure;

$config = [
    /**
     * Apply timestamps with the last modified time to static assets (js, css, images).
     * Will append a querystring parameter containing the time the file was modified.
     * This is useful for busting browser caches.
     *
     * Set to true to apply timestamps when debug is true. Set to 'force' to always
     * enable timestamping regardless of debug value.
     */
    'Asset' => [
        'timestamp' => 'force',
        // 'cacheTime' => '+1 year'
    ],

    /**
     * Email configuration.
     *
     * By defining transports separately from delivery profiles you can easily
     * re-use transport configuration across multiple profiles.
     *
     * You can specify multiple configurations for production, development and
     * testing.
     *
     * Each transport needs a `className`. Valid options are as follows:
     *
     *  Mail   - Send using PHP mail function
     *  Smtp   - Send using SMTP
     *  Debug  - Do not send the email, just return the result
     *
     * You can add custom transports (or override existing transports) by adding the
     * appropriate file to src/Mailer/Transport. Transports should be named
     * 'YourTransport.php', where 'Your' is the name of the transport.
     */
    'EmailTransport' => [
        'default' => [
            /*
             * The following keys are used in SMTP transports:
             */
            'username' => 'noreply@voreartsfund.org',
            'password' => env('EMAIL_PASSWORD'),
        ],
    ],

    /**
     * Email delivery profiles
     *
     * Delivery profiles allow you to predefine various properties about email
     * messages from your application and give the settings a name. This saves
     * duplication across your application and makes maintenance and development
     * easier. Each profile accepts a number of keys. See `Cake\Mailer\Email`
     * for more information.
     */
    'Email' => [
        'default' => [
            'transport' => 'default',
            'from' => 'noreply@voreartsfund.org',
            //'charset' => 'utf-8',
            //'headerCharset' => 'utf-8',
        ],
    ],

    /**
     * Connection information used by the ORM to connect
     * to your application's datastores.
     *
     * ### Notes
     * - Drivers include Mysql Postgres Sqlite Sqlserver
     *   See vendor\cakephp\cakephp\src\Database\Driver for complete list
     * - Do not use periods in database name - it may lead to error.
     *   See https://github.com/cakephp/cakephp/issues/6471 for details.
     * - 'encoding' is recommended to be set to full UTF-8 4-Byte support.
     *   E.g set it to 'utf8mb4' in MariaDB and MySQL and 'utf8' for any
     *   other RDBMS.
     */
    'Datasources' => [
        'default' => [
            'username' => env('DATABASE_USERNAME', 'root'),
            'password' => env('DATABASE_PASSWORD'),
            'database' => env('DATABASE_NAME', 'vore_arts_fund'),
            /*
             * You do not need to set this flag to use full utf-8 encoding (internal default since CakePHP 3.6).
             */
            'encoding' => 'utf8mb4',
        ],

        /**
         * The test connection is used during the test suite.
         */
        'test' => [
            'username' => env('TESTING_DATABASE_USERNAME', 'root'),
            'password' => env('TESTING_DATABASE_PASSWORD'),
            'database' => env('TESTING_DATABASE_NAME', 'vore_arts_fund_testing'),
            'encoding' => 'utf8mb4',
        ],
    ],

    'Log' => [
        'email' => [
            'className' => 'Cake\Log\Engine\FileLog',
            'path' => LOGS,
            'file' => 'email',
            'levels' => ['info'],
        ],
    ],
];

if (Configure::read('debug')) {
    // Use the DebugTransport class to emulate sending an email without actually sending it
    $config['EmailTransport']['default']['className'] = 'Debug';

    // Log emails to /logs/email.log
    $config['Email']['default']['log'] = [
        'level' => 'info',
        'scope' => 'email',
    ];
}

return $config;
