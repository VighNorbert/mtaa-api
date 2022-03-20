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
                LIMIT :l 
                OFFSET :o";
        $queryParams = [
            "p" => $user->getId(),
            "l" => $params['per_page'],
            "o" => $params['per_page']*($params['page']-1)
        ];
        $statement = $this->_em->getConnection()->executeQuery($sql, $queryParams);
        return $statement->fetchAllAssociative();
    }
}