<?php

declare(strict_types=1);

namespace OomphInc\ComposerInstallersExtender\Installers;

use Composer\Composer;
use Composer\Config;
use Composer\IO\IOInterface;
use Composer\Package\RootPackageInterface;
use PHPUnit\Framework\TestCase;
use Composer\Package\Package;

class InstallerTest extends TestCase
{

    protected $composer;

    protected $io;

    public function setUp(): void
    {
        parent::setUp();

        $config = $this->getMockBuilder(Config::class)
            ->onlyMethods(['get'])
            ->getMock();
        $config->method('get')
            ->will($this->returnCallback(
                function (string $key, int $flags = 0) {
                    if ($key == 'vendor-dir') {
                        return 'vendor/';
                    }
                    if ($key == 'bin-dir') {
                        return 'bin/';
                    }
                    if ($key == 'bin-compat') {
                        return 'library';
                    }
                    return null;
                }
            ));

        $this->composer = $this->createMock(Composer::class);
        $this->composer
            ->method('getConfig')
            ->willReturn($config);

        $this->io = $this->createMock(IOInterface::class);
    }

    public function testGetInstallPath(): void
    {
        $root_package = $this->getMockBuilder(RootPackageInterface::class)
            ->onlyMethods(['getExtra'])
            ->getMockForAbstractClass();
        $root_package->method('getExtra')
            ->willReturn([
                'installer-types' => ['custom-type'],
                'installer-paths' => [
                    'custom/path/{$name}' => ['type:custom-type'],
                ],
            ]);

        $this->composer
            ->method('getPackage')
            ->willReturn($root_package);

        $installer = new Installer($this->io, $this->composer);

        $package = new Package('oomphinc/test', '1.0.0', '1.0.0');
        $package->setType('custom-type');

        $this->assertEquals(
            'custom/path/test',
            $installer->getInstallPath($package)
        );
    }

    public function testSupports(): void
    {
        $installer = new class extends Installer {

            public function __construct()
            {
            }

            public function getInstallerTypes(): array
            {
                return ['custom-type'];
            }

        };

        $this->assertTrue($installer->supports('custom-type'));
        $this->assertFalse($installer->supports('oomph'));
    }

    /**
     * @dataProvider installerTypesDataProvider
     */
    public function testGetInstallerTypes($extra, array $expected): void
    {
        $root_package = $this->getMockBuilder(RootPackageInterface::class)
            ->onlyMethods(['getExtra'])
            ->getMockForAbstractClass();
        $root_package->method('getExtra')
            ->willReturn($extra);

        $this->composer
            ->method('getPackage')
            ->willReturn($root_package);

        $installer = new Installer($this->io, $this->composer);
        $this->assertEquals($expected, $installer->getInstallerTypes());
    }

    public function installerTypesDataProvider(): array
    {
        return [
            [
                [
                    'installer-types' => ['custom-type'],
                    'installer-paths' => [
                        'custom/path/{$name}' => ['type:custom-type'],
                    ],
                ],
                ['custom-type'],
            ],
            [
                [],
                [],
            ],
        ];
    }
}
