<?php

namespace App\Controller;

use App\Repository\UserFavDoctorRepository;
use App\Response\ErrorResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Exception;

#[AsController]
class DoctorFavoriteRemoveController extends BaseController
{
    private UserFavDoctorRepository $ufdRepository;

    public function __construct(UserFavDoctorRepository $ufdRepository)
    {
        $this->ufdRepository = $ufdRepository;
    }

    public function __invoke(Request $request, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $this->denyAccessUnlessGranted('ROLE_PATIENT');
        $user_id = $this->getUser()->getId();
        $doctor_id = intval($request->get('id'));
        $ufd = $this->ufdRepository->findOneBy(['patient' => $user_id, 'doctor' => $doctor_id]);

        if ($ufd == null) {
            return new ErrorResponse(new Exception('Lekára sa nepodarilo nájsť', 404));
        }

        $entityManager->remove($ufd);
        $entityManager->flush();

        return new JsonResponse([], 204);
    }
}