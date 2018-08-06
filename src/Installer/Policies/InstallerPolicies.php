<?php

namespace App\Installer\Policies;

class InstallerPolicies extends BasePolicy
{
    private $steps = [
        /*
         * Configuration policies
         */
        AppKeyExists::class,
        InstallDirKeyExists::class,
        PhpVersionKeyExists::class,
        DatabaseInstallersKeyExists::class,
        DatabaseDriverKeyExists::class,

        /*
         * User and Server State policies
         */
        IsPrivilegedUser::class,
        AppNotInstalled::class,
        IsPhpVersionCompat::class
    ];

    public function allows($param = null)
    {
        foreach ($this->steps as $class) {
            (new $class($this->io, $this->config))->allows($param);
        }
    }
}