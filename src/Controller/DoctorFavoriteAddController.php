<?php

namespace App\Controller;

use App\Entity\UserFavDoctors;
use App\Repository\DoctorRepository;
use App\Repository\UserFavDoctorRepository;
use App\Response\ErrorResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Exception;

#[AsController]
class DoctorFavoriteAddController extends BaseController
{
    private DoctorRepository $doctorRepository;
    private UserFavDoctorRepository $ufdRepository;

    public function __construct(DoctorRepository $doctorRepository, UserFavDoctorRepository $ufdRepository)
    {
        $this->doctorRepository = $doctorRepository;
        $this->ufdRepository = $ufdRepository;
    }

    public function __invoke(Request $request, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $this->denyAccessUnlessGranted('ROLE_PATIENT');
        $user = $this->getUser();
        $doctor_id = intval($request->get('id'));
        $doctor = $this->doctorRepository->find($doctor_id);
        if ($doctor == null) {
            return new ErrorResponse(new Exception('Lekára sa nepodarilo nájsť', 404));
        }
        $fav = $this->ufdRepository->findOneBy(['patient' => $user->getId(), 'doctor' => $doctor_id]);
        if ($fav != null) {
            return new JsonResponse([], 204);
        }
        $ufd = new UserFavDoctors();
        $ufd->setPatient($user);
        $ufd->setDoctor($doctor);

        $entityManager->persist($ufd);
        $entityManager->flush();

        return new JsonResponse([], 204);
    }
}