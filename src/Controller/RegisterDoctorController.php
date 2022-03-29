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
use phpDocumentor\Reflection\Types\Array_;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class RegisterDoctorController extends BaseController
{
    const ACCEPTED_FILETYPES = ['jpg', 'jpeg', 'png', 'webp'];

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

            if (!isset($content->name) || !isset($content->surname) || !isset($content->email) || !isset($content->phone) || !isset($content->password)
             || !isset($content->title) || !isset($content->specialisation_id) || !isset($content->appointments_length) || !isset($content->address) || !isset($content->city)
             || !isset($content->description) || !isset($content->schedules)
             || (isset($content->avatar) && (!isset($content->avatar->file) || !isset($content->avatar->filename) || !isset($content->avatar->extension)))
            )
                throw new Exception('Chýbajúci povinný parameter.');

            $parameters = $this->parametersValidation(
                [
                    'name' => (string) $content->name,
                    'surname' => (string) $content->surname,
                    'title' => (string) $content->title,
                    'email' => (string) $content->email,
                    'phone' => (string) $content->phone,
                    'password' => (string) $content->password,
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
                    'password' => new ValidationSchema(min: 8),
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
            return new ErrorResponse(new Exception('Používateľ s daným emailom už existuje', Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        $specialisation = $this->specialisationRepository->find($parameters['specialisation_id']);

        if ($specialisation == null) {
            return new ErrorResponse(new Exception('Špecializácia s daným identifikátorom neexistuje', Response::HTTP_BAD_REQUEST));
        }

        if (isset($content->avatar)) {
            if (isset($content->avatar->extension) && !in_array($content->avatar->extension, $this::ACCEPTED_FILETYPES)) {
                return new ErrorResponse(new Exception('Nepovolený formát avataru. Povolené formáty sú: jpg, jpeg, png, webp'));
            }
            if (isset($content->avatar->file) && $content->avatar->file != null) {
                $imgdata = base64_decode($content->avatar->file);
                $mime_type = finfo_buffer(finfo_open(), $imgdata, FILEINFO_MIME_TYPE);
                if (!in_array($mime_type, ['image/jpeg', 'image/png', 'image/webp']))
                    return new ErrorResponse(new Exception('Nepovolený formát avataru. Povolené formáty sú: jpg, jpeg, png, webp'));
            }
        }

        $user = new Users();
        $user->setName($parameters['name'])
             ->setSurname($parameters['surname'])
             ->setEmail($parameters['email'])
             ->setPhone($parameters['phone'])
             ->setPasswordHash(hash('sha256', $parameters['password']));

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

        try {
            if (isset($content->avatar)) {
                $avatar = $content->avatar;
                if (isset($avatar->file) && isset($avatar->filename) && isset($avatar->extension)) {
                    $avatar = [
                        'file' => (string)$avatar->file,
                        'filename' => (string)$avatar->filename,
                        'extension' => strtolower($avatar->extension),
                    ];

                    // check if directory exists
                    if (!file_exists('img/')) mkdir('img');
                    if (!file_exists('img/avatars/')) mkdir('img/avatars');

                    // find available filename
                    $i = 0;
                    $path = 'img/avatars/' . $avatar['filename'] . '.' . $avatar['extension'];
                    $filename = $avatar['filename'] . '.' . $avatar['extension'];
                    while (file_exists($path)) {
                        $path = 'img/avatars/' . $i . "_" . $avatar['filename'] . '.' . $avatar['extension'];
                        $filename = $i . "_" . $avatar['filename'] . '.' . $avatar['extension'];
                        $i++;
                    }

                    // save image
                    $f = fopen($path, "wb");
                    fwrite($f, base64_decode($avatar['file']));
                    fclose($f);

                    $doctor->setAvatarFilename($filename);
                }
            }
        } catch (Exception $e) {
            return new ErrorResponse($e);
        }

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

        return new JsonResponse(['id' => $doctor->getId()], Response::HTTP_CREATED);
    }
}
