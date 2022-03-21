<?php

namespace App\Security;

use App\Repository\AccessTokenRepository;
use App\Response\ErrorResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiAuthenticator extends AbstractAuthenticator
{
    private AccessTokenRepository $accessTokenRepository;

    /**
     * @param AccessTokenRepository $accessTokenRepository
     */
    public function __construct(AccessTokenRepository $accessTokenRepository)
    {
        $this->accessTokenRepository = $accessTokenRepository;
    }

    /**
     * @param Request $request
     * @return bool|null
     */
    public function supports(Request $request): ?bool
    {
        $path = $request->getPathInfo();
        $allowed_paths = ['/', '/login', '/register'];
        return !in_array($path, $allowed_paths);
    }

    /**
     * @param Request $request
     * @return Passport
     */
    public function authenticate(Request $request): Passport
    {
        $token = $request->headers->get('x-auth-token');

        if (empty($token)) {
            throw new CustomUserMessageAuthenticationException('Chýbajúca autentifikácia', [], 401);
        }

        return new SelfValidatingPassport(
            new UserBadge($token, function ($token) {
                $token_object = $this->accessTokenRepository->findOneBy(['token' => $token]);

                if (!$token_object || $token_object->getValidUntil() < new \DateTime()) {
                    throw new CustomUserMessageAuthenticationException('Neplatná autentifikácia', [], 401);
                }

                return $token_object->getUser();
            })
        );
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $firewallName
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new ErrorResponse($exception);
    }
}
