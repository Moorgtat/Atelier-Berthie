<?php

namespace App\Classe;

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