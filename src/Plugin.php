<?php

namespace OomphInc\ComposerInstallersExtender;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class Plugin implements PluginInterface {

	public function activate( Composer $composer, IOInterface $io ) {
		// check that we have package types
		if ( !( $extra = $composer->getPackage()->getExtra() ) || empty( $extra['installer-types'] ) ) {
			return;
		}
		$installer = new Installer( $io, $composer, (array) $extra['installer-types'] );
		$composer->getInstallationManager()->addInstaller( $installer );
	}

}
