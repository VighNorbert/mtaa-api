<?php

namespace App\Controller;

use App\Repository\AppointmentRepository;
use App\Response\ErrorResponse;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Exception;

#[AsController]
class AppointmentsAddController extends BaseController
{
    private AppointmentRepository $appointmentRepository;

    public function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }

    public function __invoke(Request $request, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $user = $this->getUser();
        $appointment = $this->appointmentRepository->findOneBy(['doctor' => intval($request->get('id')),
                                                        'id' => intval($request->get('appointment_id'))]);
        if ($appointment == null) {
            return new ErrorResponse(new Exception('Lekára alebo termín vyšetrenia sa nepodarilo nájsť', Response::HTTP_NOT_FOUND));
        }
        if ($appointment->getPatient() != null) {
            return new ErrorResponse(new Exception('Termín je už obsadený', Response::HTTP_CONFLICT));
        }
        try {
            $content = $request->getContent();
            $content = json_decode($content, flags: JSON_THROW_ON_ERROR);
            if (!isset ($content->description) || !isset ($content->appointment_type) || !in_array($content->appointment_type, ['O', 'F'])) {
                throw new Exception();
            }
        } catch (Exception) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }
        $appointment->setUpdatedAt(new DateTime());
        $appointment->setPatient($user);
        $appointment->setDescription($content->description);
        $appointment->setType($content->appointment_type);

        $entityManager->persist($appointment);
        $entityManager->flush();
        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}