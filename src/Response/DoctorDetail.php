<?php

namespace App\Response;

use App\Entity\Doctors;

class DoctorDetail extends DoctorFavorite
{

    public array $schedules;

    public function __construct(Doctors $doctor, bool $is_favorite, array $schedules)
    {
        parent::__construct($doctor, $is_favorite);
        $this->schedules = $schedules;
    }
}