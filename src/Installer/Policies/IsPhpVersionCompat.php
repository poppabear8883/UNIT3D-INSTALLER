<?php

namespace App\Installer\Policies;

class IsPhpVersionCompat extends BasePolicy
{
    public function allows($param = null)
    {
        $phpv = $this->config->app('min_php_version');
        if (version_compare(PHP_VERSION, $phpv, '<')) {
            $this->throwError('PHP Version is not compatible with NxPanel. Install PHP ' . $phpv . ' or later...');
        }
    }
}