<?php

namespace App\EntityHandler;

use App\Entity\FichaTecnicaPreco;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para a entidade FichaTecnicaPreco.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaPrecoEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return FichaTecnicaPreco::class;
    }
}