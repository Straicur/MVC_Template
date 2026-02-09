<?php

declare(strict_types = 1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

use const PASSWORD_BCRYPT;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private readonly UserRepository $userAuthRepository,
    ) {}

    #[Override]
    public function load(ObjectManager $manager): void
    {
        $user = new User(
            'mosinskidamian11@gmail.com',
            password_hash(
                password: 'zaq1@WSX',
                algo: PASSWORD_BCRYPT,
                options: [
                    'cost' => 15,
                ],
            )
        );
        $this->userAuthRepository->saveUser(user: $user);
        $manager->flush();
    }

    #[Override]
    public static function getGroups(): array
    {
        return ['default'];
    }
}
