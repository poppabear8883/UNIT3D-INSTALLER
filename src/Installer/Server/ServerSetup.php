<?php

namespace App\Installer\Server;

use App\Installer\BaseInstaller;

class ServerSetup extends BaseInstaller
{

    public function handle()
    {
        $this->server();

        $this->user();

        $this->database();

        $this->chat();
    }

    protected function server()
    {
        $server_name = $this->question('Server Name', hostname());
        $this->config->app('server_name', trim($server_name));

        do {
            $hostname = $this->question('The FQDN for this server', fqdn());

            $valid = (str_contains($hostname, '.') || $hostname === 'localhost');

            if (!$valid) {
                $this->warning("Invalid Format:");
                $this->io->writeln("<fg=blue>Must be a fully qualified domain name. Examples:</>");
                $this->io->listing([
                    fqdn().'.com',
                    'server.'.fqdn().'.com',
                    'example.com',
                    'server.example.com',
                    'localhost'
                ]);
            }

        } while (!$valid);

        $this->config->app('hostname', trim($hostname));

        $ip = $this->question('Primary IP Address', ip());
        $this->config->app('ip', trim($ip));

        $ssl = $this->io->choice('Enable SSL (https)', ['yes', 'no'], 'yes');
        $this->config->app('ssl', $ssl === 'yes' ? true : false);

    }

    protected function user()
    {
        $this->io->writeln('<fg=blue>User Settings</>');
        $this->seperator();

        $dbpass = $this->question('Owner Username', '');
        $this->config->app('owner', $dbpass);

        $dbpass = $this->question('Owner Password', '');
        $this->config->app('password', $dbpass);

        $default = 'admin@'.$this->config->app('hostname');
        $email = $this->question('Owner Email', $default);
        $this->config->app('owner_email', trim($email));
    }

    protected function database()
    {
        $this->io->writeln('<fg=blue>Database Settings</>');
        $this->seperator();

        $driver_choices = array_keys($this->config->app('database_installers'));
        $default_driver = $this->config->app('database_driver');

        $driver = $this->io->choice('Choose a database driver', $driver_choices, $default_driver);
        $this->config->app('database_driver', $driver);

        $this->io->writeln('<fg=red>It is STRONGLY advised to set a Database Server Password.</>');
        $mysql_pass = $this->question('Database Server Password', '');
        $this->config->app('dbrootpass', $mysql_pass);

        $db = $this->question('Database Name', 'unit3d');
        $this->config->app('db', $db);

        $dbuser = $this->question('Database User', 'unit3d');
        $this->config->app('dbuser', $dbuser);

        $dbpass = $this->question('Database Password', '');
        $this->config->app('dbpass', $dbpass);
    }

    protected function chat()
    {
        $this->io->writeln('<fg=blue>Chat Settings</>');
        $this->seperator();

        $port = $this->question('Chat Listening Port', '6001');
        $this->config->app('echo-port', $port);

        $protocol = $this->io->choice('Chat Server Protocol', ['http', 'https'], 'http');
        $this->config->app('echo-protocol', $protocol);
    }
}