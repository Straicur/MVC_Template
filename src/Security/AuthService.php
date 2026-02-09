<?php

declare(strict_types = 1);

namespace App\Security;

use App\Entity\User;
use App\ExceptionManagement\Exceptions\ApiException\UnauthorizedException\UnauthorizedException;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Override;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class AuthService implements AuthServiceInterface
{
    public function __construct(
        private JWTTokenManagerInterface $JWTTokenManager,
        private ParameterBagInterface $config,
        private TokenStorageInterface $tokenStorage,
        private LoggerInterface $logger,
        private RefreshTokenGeneratorInterface $refreshTokenGenerator,
        private EntityManagerInterface $entityManager,
    ) {}

    #[Override]
    public function createToken(UserInterface $user): string
    {
        $jwt = $this->JWTTokenManager->create($user);

        $securityToken = new JWTPostAuthenticationToken(
            $user,
            'main',
            $user->getRoles(),
            $jwt
        );

        $this->tokenStorage->setToken($securityToken);

        return $jwt;
    }

    #[Override]
    public function getAccessTokenTimeToLive(): int
    {
        return $this->config->get('lexik_jwt_authentication.token_ttl');
    }

    #[Override]
    public function getRefreshTokenTimeToLive(): int
    {
        return $this->config->get('gesdinet_jwt_refresh_token.ttl');
    }

    #[Override]
    public function getUserFromAccessToken(): User
    {
        $token = $this->tokenStorage->getToken();

        if (false === $token instanceof TokenInterface) {
            $exception = new UnauthorizedException();
            $this->logger->error(message: $exception->getMessage());

            throw $exception;
        }

        /** @var ?User $user */
        $user = $token->getUser();

        if (null === $user) {
            $exception = new UnauthorizedException();
            $this->logger->error(message: $exception->getMessage());

            throw $exception;
        }

        return $user;
    }

    #[Override]
    public function createRefreshToken(UserInterface $user): RefreshTokenInterface
    {
        $refreshToken = $this->refreshTokenGenerator->createForUserWithTtl(
            user: $user,
            ttl: $this->getRefreshTokenTimeToLive()
        );

        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();

        return $refreshToken;
    }
}
