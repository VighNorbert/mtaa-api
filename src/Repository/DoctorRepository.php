<?php

namespace App\Repository;

use App\Entity\Doctors;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Doctors::class);
    }

    public function filterAll(array $params, Users $user): array
    {
        $sql = "SELECT d.id,d.specialisation_id,(ufd.patient_id is not null) AS is_favourite, u.id AS user_id
                FROM doctors d
                JOIN users u ON d.user_id=u.id
                LEFT JOIN user_fav_doctors ufd ON d.id = ufd.doctor_id AND ufd.deleted_at is null AND ufd.patient_id=:p
                WHERE 1=1
                ".(($params['name'] != null) ? " AND (u.name ILIKE :n OR u.surname ILIKE :n)" : "")
                 .(($params['specialisation'] != null) ? " AND (specialisation_id=:s)" : "")
                 .(($params['city'] != null) ? " AND (d.city ILIKE :c)" : "")
                 .(($params['only_favourites']) ? " AND (ufd.patient_id is not null)" : "").
                " ORDER BY u.surname asc
                LIMIT :l 
                OFFSET :o";
        $queryParams = [
            "n" => "%" . $params['name'] . "%",
            "s" => $params['specialisation'],
            "c" => "%" . $params['city'] . "%",
            "p" => $user->getId(),
            "l" => $params['per_page'],
            "o" => $params['per_page']*($params['page']-1)
        ];
        $statement = $this->_em->getConnection()->executeQuery($sql, $queryParams);
        return $statement->fetchAllAssociative();
    }

    public function findFavourite(Users $user, int $id): array {
        $sql = "SELECT (ufd.patient_id is not null) AS is_favourite
                FROM user_fav_doctors ufd
                WHERE ufd.patient_id=:p AND ufd.doctor_id=:id AND ufd.deleted_at is null";
        $queryParams = [
            "p" => $user->getId(),
            "id" => $id
        ];
        $statement = $this->_em->getConnection()->executeQuery($sql, $queryParams);
        return $statement->fetchAllAssociative();
    }

    public function filterSchedules(int $id) {
        $sql = "SELECT weekday, time_from, time_to
                FROM work_schedules w 
                WHERE doctor_id=:id AND ((current_timestamp between w.created_at AND w.deleted_at) OR (w.deleted_at is null AND w.created_at < current_timestamp))";
        $queryParams = [
            "id" => $id
        ];
        $statement = $this->_em->getConnection()->executeQuery($sql, $queryParams);
        return $statement->fetchAllAssociative();
    }

}