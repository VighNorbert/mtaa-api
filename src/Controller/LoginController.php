<?php

namespace App\Controller;

use App\Entity\AccessTokens;
use App\Repository\UserRepository;
use App\Response\ErrorResponse;
use App\Response\LoginResponse;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class LoginController extends BaseController
{

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(Request $request, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        try {
            $content = $request->getContent();
            $content = json_decode($content, flags: JSON_THROW_ON_ERROR);
            if (!isset($content->email) || !isset($content->password))
                throw new Exception();
            $email = (string) $content->email;
            $password = hash('sha256', (string) $content->password);
        } catch (Exception) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findOneBy(['email' => $email, 'passwordHash' => $password]);

        if (!$user) {
            return new ErrorResponse(new Exception('Nesprávne prihlasovacie údaje', Response::HTTP_UNAUTHORIZED));
        }

        $token = new AccessTokens();
        $token->setUser($user);
        $token->setToken(bin2hex(random_bytes(128)));

        $valid_until = new \DateTime('now');
        $valid_until->add(new \DateInterval('P14D'));
        $token->setValidUntil($valid_until);

        $entityManager->persist($token);
        $entityManager->flush();

        return new JsonResponse(
            new LoginResponse(
                in_array('ROLE_DOCTOR', $user->getRoles()) ? $user->getDoctor() : $user,
                $token->getToken()
            )
        );
    }
}
