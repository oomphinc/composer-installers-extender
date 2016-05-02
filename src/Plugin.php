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
		$types = (array) $extra['installer-types'];
		// default composer-installers frameworks can be disabled
		$disable = array_filter( $types, function( $type ) {
			return $type[0] === '!';
		} );
		// do we have any frameworks to disable?
		if ( count( $disable ) ) {
			// get the installation manager
			$manager = $composer->getInstallationManager();
			// use reflection to get all of the installers
			$installers = new \ReflectionProperty( $manager, 'installers' );
			$installers->setAccessible( true );
			// find the composer-installers installer
			foreach ( $installers->getValue( $manager ) as $installer ) {
				if ( !( $installer instanceof \Composer\Installers\Installer ) ) {
					continue;
				}
				// use reflection again to get access to the supported types (frameworks) array
				$prop = new \ReflectionProperty( $installer, 'supportedTypes' );
				$prop->setAccessible( true );
				$frameworks = $prop->getValue( $installer );
				// unset any disabled frameworks
				foreach ( $disable as $framework ) {
					unset( $frameworks[ substr( $framework, 1 ) ] );
				}
				// update the value
				$prop->setValue( $installer, $frameworks );
				break;
			}
			// remove disabled types from the new types
			$types = array_diff( $types, $disable );
		}
		$installer = new Installer( $io, $composer, $types );
		$composer->getInstallationManager()->addInstaller( $installer );
	}

}
