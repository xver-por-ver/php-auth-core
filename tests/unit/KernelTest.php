<?php

declare(strict_types=1);

namespace Xver\PhpAuthCoreBundle\Tests\unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Xver\PhpAuthCoreBundle\Kernel;

#[CoversClass(Kernel::class)]
final class KernelTest extends TestCase
{
    public function testKernelUsesProvidedEnvironmentAndDebugMode(): void
    {
        $kernel = new Kernel('test', true);

        $this->assertSame('test', $kernel->getEnvironment());
        $this->assertTrue($kernel->isDebug());
    }
}
