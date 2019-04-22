<?php

namespace App\EntityHandler;

use App\Entity\FichaTecnica;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para a entidade FichaTecnica.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return FichaTecnica::class;
    }
}