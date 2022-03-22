<?php

namespace App\Controller;

use App\Entity\Doctors;
use App\Repository\DoctorRepository;
use App\Response\ErrorResponse;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class DoctorAvatarController extends BaseController
{

    private DoctorRepository $doctorRepository;

    public function __construct(DoctorRepository $doctorRepository)
    {
        $this->doctorRepository = $doctorRepository;
    }

    public function __invoke(Request $request, int $id, ManagerRegistry $doctrine)
    {
        $doctor = $this->doctorRepository->find($id);

        if (!($doctor instanceof Doctors)) {
            return new ErrorResponse(new Exception('Lekára sa nepodarilo nájsť', Response::HTTP_NOT_FOUND));
        }

        $filename = $doctor->getAvatarFilename();

        $path = 'img/avatars/' . $filename;

        if ($filename === null || !file_exists($path)) {
            return new ErrorResponse(new Exception('Avatar neexistuje', Response::HTTP_NOT_FOUND));
        }

        return new BinaryFileResponse($path);
    }
}
