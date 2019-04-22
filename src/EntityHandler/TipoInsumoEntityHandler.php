<?php

namespace App\EntityHandler;

use App\Entity\TipoInsumo;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para a entidade TipoInsumo.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
class TipoInsumoEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return TipoInsumo::class;
    }
}