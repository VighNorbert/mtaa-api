<?php

namespace App\Controller;

use App\Repository\DoctorRepository;
use App\Repository\SpecialisationRepository;
use App\Repository\UserRepository;
use App\Response\DoctorDetail;
use App\Response\DoctorFavorite;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class DoctorsItemController extends AbstractController
{

    private DoctorRepository $doctorRepository;

    public function __construct(DoctorRepository $doctorRepository)
    {
        $this->doctorRepository = $doctorRepository;
    }

    public function __invoke(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_PATIENT');
        $id = intval($request->get('id'));
        $user = $this->getUser();
        $favorite = $this->doctorRepository->findFavorite($user, $id);
        $favorite = !(sizeof($favorite) == 0);
        $doctor = $this->doctorRepository->find($id);
        $schedules = $this->doctorRepository->filterSchedules($id); //TODO
        $result = new DoctorDetail($doctor, $favorite, $schedules);
        return new JsonResponse($result);
    }
}