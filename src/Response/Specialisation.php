<?php

namespace App\Response;

use App\Entity\Specialisations;

class Specialisation
{
    public int $id;

    public string $title;

    public function __construct(Specialisations $specialisations)
    {
        $this->id = $specialisations->getId();
        $this->title = $specialisations->getTitle();
    }
}