<?php

namespace App\Service;

use App\Entity\Categorie;

class Search
{
    /**
     * Variable de recherche
     *
     * @var string
     */
    public $string = '';

    /**
     * Tableau de Catégories
     *
     * @var Categorie[]
     */
    public $categories = [];
}