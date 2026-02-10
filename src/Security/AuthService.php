<?php

declare(strict_types = 1);

namespace App\Security;

use App\Entity\User;
use App\ExceptionManagement\Exceptions\ApiException\UnauthorizedException\UnauthorizedException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Override;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class AuthService implements AuthServiceInterface
{
    public function __construct(
        private JWTTokenManagerInterface $JWTTokenManager,
        private UserRepository $userRepository,
        private TokenStorageInterface $tokenStorage,
        private LoggerInterface $logger,
        private RefreshTokenGeneratorInterface $refreshTokenGenerator,
        private EntityManagerInterface $entityManager,
        private ConfigServiceInterface $configService,
    ) {}

    #[Override]
    public function getUserByEmailAndPassword(string $email, string $password): User
    {
        $user = $this->userRepository->findUserByEmail($email);

        if (null === $user) {
            throw new UnauthorizedException();
        }

        $validCredentials = password_verify(
            password: $password,
            hash: $user->getPassword()
        );

        if (false === $validCredentials) {
            throw new UnauthorizedException();
        }

        return $user;
    }

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
            ttl: $this->configService->getRefreshTokenTimeToLive()
        );

        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();

        return $refreshToken;
    }
}
