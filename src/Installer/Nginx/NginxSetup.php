<?php

namespace App\Installer\Nginx;

use App\Installer\BaseInstaller;

class NginxSetup extends BaseInstaller
{

    public function handle()
    {
        $default = $this->config->os('nginx-sites-available_path') . '/default';
        $echo_port = $this->config->app('echo-port');
        $fqdn = $this->config->app('hostname');
        $email = $this->config->app('owner_email');
        $ssl = $this->config->app('ssl');

        if (file_exists($default)) {
            $this->process(["rm -rf $default"]);
        }

        $this->createFromStub([
            '{{FQDN}}' => $fqdn
        ], 'nginx/default.site', $default);

        $this->process([
            "ufw allow 'Nginx Full'",
            "ufw delete allow 'Nginx HTTP'",
            "ufw allow $echo_port",
            "ufw enable",
            "systemctl restart nginx"
        ]);

        $this->install('certbot python3-certbot-nginx');

        if ($ssl == 'yes') {
            $this->process([
                "certbot --redirect --nginx -n --agree-tos --email=$email  -d $fqdn -d www.$fqdn --rsa-key-size 2048",
            ]);
        }

        $this->io->writeln(' ');
    }
}
