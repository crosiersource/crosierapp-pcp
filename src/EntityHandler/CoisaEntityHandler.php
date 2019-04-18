<?php

namespace App\EntityHandler;

use App\Entity\Coisa;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para a entidade Coisa.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
class CoisaEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return Coisa::class;
    }
}