<?php

namespace App\Installer\Policies;

class AppKeyExists extends BasePolicy
{
    public function allows($param = null)
    {
        if (!array_key_exists('app', $this->config->getValues())) {
            $this->throwError(
                "The key 'app' was not found in config!
                
                Inside the 'Configs' directory there should be a file named 'app.php'
                with the application configuration."
            );
        }
    }
}