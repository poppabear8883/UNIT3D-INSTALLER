<?php

namespace App\Installer\Policies;

class DatabaseDriverKeyExists extends BasePolicy
{
    public function allows($param = null)
    {
        if (!array_key_exists('database_driver', $this->config->get('app'))) {
            $this->throwError("The key 'database_driver' was not found in 'app' config!
                
                Please fix this and try again.
                 
                Example: 'database_driver' => 'MySql',"
            );
        }

        $driver = $this->config->app('database_driver');

        if (!is_string($driver)) {
            $this->throwError("The key 'database_driver' is NOT a string in 'app' config!
                
                'database_driver' should be a string value of the Default database driver.
                 
                Example: 'database_driver' => 'MySql',"
            );
        }

        if (!array_key_exists($driver, $this->config->app('database_installers'))) {
            $this->throwError("The key 'database_driver' is invalid in 'app' config!
                
                'database_driver' should match a key listed in 'database_installers' array.
                 
                Example: 'database_driver' => 'MySql',
                 
                         'database_installers' => [
                              'MySql' => MySqlInstaller::class,
                         ],"
            );
        }
    }
}