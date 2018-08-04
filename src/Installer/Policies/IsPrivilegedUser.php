<?php

namespace App\Installer\Policies;

class IsPrivilegedUser extends BasePolicy
{
    public function allows($param = null)
    {
        $who = trim(shell_exec('whoami'));
        if ($who !== 'root') {
            $this->throwError('Must be ran as root or using sudo!');
        }
    }
}