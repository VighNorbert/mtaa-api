<?php

namespace App\Controller;

use App\Repository\AppointmentRepository;
use App\Repository\DoctorRepository;
use App\Repository\UserRepository;
use App\Response\Appointment;
use App\Response\DoctorBase;
use App\Response\ErrorResponse;
use App\Response\User;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class AppointmentsDeleteController extends BaseController
{
    private AppointmentRepository $appointmentRepository;
    private DoctorRepository $doctorRepository;
    private UserRepository $userRepository;

    public function __construct(AppointmentRepository $appointmentRepository, DoctorRepository $doctorRepository, UserRepository $userRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
        $this->doctorRepository = $doctorRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_PATIENT');
        $user = $this->getUser();
        $doctor = $this->doctorRepository->find($user->getId());
        try {
            $app = $this->appointmentRepository->getMyAppointment($id, $user, $doctor);
        } catch (Exception $e) {
            return new ErrorResponse($e);
        }

        $appointment = $this->appointmentRepository->find($app['id']);
        $doctor = new DoctorBase($this->doctorRepository->find($app['doctor_id']));
        $patient = new User($this->userRepository->find($app['patient_id']));
        $result = new Appointment($appointment, $doctor, $patient);

        return new JsonResponse($result);
    }
}