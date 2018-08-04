<?php

namespace App\Installer\Policies;

use App\Classes\Config;
use App\Traits\ConsoleTools;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class BasePolicy
{
    use ConsoleTools;

    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * @var Config
     */
    protected $config;

    public function __construct(SymfonyStyle $io, Config $config)
    {
        $this->io = $io;

        $this->config = $config;
    }

    public function handle($param = null)
    {
        $this->allows($param);
    }

    abstract public function allows($param = null);
}