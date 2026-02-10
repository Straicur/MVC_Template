<?php

declare(strict_types = 1);

namespace App\Security;

use App\Entity\User;
use App\ExceptionManagement\Exceptions\ApiException\UnauthorizedException\UnauthorizedException;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface AuthServiceInterface
{
    /**
     * @throws UnauthorizedException
     */
    public function getUserByEmailAndPassword(string $email, string $password): User;

    public function createToken(UserInterface $user): string;

    /**
     * @throws UnauthorizedException
     */
    public function getUserFromAccessToken(): User;

    public function createRefreshToken(UserInterface $user): RefreshTokenInterface;
}
