<?php

namespace App\Controller;

use App\Repository\AppointmentRepository;
use App\Repository\DoctorRepository;
use App\Response\Appointment;
use App\Response\ErrorResponse;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class AppointmentsTimesController extends BaseController
{
    private AppointmentRepository $appointmentRepository;
    private DoctorRepository $doctorRepository;

    public function __construct(AppointmentRepository $appointmentRepository, DoctorRepository $doctorRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
        $this->doctorRepository = $doctorRepository;
    }

    public function __invoke(Request $request)
    {
        $id = $request->get('id');
        $doctor = $this->doctorRepository->find($id);
        if ($doctor == null) {
            return new ErrorResponse(new Exception('Lekára sa nepodarilo nájsť', Response::HTTP_NOT_FOUND));
        }
        $date = new DateTime();
        $date->setDate($request->get('year'), $request->get('month'), $request->get('day'));
        $date = $date->format('Y-m-d');
        $appointments = $this->appointmentRepository->getTimes($id, $date);
        $this->appointmentRepository->getTimes($id, $date);
        return new JsonResponse($appointments);
    }
}