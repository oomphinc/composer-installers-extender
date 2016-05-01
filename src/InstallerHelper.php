<?php

namespace OomphInc\ComposerInstallersExtender;

use Composer\Installers\BaseInstaller;
use Composer\Package\PackageInterface;

class InstallerHelper extends BaseInstaller {

	/**
	 * Set the package property so the same instance can be used for multiple packages.
	 * @param PackageInterface $package
	 */
	function setPackage( PackageInterface $package ) {
		$this->package = $package;
	}

}
