<?php

use App\Installer\Database\MySqlSetup;

return [
    'install_dir' => '/var/www/html',

    'min_php_version' => '7.1.3',

    'repository' => 'https://github.com/HDInnovations/UNIT3D.git',

    'database_installers' => [
        /**
         * Map to the Installer class
         */
        'MySql' => MySqlSetup::class,

        //'MariaDB' => '',
        //'Postgres' => '',
    ],

    /*
     * Dynamically set configuration defaults and place holders
     *
     * These do NOT need policy classes
     */

    /* Main Server */
    'server_name' => '',
    'ip' => '',
    'hostname' => '',
    'ssl' => true,
    'owner' => '',
    'owner_email' => '',
    'password' => '',

    /* Database */
    'database_driver' => 'MySql',

    'db' => '',
    'dbuser' => '',
    'dbpass' => '',
    'dbrootpass' => '',

    /* Chat */
    'echo-port' => '',
    'echo-protocol' => '',

    /* API Keys */
    'fanart-key' => '',
    'tmdb-key' => '',
    'tvdb-key' => '',
    'omdb-key' => ''
];