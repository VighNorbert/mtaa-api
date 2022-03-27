<?php

namespace App\Repository;

use App\Entity\Appointments;
use App\Entity\Doctors;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

class AppointmentRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointments::class);
    }

    public function getMyAppointments(Users $user, ?Doctors $doctor, ?string $date)
    {
        $doc_id = ($doctor != null) ? $doctor->getId() : null;
        $sql = "SELECT id, doctor_id, patient_id
                FROM appointments
                WHERE 1=1
                " .(($date != null) ? "AND (date=:d)" : "")
                  .(($doctor != null) ? "AND (doctor_id=:did)" : "AND (patient_id=:pid)");
        $params = [
            "d" => $date,
            "did" => $doc_id,
            "pid" => $user->getId()
        ];
        $statement = $this->_em->getConnection()->executeQuery($sql, $params);
        return $statement->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    public function getMyAppointment(int $id, Users $user, ?Doctors $doctor)
    {
        $doc_id = ($doctor != null) ? $doctor->getId() : null;
        $sql = "SELECT id, doctor_id, patient_id
                FROM appointments
                WHERE appointments.id = :aid";
//                " .(($doctor != null) ? "AND (doctor_id=:did)" : "AND (patient_id=:pid)");
        $params = [
            "aid" => $id,
//            "did" => $doc_id,
//            "pid" => $user->getId()
        ];
        $statement = $this->_em->getConnection()->executeQuery($sql, $params);
        $result = $statement->fetchAssociative();
        if ($result == null)
            return null;
        if ($result['doctor_id'] != $doc_id && $result['patient_id'] != $user->getId())
            throw new Exception('Nedostatočné práva', 403);
        return $statement->fetchAssociative();
    }
}