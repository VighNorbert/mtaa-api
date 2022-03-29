<?php

namespace App\Controller;

use App\Repository\DoctorRepository;
use App\Response\DoctorFavourite;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class DoctorsCollectionController extends BaseController
{

    private DoctorRepository $doctorRepository;

    public function __construct(DoctorRepository $doctorRepository)
    {
        $this->doctorRepository = $doctorRepository;
    }

    public function __invoke(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_PATIENT');
        $user = $this->getUser();
        $parameters = [
            'name' => $request->query->get('name', ''),
            'specialisation' => $request->query->get('specialisation', ''),
            'city' => $request->query->get('city', ''),
            'only_favourites' => filter_var(($request->query->get('only_favourites', false)), FILTER_VALIDATE_BOOLEAN),
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