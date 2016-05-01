<?php

namespace OomphInc\ComposerInstallersExtender;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;

class Installer extends \Composer\Installers\Installer {

	// array of supported types
	protected $packageTypes;
	// instance of Composer\Installers\BaseInstaller used to get a path from extra
	protected $installerHelper;

	public function __construct( IOInterface $io, Composer $composer, array $packageTypes ) {
		$this->packageTypes = $packageTypes;
		// get an installer helper
		$this->installerHelper = new InstallerHelper( null, $composer, $io );
		parent::__construct( $io, $composer );
	}

	public function getInstallPath( PackageInterface $package ) {
		$this->installerHelper->setPackage( $package );
		try {
			// see if we have an install path
			return $this->installerHelper->getInstallPath( $package );
		} catch ( \InvalidArgumentException $e ) {
			// otherwise use the default (library) install path
			return \Composer\Installer\LibraryInstaller::getInstallPath( $package );
		}
	}

	public function supports( $packageType ) {
		return in_array( $packageType, $this->packageTypes );
	}

}
