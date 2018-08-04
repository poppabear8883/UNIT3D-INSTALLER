<?php

namespace App\Installer\Policies;

class PhpVersionKeyExists extends BasePolicy
{
    public function allows($param = null)
    {
        if (!array_key_exists('min_php_version', $this->config->get('app'))) {
            $this->throwError(
                "The key 'min_php_version' was not found in 'app' config!
                
                Please fix this and try again.
                 
                Example: 'min_php_version' => '7.0',"
            );
        }
    }
}