<?php

declare(strict_types=1);

namespace Xver\PhpAuthCoreBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Kernel\AbstractBundle;
use Xver\PhpAuthCoreBundle\SymfonyFramework\DependencyInjection\PhpAuthCoreBundleExtension;

final class PhpAuthCoreBundle extends AbstractBundle
{
    #[\Override]
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    #[\Override]
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new PhpAuthCoreBundleExtension();
        }

        return $this->extension ?: null;
    }
}
