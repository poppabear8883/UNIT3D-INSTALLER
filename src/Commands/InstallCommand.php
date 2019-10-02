<?php

namespace App\Commands;

use App\Classes\Config;
use App\Installer\Database\DatabaseSetup;
use App\Installer\Nginx\NginxSetup;
use App\Installer\PHP\PhpSetup;
use App\Installer\Policies\InstallerPolicies;
use App\Installer\Prerequisites\Prerequisites;
use App\Installer\Server\ServerSetup;
use App\Installer\UNIT3D\Unit3dSetup;
use App\Traits\ConsoleTools;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallCommand extends Command
{
    use ConsoleTools;

    private $steps = [
        InstallerPolicies::class => 'Validating Installer Policies',
        ServerSetup::class => 'Server Setup',
        Prerequisites::class => 'Prerequisites',
        DatabaseSetup::class => 'Configuring & Securing Database',
        PhpSetup::class => 'PHP & PHP-FPM Configuration',
        NginxSetup::class => 'Nginx Setup & Configurations',
        Unit3dSetup::class => 'UNIT3D Settings and Configuration',
    ];

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var Config
     */
    private $config;

    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Provisions Server')
            ->setHelp('Provisions Server and installs the UNIT3D Platform.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->config = new Config();

        $this->displayIntro();

        foreach ($this->steps as $class => $header) {
            $this->head($header);

            (new $class($this->io, $this->config))->handle();

            $this->done();
        }

        $this->head("Finalizing Install");

        $this->info();

        $this->done();
    }

    private function info()
    {
        $db = $this->config->app('db');
        $dbuser = $this->config->app('dbuser');
        $dbpass = $this->config->app('dbpass');
        $domain = $this->config->app('hostname');
        $owner = $this->config->app('owner');
        $password = $this->config->app('password');

        $this->io->writeln([
            '<fg=magenta>Please run "certbot renew --dry-run" manually to test your LetsEncrypt renewal process!!!</>',
        ]);

        $this->io->note([
            'Database: ' . $db,
            'Database User: ' . $dbuser,
            'Database Password: ' . $dbpass
        ]);

        $this->io->writeln([
            '<fg=green>UNIT3D has been successfully installed!</>',
            ' ',
            "Visit <fg=green>$domain</> in a browser",
            ' ',
            "Login: <fg=green>$owner</>",
            "Password: <fg=green>$password</>"
        ]);
    }

    private function done()
    {
        $this->io->writeln("<fg=green>[OK] Done!</>");
    }
}
