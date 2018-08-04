<?php

namespace App\Installer\Utilities;

use App\Installer\BaseInstaller;

class RestartService extends BaseInstaller
{
    public function handle($target = null, $service = null)
    {
        if ($target === null || $service === null) {
            $this->throwError(
                "Null Argument supplied in handle method for RestartService::class. 
                
                Expecting string value"
            );
        }

        $this->salt->execute($target, 'service.restart', [$service]);
    }
}