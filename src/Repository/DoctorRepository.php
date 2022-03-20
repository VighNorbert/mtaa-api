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
        $sql = "SELECT d.id,d.specialisation_id,(ufd.patient_id is not null) AS is_favorite, u.id AS user_id
                FROM doctors d
                JOIN users u ON d.user_id=u.id
                LEFT JOIN user_fav_doctors ufd ON d.id = ufd.doctor_id AND ufd.deleted_at is null AND ufd.patient_id=:p
                WHERE 1=1
                ".(($params['name'] != null) ? "AND (u.name ILIKE :n OR u.surname ILIKE :n)" : "")
                 .(($params['specialisation'] != null) ? "AND (specialisation_id=:s)" : "")
                 .(($params['city'] != null) ? "AND (d.city ILIKE :c)" : "")
                 .(($params['only_favorites']) ? "AND (ufd.patient_id is not null)" : "").
                "LIMIT :l 
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
}