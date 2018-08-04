<?php

namespace App\Installer\Policies;

class OsSupported extends BasePolicy
{
    public function allows($param = null)
    {
        if (!in_array(distname(), array_keys($this->config->get('os'))) || !file_exists('/etc/issue')) {
            $this->throwError('Unsupported GNU/Linux distribution');
        }
    }
}