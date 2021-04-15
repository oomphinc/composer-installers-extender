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
        /* In some cases where installers use 'library' or other non namespaced
         * types composer will fail to handle the installer but this project is
         * specifically supporting that case so this works around composer.
         *
         * In PHP 7.x composer will will be looking for a key of FALSE, which
         * evaluates to 0. In PHP 8.x composer will be looking for a key of "".
         * A value of false signals the installer to use the default path.
         */
        return [ 0 => false, '' => false ];
    }
}
