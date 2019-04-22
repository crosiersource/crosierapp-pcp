<?php

namespace App\EntityHandler;

use App\Entity\Insumo;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para a entidade Insumo.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
class InsumoEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return Insumo::class;
    }
}