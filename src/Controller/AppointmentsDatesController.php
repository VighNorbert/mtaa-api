<?php

namespace App\Controller;

use App\Entity\Appointments;
use App\Repository\AppointmentRepository;
use App\Repository\DoctorRepository;
use App\Repository\UserRepository;
use App\Repository\ValidationSchema;
use App\Repository\WorkScheduleRepository;
use App\Response\ErrorResponse;
use DateInterval;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class AppointmentsDatesController extends BaseController
{

    private AppointmentRepository $appointmentRepository;
    private DoctorRepository $doctorRepository;
    private UserRepository $userRepository;
    private WorkScheduleRepository $workScheduleRepository;

    public function __construct(AppointmentRepository $appointmentRepository, DoctorRepository $doctorRepository, UserRepository $userRepository, WorkScheduleRepository $workScheduleRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
        $this->doctorRepository = $doctorRepository;
        $this->userRepository = $userRepository;
        $this->workScheduleRepository = $workScheduleRepository;
    }

    public function __invoke(Request $request, ManagerRegistry $doctrine, int $id)
    {
        $parameters = $this->parametersValidation(
            [
                'month' => $request->query->get('month'),
                'year' => $request->query->get('year'),
            ],
            [
                'month' => new ValidationSchema(allowed_values: ValidationSchema::VALIDATE_NUMBER_GTZ, min: 1, max: 12),
                'year' => new ValidationSchema(allowed_values: ValidationSchema::VALIDATE_NUMBER_GTZ),
            ]
        );

        $entityManager = $doctrine->getManager();
        $this->denyAccessUnlessGranted('ROLE_PATIENT');
        $doctor = $this->doctorRepository->find($id);
        if ($doctor == null)
            return new ErrorResponse(new Exception('Lekára sa nepodarilo nájsť', Response::HTTP_NOT_FOUND));

        $schedules = $this->workScheduleRepository->findBy(['doctor' => $id]);
        $tomorrow = new DateTime();
        $tomorrow->add(new \DateInterval('P1D'));
        $nextMonth = new DateTime();
        $nextMonth->add(new \DateInterval('P29D'));
        $generatedDates = $this->appointmentRepository->getGeneratedDates($id, $tomorrow, $nextMonth);

        $day=$tomorrow;

        $al = $doctor->getAppointmentsLength();

        while ($day < $nextMonth) {
            $day_string = $day->format('Y-m-d');
            if (!in_array($day_string, $generatedDates)) {
                $w = intval($day->format('w'));
                foreach ($schedules as $s) {
                    $cat = $s->getCreatedAt();
                    $dat = $s->getDeletedAt();
                    if($s->getWeekday() == $w && $cat < $day && ($dat == null || $dat > $day)) {
                        $from = $s->getTimeFrom();
                        $from = new DateTime($day_string . ' ' . $from->format('H:i:s'));

                        $end = new DateTime($day_string . ' ' . $s->getTimeTo()->format('H:i:s'));

                        $to = clone $from;
                        $to = $to->add(new DateInterval('PT' . $al . 'M'));
                        while ($to <= $end) {
                            $app = new Appointments();
                            $app->setDoctor($doctor)
                                ->setDate($from)
                                ->setTimeFrom($from)
                                ->setTimeTo($to);
                            $entityManager->persist($app);

                            $from = $to;
                            $to = clone $from;
                            $to = $to->add(new DateInterval('PT' . $al . 'M'));
                        }
                    }
                }

            }
            $day->add(new DateInterval('P1D'));
        }
        $entityManager->flush();

        $freeDates = $this->appointmentRepository->getFreeDates($id, $parameters['month'], $parameters['year']);

        return new JsonResponse($freeDates);
    }
}