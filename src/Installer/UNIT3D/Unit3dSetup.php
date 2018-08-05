<?php

namespace App\Installer\UNIT3D;

use App\Installer\BaseInstaller;

class Unit3dSetup extends BaseInstaller
{
    public function handle()
    {
        $this->clone();

        $this->env();

        $this->perms();

        $this->setup();

    }

    protected function clone()
    {
        $this->io->writeln('<fg=blue>Cloning Source Files</>');
        $this->seperator();

        $install_dir = $this->config->app('install_dir');
        $url = $this->config->app('repository');

        if (is_dir($install_dir)) {
            $this->process(["rm -rf $install_dir"]);
        }

        $this->process(["git clone $url $install_dir"]);

        if (!is_dir($install_dir) || !is_file("$install_dir/server.php")) {
            $this->throwError('Something went wrong with the cloning process. Please report this bug!');
        }
    }

    protected function env()
    {
        $this->io->writeln("\n\n<fg=blue>Preparing the '.env' File</>");
        $this->seperator();

        $install_dir = $this->config->app('install_dir');

        if (file_exists("$install_dir/.env")) {
            $this->process(["rm $install_dir/.env"]);
        }

        $this->createFromStub(
            [
                '{{FQDN}}' => $this->config->app('hostname'),
                '{{DBDRIVER}}' => strtolower($this->config->app('database_driver')),
                '{{DBPORT}}' => $this->config->app('dbpass'),
                '{{DB}}' => $this->config->app('db'),
                '{{DBUSER}}' => $this->config->app('dbuser'),
                '{{DBPASS}}' => $this->config->app('dbpass'),
                '{{OWNER}}' => $this->config->app('owner'),
                '{{OWNEREMAIL}}' => $this->config->app('owner_email'),
                '{{OWNERPASSWORD}}' => $this->config->app('password'),
                '{{TMDBAPIKEY}}' => $this->config->app('tmdb-key'),
                '{{OMDBAPIKEY}}' => $this->config->app('omdb-key'),
            ],
            '../.env.stub',
            "$install_dir/.env"
        );

        $this->io->writeln('<fg=green>OK</>');
    }

    protected function perms()
    {
        $this->io->writeln("\n<fg=blue>Setting Permissions</>");
        $this->seperator();

        $install_dir = $this->config->app('install_dir');
        $web_user = $this->config->os('web-user');

        $this->process([
            "chown -R $web_user:$web_user /etc/letsencrypt",
            "chown -R $web_user:$web_user " . dirname($install_dir),
            "find $install_dir -type d -exec chmod 0775 '{}' + -or -type f -exec chmod 0664 '{}' +",
            "chmod 750 $install_dir/artisan",
            "chmod 640 $install_dir/.env"
        ]);
    }

    protected function setup()
    {
        $this->io->writeln("\n\n<fg=blue>Setting Up Web Site</>");
        $this->seperator();

        $install_dir = $this->config->app('install_dir');
        $fqdn = $this->config->app('hostname');
        $web_user = $this->config->os('web-user');
        $echo_port = $this->config->app('echo-port');
        $echo_protocol = $this->config->app('echo-protocol');

        $this->createFromStub([
            '{{FQDN}}' => $fqdn,
            '{{PORT}}' => $echo_port,
            '{{PROTOCOL}}' => $echo_protocol,
        ], '../laravel-echo-server.stub', '/var/www/html/laravel-echo-server.json');

        $this->createFromStub([
            '{{INSTALLDIR}}' => $install_dir,
            '{{WEBUSER}}' => $web_user,
        ], 'supervisor/app.conf', '/etc/supervisor/conf.d/unit3d.conf');

        $this->process([
            'supervisorctl reread',
            'supervisorctl update',
            'wget -q -O /tmp/libpng12.deb http://mirrors.kernel.org/ubuntu/pool/main/libp/libpng/libpng12-0_1.2.54-1ubuntu1_amd64.deb',
            'dpkg -i /tmp/libpng12.deb',
            'rm /tmp/libpng12.deb'
        ]);

        $www_cmds = [
            'laravel-echo-server client:add',
            'composer install',
            'npm install',
            'npm run prod',
            'php artisan key:generate',
            'php artisan migrate --seed',
        ];

        foreach ($www_cmds as $cmd) {
            $this->process([
                "su $web_user -s /bin/bash --command=\"cd $install_dir && $cmd\""
            ]);
        }

        $this->io->writeln(' ');
    }

}