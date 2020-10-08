<?php

declare(strict_types = 1);

namespace OomphInc\ComposerInstallersExtender\Installers;

use Composer\Installers\BaseInstaller;

/**
 * Provides a custom installer class for custom installer types.
 *
 * By default, the parent class has no specified locations. By not providing an
 * array of locations we are forcing the installer to use custom installer
 * paths.
 */
class CustomInstaller extends BaseInstaller
{
    public function getLocations()
    {
        // It will be looking for a key of FALSE, which evaluates to 0, i.e. the
        // first element that element value being false signals the installer to
        // use the default path.
        return [ false ];
    }
}
