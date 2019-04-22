<?php

namespace App\EntityHandler;

use App\Entity\InsumoPreco;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para a entidade InsumoPreco.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
class InsumoPrecoEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return InsumoPreco::class;
    }
}