<?php

declare(strict_types = 1);

namespace App\Security;

use App\Entity\User;
use App\ExceptionManagement\Exceptions\ApiException\UnauthorizedException\UnauthorizedException;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface AuthServiceInterface
{
    public function createToken(UserInterface $user): string;

    public function getAccessTokenTimeToLive(): int;

    public function getRefreshTokenTimeToLive(): int;

    /**
     * @throws UnauthorizedException
     */
    public function getUserFromAccessToken(): User;

    public function createRefreshToken(UserInterface $user): RefreshTokenInterface;
}
