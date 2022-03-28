<?php

namespace App\Repository;

use App\Entity\Appointments;
use App\Entity\Doctors;
use App\Entity\Users;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\Response;

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
        $params = ["aid" => $id];
        $statement = $this->_em->getConnection()->executeQuery($sql, $params);
        $result = $statement->fetchAssociative();
        if ($result == null)
            return null;
        if ($result['doctor_id'] != $doc_id && $result['patient_id'] != $user->getId())
            throw new Exception('Nedostatočné práva', Response::HTTP_FORBIDDEN);
        return $result;
    }

    public function getGeneratedDates(int $doctor_id, DateTime $from, DateTime $to) {
        $sql = "SELECT distinct date FROM appointments
                WHERE doctor_id = :did and date between :timefrom and :timeto";
        $params = [
            "did" => $doctor_id,
            "timefrom" => $from->format('Y-M-d'),
            "timeto" => $to->format('Y-M-d')
        ];
        $statement = $this->_em->getConnection()->executeQuery($sql, $params);
        $results =  $statement->fetchAllAssociative();

        $gd = [];
        foreach ($results as $result) {
            $gd[] = $result['date'];
        }

        return $gd;
    }

    public function getFreeDates(int $doctor_id, int $month, int $year) {
        $sql = "SELECT distinct date_part('day', date) as day FROM appointments
            WHERE doctor_id = :did
            AND date_part('month', date) = :month
            AND date_part('year', date) = :year
            AND patient_id is null
            AND date >= CURRENT_DATE + INTERVAL '1 day'";
        $params = [
            "did" => $doctor_id,
            "month" => $month,
            "year" => $year
        ];
        $statement = $this->_em->getConnection()->executeQuery($sql, $params);
        $results =  $statement->fetchAllAssociative();

        $fd = [];
        foreach ($results as $result) {
            $fd[] = intval($result['day']);
        }

        return $fd;
    }
}