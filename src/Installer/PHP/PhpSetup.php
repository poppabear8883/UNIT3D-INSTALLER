<?php

namespace App\Installer\PHP;

use App\Installer\BaseInstaller;

class PhpSetup extends BaseInstaller
{

    public function handle()
    {
        $fqdn = $this->config->app('hostname');

        $pool = trim(shell_exec('find /etc/php* -type d \( -name "pool.d" -o -name "*fpm.d" \)'));
        $php_fpm = trim(shell_exec('ls /etc/init.d/php*.*-fpm* |cut -f 4 -d /'));

        $this->createFromStub(
            [
                '{{FQDN}}' => $this->config->app('hostname'),
                '{{WEBUSER}}' => strtolower($this->config->os('web-user')),
            ],
            'php-fpm/php-fpm.conf',
            "$pool/$fqdn.conf"
        );

        //$this->process(["cp -f " . resource_path() . distname() . "/php-fpm/php-fpm.sock $pool/",]);

        if (!is_link("/etc/init.d/php-fpm")) {
            $this->process(["ln -s /etc/init.d/$php_fpm /etc/init.d/php-fpm > /dev/null 2>&1"]);
        }

        foreach (explode("\n", trim(shell_exec('find /etc/php* -name php.ini'))) as $file) {
            $this->process([
                "sed -i 's/;date.timezone =/date.timezone = UTC/g' $file",
                "sed -i 's%_open_tag = Off%_open_tag = On%g' $file",
                "sed -i 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=1/g' $file"
            ]);

        }

        $this->process([
            "systemctl restart $php_fpm",
            "update-rc.d $php_fpm defaults",
            "service $php_fpm start"
        ]);

        $this->io->writeln(' ');
    }
}