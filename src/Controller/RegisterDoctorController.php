<?php

namespace App\Controller;

use App\Entity\Doctors;
use App\Entity\Users;
use App\Entity\WorkSchedules;
use App\Repository\SpecialisationRepository;
use App\Repository\UserRepository;
use App\Repository\ValidationSchema;
use App\Response\ErrorResponse;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class RegisterDoctorController extends BaseController
{

    private UserRepository $userRepository;
    private SpecialisationRepository $specialisationRepository;

    public function __construct(UserRepository $userRepository, SpecialisationRepository $specialisationRepository)
    {
        $this->userRepository = $userRepository;
        $this->specialisationRepository = $specialisationRepository;
    }

    public function __invoke(Request $request, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        try {
            $content = $request->getContent();
            $content = json_decode($content, flags: JSON_THROW_ON_ERROR);

            if (!isset($content->name) || !isset($content->surname) || !isset($content->email) || !isset($content->phone) || !isset($content->password_hash))
                throw new Exception('Chýbajúci povinný parameter.');

            $parameters = $this->parametersValidation(
                [
                    'name' => (string) $content->name,
                    'surname' => (string) $content->surname,
                    'title' => (string) $content->title,
                    'email' => (string) $content->email,
                    'phone' => (string) $content->phone,
                    'password' => (string) $content->password_hash,
                    'specialisation_id' => (int) $content->specialisation_id,
                    'appointments_length' => (int) $content->appointments_length,
                    'address' => (string) $content->address,
                    'city' => (string) $content->city,
                    'description' => (string) $content->description,
                    'schedules' => $content->schedules,
                ],
                [
                    'name' => new ValidationSchema(max: 64),
                    'surname' => new ValidationSchema(max: 64),
                    'title' => new ValidationSchema(max: 8),
                    'email' => new ValidationSchema(allowed_values: ValidationSchema::VALIDATE_EMAIL, max: 256),
                    'phone' => new ValidationSchema(allowed_values: ValidationSchema::VALIDATE_PHONE, min: 10, max: 16),
                    'password' => new ValidationSchema(min: 64, max: 64),
                    'specialisation_id' => new ValidationSchema(allowed_values: ValidationSchema::VALIDATE_NUMBER_GTZ),
                    'appointments_length' => new ValidationSchema(allowed_values: ValidationSchema::VALIDATE_NUMBER_GTZ),
                    'address' => new ValidationSchema(max: 128),
                    'city' => new ValidationSchema(max: 128),
                    'description' => new ValidationSchema(max: 128),
                ]
            );
            foreach ($parameters['schedules'] as $key => $schedule) {
                $parameters['schedules'][$key] = $this->parametersValidation(
                    [
                        'weekday' => (int) $schedule->weekday,
                        'time_from' => (string) $schedule->time_from,
                        'time_to' => (string) $schedule->time_to,
                    ],
                    [
                        'weekday' => new ValidationSchema(ValidationSchema::VALIDATE_NUMBER, min: 0, max: 6),
                        'time_from' => new ValidationSchema(ValidationSchema::VALIDATE_TIME),
                        'time_to' => new ValidationSchema(ValidationSchema::VALIDATE_TIME),
                    ]
                );
            }
        } catch (Exception $e) {
            return new ErrorResponse($e);
        }

        $user = $this->userRepository->findOneBy(['email' => $parameters['email']]);

        if ($user) {
            return new ErrorResponse(new Exception('Používateľ s daným emailom už existuje', 422));
        }

        $specialisation = $this->specialisationRepository->find($parameters['specialisation_id']);

        if ($specialisation == null) {
            return new ErrorResponse(new Exception('Špecializácia s daným identifikátorom neexistuje', 400));
        }

        $user = new Users();
        $user->setName($parameters['name'])
             ->setSurname($parameters['surname'])
             ->setEmail($parameters['email'])
             ->setPhone($parameters['phone'])
             ->setPasswordHash($parameters['password']);

        $entityManager->persist($user);
        $entityManager->flush();

        $doctor = new Doctors();
        $doctor->setUser($user)
            ->setSpecialisation($specialisation)
            ->setTitle($parameters['title'])
            ->setAppointmentsLength($parameters['appointments_length'])
            ->setAddress($parameters['address'])
            ->setCity($parameters['city'])
            ->setDescription($parameters['description']);

        $entityManager->persist($doctor);

        $user->setDoctor($doctor);
        $entityManager->persist($user);

        foreach($parameters['schedules'] as $schedule) {
            $work_schedule = new WorkSchedules();
            $work_schedule->setDoctor($doctor)
                ->setWeekday($schedule['weekday'])
                ->setTimeFrom(new DateTime($schedule['time_from']))
                ->setTimeTo(new DateTime($schedule['time_to']));

            $entityManager->persist($work_schedule);
        }

        $entityManager->flush();

        return new JsonResponse(['id' => $user->getId()], 201);
    }
}
