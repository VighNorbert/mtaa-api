<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UserRepository;
use App\Repository\ValidationSchema;
use App\Response\ErrorResponse;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class RegisterController extends BaseController
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

            if (!isset($content->name) || !isset($content->surname) || !isset($content->email) || !isset($content->phone) || !isset($content->password))
                throw new Exception('Chýbajúci povinný parameter.');

            $parameters = $this->parametersValidation(
                [
                    'name' => (string) $content->name,
                    'surname' => (string) $content->surname,
                    'email' => (string) $content->email,
                    'phone' => (string) $content->phone,
                    'password' => (string) $content->password
                ],
                [
                    'name' => new ValidationSchema(max: 64),
                    'surname' => new ValidationSchema(max: 64),
                    'email' => new ValidationSchema(allowed_values: ValidationSchema::VALIDATE_EMAIL, max: 256),
                    'phone' => new ValidationSchema(allowed_values: ValidationSchema::VALIDATE_PHONE, min: 10, max: 16),
                    'password' => new ValidationSchema(min: 8),
                ]
            );
        } catch (Exception $e) {
            return new ErrorResponse($e);
        }

        $user = $this->userRepository->findOneBy(['email' => $parameters['email']]);

        if ($user) {
            return new ErrorResponse(new Exception('Používateľ s daným emailom už existuje', Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        $user = new Users();
        $user->setName($parameters['name']);
        $user->setSurname($parameters['surname']);
        $user->setEmail($parameters['email']);
        $user->setPhone($parameters['phone']);
        $user->setPasswordHash(hash('sha256', $parameters['password']));

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['id' => $user->getId()], Response::HTTP_CREATED);
    }
}
