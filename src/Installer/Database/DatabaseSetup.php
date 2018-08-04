<?php

namespace App\Installer\Database;

use App\Installer\BaseInstaller;

class DatabaseSetup extends BaseInstaller
{
    public function handle()
    {
        $driver = $this->config->app('database_driver');
        $class = $this->config->app('database_installers.' . $driver);

        (new $class($this->io, $this->config))->handle();
    }
}