<?php

namespace App;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class BaseTestCase extends TestCase
{
    protected $input;
    protected $output;
    protected $io;
    protected $salt;
    protected $config;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->input = new ArgvInput();
        $this->output = new ConsoleOutput();

        $this->io = new SymfonyStyle($this->input, $this->output);
    }

    protected function getOsConfig($path)
    {

    }

    protected function setOsConfig($path, $value)
    {

    }
}