<?php

namespace App\Controller;

use App\Repository\DoctorRepository;
use App\Response\DoctorFavorite;
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
            'only_favorites' => filter_var(($request->query->get('only_favorites', false)), FILTER_VALIDATE_BOOLEAN),
            'page' => $request->query->get('page', 1),
            'per_page' => $request->query->get('per_page', 10),
        ];
        $doctorsCollection = $this->doctorRepository->filterAll($parameters, $user);
        $result = [];
        foreach ($doctorsCollection as $doc){
            $doctor = $this->doctorRepository->find($doc['id']);
            $result[] = new DoctorFavorite($doctor, $doc['is_favorite']);
        }
        return new JsonResponse($result);
    }
}