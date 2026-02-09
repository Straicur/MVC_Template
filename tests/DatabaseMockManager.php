<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Component\DependencyInjection\ContainerInterface;

class DatabaseMockManager
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function getService(string $serviceName): object
    {
        return $this->container->get($serviceName);
    }
}
