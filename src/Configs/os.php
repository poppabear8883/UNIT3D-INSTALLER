<?php

return [

    /*
     * UBUNTU
     */
    'ubuntu' => [
        'pkg_manager' => 'apt',
        'web-user' => 'www-data',
        'install_dir' => '/var/www/html',
        'nginx-sites-available_path' => '/etc/nginx/sites-available',

        'software' => [
            'nginx' => 'Web Server',
            'mysql-server' => 'Database Server',
            'supervisor' => 'A Process Control System',
            'redis-server' => 'In-memory Data Structure Store',
            'nodejs' => 'JavaScript Run-time Environment (Includes npm)',
            'build-essential' => 'Basic C/C++ Development Environment',
            'git' => 'Version Control',
            'tmux' => 'Screen Multiplexer',
            'vim' => 'Text Editor',
            'wget' => 'Transfer Data From A Server',
            'zip' => 'Compress Files',
            'unzip' => 'Decompress Files',
            'htop' => 'Monitor Server Resources',
        ],
    ]


];