<?php

namespace App\EntityHandler;

use App\Entity\FichaTecnicaItem;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para a entidade FichaTecnicaItem.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaItemEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return FichaTecnicaItem::class;
    }
}