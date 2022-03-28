<?php

namespace App\Controller;

use App\Repository\AppointmentRepository;
use App\Repository\DoctorRepository;
use App\Repository\UserRepository;
use App\Response\ErrorResponse;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class AppointmentRemoveController extends BaseController
{
    private AppointmentRepository $appointmentRepository;
    private DoctorRepository $doctorRepository;

    public function __construct(AppointmentRepository $appointmentRepository, DoctorRepository $doctorRepository, UserRepository $userRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
        $this->doctorRepository = $doctorRepository;
    }

    public function __invoke(Request $request, ManagerRegistry $doctrine, $id, $appointment_id)
    {
        $entityManager = $doctrine->getManager();
        $this->denyAccessUnlessGranted('ROLE_PATIENT');
        $user = $this->getUser();
        $doctor = $this->doctorRepository->find($user->getId());
        try {
            $app = $this->appointmentRepository->getMyAppointment($appointment_id, $user, $doctor);
            if ($app == null) {
                throw new Exception('Termín neexistuje', Response::HTTP_NOT_FOUND);
            }
            if (isset($app['doctor_id']) && $app['doctor_id'] != $id) {
                throw new Exception('Nedostatočné práva', Response::HTTP_FORBIDDEN);
            }
        } catch (Exception $e) {
            return new ErrorResponse($e);
        }

        $appointment = $this->appointmentRepository->find($app['id']);
        $appointment->setDescription(null);
        $appointment->setPatient(null);
        $appointment->setUpdatedAt(new DateTime());
        $entityManager->flush();

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}