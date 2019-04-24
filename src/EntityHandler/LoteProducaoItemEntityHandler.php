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

    public function beforeSave($loteProducaoItem)
    {
        /** @var LoteProducaoItem $loteProducaoItem */
        if (!$loteProducaoItem->getOrdem()) {
            $loteProducao = $loteProducaoItem->getLoteProducao();
            $maxOrdem = 0;
            foreach ($loteProducao->getItens() as $item) {
                $maxOrdem = $maxOrdem < $item->getOrdem() ? $item->getOrdem() : $maxOrdem;
            }
            $loteProducaoItem->setOrdem($maxOrdem + 1);
        }
    }


}