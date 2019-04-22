<?php

namespace App\EntityHandler;

use App\Entity\LoteProducaoItem;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para a entidade LoteProducaoItem.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
class LoteProducaoItemEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return LoteProducaoItem::class;
    }
}