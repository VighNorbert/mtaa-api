<?php

namespace App\Controller;

use App\Repository\DoctorRepository;
use App\Repository\SpecialisationRepository;
use App\Repository\UserRepository;
use App\Response\DoctorFavourite;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

#[AsController]
class DoctorsCollectionController extends AbstractController
{

    private DoctorRepository $doctorRepository;
    private UserRepository $userRepository;
    private SpecialisationRepository $specialisationRepository;

    public function __construct(DoctorRepository $doctorRepository, UserRepository $userRepository, SpecialisationRepository $specialisationRepository)
    {
        $this->doctorRepository = $doctorRepository;
        $this->userRepository = $userRepository;
        $this->specialisationRepository = $specialisationRepository;
    }

    public function __invoke(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_PATIENT');
        $user = $this->getUser();
        $parameters = [
            'name' => $request->query->get('name', ''),
            'specialisation' => $request->query->get('specialisation', ''),
            'city' => $request->query->get('city', ''),
            'only_favorites' => boolval($request->query->get('only_favorites', false)),
            'page' => $request->query->get('page', 1),
            'per_page' => $request->query->get('per_page', 10),
        ];
        $doctorsCollection = $this->doctorRepository->filterAll($parameters, $user);
        $result = [];
        foreach ($doctorsCollection as $doc){
            $doctor = $this->doctorRepository->find($doc['id']);
            $result[] = new DoctorFavourite($doctor, $doc['is_favourite']);
        }
        return new JsonResponse($result);
    }
}