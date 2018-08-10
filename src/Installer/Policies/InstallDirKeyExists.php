<?php

namespace App\Installer\Policies;

class InstallDirKeyExists extends BasePolicy
{
    public function allows($param = null)
    {
        if (!array_key_exists('install_dir', $this->config->get('os.' . distname()))) {
            $this->throwError(
                "The key 'install_dir' was not found in 'app' config!
                
                Please fix this and try again.
                 
                Example: 'install_dir' => '/var/www/html',"
            );
        }
    }
}