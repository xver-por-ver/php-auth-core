<?php

declare(strict_types=1);

namespace Xver\PhpAuthCoreBundle\Tests\unit\SymfonyFramework\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Xver\PhpAuthCoreBundle\SymfonyFramework\DependencyInjection\PhpAuthCoreBundleExtension;

#[CoversClass(PhpAuthCoreBundleExtension::class)]
final class PhpAuthCoreBundleExtensionTest extends TestCase
{
    public function testLoadLoadsServicesConfiguration(): void
    {
        $container = new ContainerBuilder();
        $extension = new PhpAuthCoreBundleExtension();

        $extension->load([], $container);

        $this->assertTrue($container->hasDefinition(\Xver\PhpAuthCoreBundle\Account\Domain\Account::class));
    }
}
