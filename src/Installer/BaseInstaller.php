<?php

namespace App\Installer;

use App\Classes\Config;
use App\Classes\Process;
use App\Traits\ConsoleTools;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class BaseInstaller
{
    use ConsoleTools;

    /**
     * @var SymfonyStyle $io
     */
    protected $io;

    /**
     * @var Process
     */
    protected $process;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var $pkg_manager
     */
    protected $pkg_manager;

    /**
     * @var int $timeout
     */
    protected $timeout = 15;

    public function __construct(SymfonyStyle $io, Config $config)
    {
        $this->io = $io;
        $this->process = new Process($this->io);
        $this->config = $config;
        $this->pkg_manager = $config->get('os.'.distname().'.pkg_manager');
    }

    abstract public function handle();

    protected function install($pkgs)
    {
        $this->process->execute($this->pkg_manager . " install -y $pkgs");
    }

    protected function process(array $commands, $force = false)
    {
        foreach ($commands as $cmd) {
            $this->process->execute($cmd, null, $force);
        }
    }

    protected function createFromStub(array $fr, $stub, $to)
    {
        $stub = resource_path() . distname() . '/' . $stub;

        $file = file_get_contents($stub);

        if ($file === false) {
            $this->throwError("'$stub' error getting file contents. Please report this bug.");
        }

        $contents = str_replace(array_keys($fr), array_values($fr), $file);

        file_put_contents($to, $contents);
        return true;
    }

    protected function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

}