<?php

namespace App\Response;

use App\Entity\Doctors;

class DoctorDetail extends DoctorFavourite
{

    public array $schedules;

    public function __construct(Doctors $doctor, bool $is_favourite, array $schedules)
    {
        parent::__construct($doctor, $is_favourite);
        $this->schedules = $schedules;
    }
}