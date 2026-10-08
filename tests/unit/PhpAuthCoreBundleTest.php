<?php

declare(strict_types=1);

namespace Xver\PhpAuthCoreBundle\Tests\unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Xver\PhpAuthCoreBundle\PhpAuthCoreBundle;
use Xver\PhpAuthCoreBundle\SymfonyFramework\DependencyInjection\PhpAuthCoreBundleExtension;

#[CoversClass(PhpAuthCoreBundle::class)]
class PhpAuthCoreBundleTest extends TestCase
{
    public function testGetPathReturnsBundleRoot(): void
    {
        $bundle = new PhpAuthCoreBundle();

        $this->assertSame(dirname(__DIR__, 2), $bundle->getPath());
    }

    public function testGetContainerExtensionReturnsCachedExtension(): void
    {
        $bundle = new PhpAuthCoreBundle();

        $extension = $bundle->getContainerExtension();

        $this->assertInstanceOf(PhpAuthCoreBundleExtension::class, $extension);
        $this->assertInstanceOf(ExtensionInterface::class, $extension);
        $this->assertSame($extension, $bundle->getContainerExtension());
    }
}
