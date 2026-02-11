<?php

declare(strict_types=1);

namespace App\Tests\Factory\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Factory\FactoryInterface;
use App\Tests\Factory\User\DTO\UserTestDTO;
use App\Util\PasswordHasher;

class UserFactory implements FactoryInterface
{
    public function __construct(
        private UserRepository $userRepository,
    )
    {
    }

    public function create(UserTestDTO $userTestDTO): User
    {
        $user = new User(
            'mosinskidamian11@gmail.com',
            PasswordHasher::hash('zaq1@WSX')
        );
        $this->userRepository->saveUser(user: $user);

        return $user;
    }
}
