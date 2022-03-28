<?php

namespace App\Controller;

use App\Repository\UserFavDoctorRepository;
use App\Response\ErrorResponse;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
            return new ErrorResponse(new Exception('Lekára sa nepodarilo nájsť', Response::HTTP_NOT_FOUND));
        }

        $ufd->setUpdatedAt(new DateTime());
        $ufd->setDeletedAt(new DateTime());
        $entityManager->flush();

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}