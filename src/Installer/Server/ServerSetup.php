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

        $this->mail();

        $this->chat();

        $this->apiKeys();
    }

    protected function server()
    {
        $server_name = $this->question('Server Name', hostname());
        $this->config->app('server_name', trim($server_name));

        do {
            $hostname = strtolower($this->question('The FQDN for this server', fqdn()));

            $valid = (str_contains($hostname, '.') || $hostname === 'localhost');

            if (!$valid) {
                $this->warning("Invalid Format:");
                $this->io->writeln("<fg=blue>Must be a fully qualified domain name. Examples:</>");
                $this->io->listing([
                    fqdn() . '.com',
                    'server.' . fqdn() . '.com',
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
        $this->config->app('ssl', $ssl);

    }

    protected function user()
    {
        $this->io->writeln('<fg=blue>User Settings</>');
        $this->seperator();

        $dbowner = $this->question('Owner Username', '');
        $this->config->app('owner', $dbowner);

        $dbpass = $this->question('Owner Password', '');
        $this->config->app('password', $dbpass);

        $default = 'admin@' . $this->config->app('hostname');
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

        $this->io->writeln('<fg=red>It is STRONGLY advised to set a DB Server Root Password.</>');
        $db_root_pass = $this->question('DB Server Root Password', '');
        $this->config->app('dbrootpass', $db_root_pass);

        $db = $this->question('UNIT3D DB Name', 'unit3d');
        $this->config->app('db', $db);

        $dbuser = $this->question('UNIT3D DB User', 'unit3d');
        $this->config->app('dbuser', $dbuser);

        $dbpass = $this->question('UNIT3D DB Password', '');
        $this->config->app('dbpass', $dbpass);
    }

    protected function chat()
    {
        $this->io->writeln('<fg=blue>Chat Settings</>');
        $this->seperator();

        $port = $this->question('Chat Listening Port', '8443');
        $this->config->app('echo-port', $port);
    }

    protected function apiKeys()
    {
        $this->io->writeln('<fg=blue>API Keys</>');
        $this->seperator();

        $this->io->writeln('<fg=magenta>Obtaining an TMDB Key</>:');
        $this->io->listing([
            'Visit <fg=cyan>https://www.themoviedb.org/</>',
            'Create Free Account',
            'Visit <fg=cyan>https://www.themoviedb.org/settings/api</>'
        ]);

        $key = $this->question('TMDB Key', '');
        $this->config->app('tmdb-key', $key);

        $this->io->writeln('<fg=magenta>Obtaining an OMDB Key</>:');
        $this->io->listing([
            'Visit <fg=cyan>https://www.omdbapi.com/apikey.aspx</>',
            'Choose Free or Patreon (Recommended)',
        ]);

        $key = $this->question('OMDB Key', '');
        $this->config->app('omdb-key', $key);
    }

    protected function mail()
    {
        $this->io->writeln('<fg=blue>Mail Settings</>');
        $this->io->writeln('(Used for things like invites, registration, ect.)');
        $this->seperator();

        $this->io->writeln('<fg=blue>/* You will need a provider like torguard. */</>');
        $this->io->writeln('<fg=cyan>https://torguard.net/anonymous-email.php</>');

        $this->io->writeln('Ref: <fg=cyan>https://laravel.com/docs/5.6/mail#introduction</>');

        $value = $this->io->choice('Mail Driver', [
            "smtp",
            "sendmail",
            "mailgun",
            "mandrill",
            "ses",
            "sparkpost",
            "log",
            "array"
        ], 'smtp');

        $this->config->app('mail_driver', $value);

        $value = $this->question('Mail Host', '');
        $this->config->app('mail_host', $value);

        $value = $this->question('Mail Port', '');
        $this->config->app('mail_port', $value);

        $value = $this->question('Mail Username', '');
        $this->config->app('mail_username', $value);

        $value = $this->question('Mail Password', '');
        $this->config->app('mail_password', $value);

        $value = $this->question('Mail From Name', '');
        $this->config->app('mail_from_name', $value);

    }
}