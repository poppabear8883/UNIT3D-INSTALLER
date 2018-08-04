<?php

namespace App\Installer\Policies;

class DatabaseInstallersKeyExists extends BasePolicy
{
    public function allows($param = null)
    {
        if (!array_key_exists('database_installers', $this->config->get('app'))) {
            $this->throwError(
                "The key 'database_installers' was not found in 'app' config!
                
                Please fix this and try again.
                 
                Example: 'database_installers' => [
                              'MySql' => MySqlInstaller::class,
                         ],"
            );
        }

        $database_installers = $this->config->app('database_installers');

        if (!is_array($database_installers)) {
            $this->throwError(
                "The key 'database_installers' is NOT an array in 'app' config!
                
                'database_installers' should be an array of database drivers
                mapped with their respected installer class.
                 
                Example: 'database_installers' => [
                              'MySql' => MySqlInstaller::class,
                         ],"
            );
        }

        if (count($database_installers) <= 0) {
            $this->throwError(
                "The key 'database_installers' is an empty array in 'app' config!
                
                'database_installers' should be an array of at least 1 database driver
                mapped with its respected installer class.
                 
                Example: 'database_installers' => [
                              'MySql' => MySqlInstaller::class,
                         ],"
            );
        }
    }
}