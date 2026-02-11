<?php

declare(strict_types=1);

namespace App\Tests\Factory;

interface FactoryInterface
{
    public function create(object $data): object;
}
