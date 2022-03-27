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
            return new ErrorResponse(new Exception('Lekára alebo termín vyšetrenia sa nepodarilo nájsť', 404));
        }
        if ($appointment->getPatient() != null) {
            return new ErrorResponse(new Exception('Termín je už obsadený', 409));
        }
        try {
            $content = $request->getContent();
            $content = json_decode($content, flags: JSON_THROW_ON_ERROR);
            if (!isset ($content->description)) {
                throw new Exception();
            }
            $appointment->setDescription($content->description);
        } catch (Exception) {
            return new Response('', 400);
        }
        $appointment->setUpdatedAt(new DateTime());
        $appointment->setPatient($user);
        $appointment->setType($request->get('type'));

        $entityManager->persist($appointment);
        $entityManager->flush();
        return new JsonResponse([], 204);
    }
}