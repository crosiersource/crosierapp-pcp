<?php

namespace App\EntityHandler;

use App\Business\PropBusiness;
use App\Entity\LoteProducaoItem;
use App\Entity\LoteProducaoItemQtde;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para a entidade LoteProducaoItem.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
class LoteProducaoItemEntityHandler extends EntityHandler
{

    /** @var PropBusiness */
    private $propBusiness;

    /**
     * @required
     * @param PropBusiness $propBusiness
     */
    public function setPropBusiness(PropBusiness $propBusiness): void
    {
        $this->propBusiness = $propBusiness;
    }


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

    public function handleSaveArrayQtdes(LoteProducaoItem $item, array $qtdes): void
    {

        foreach ($item->getQtdes() as $qtde) {
            $qtde->setLoteProducaoItem(null);
            $this->getDoctrine()->remove($qtde);
        }
        $item->getQtdes()->clear();
        $this->getDoctrine()->flush();


        foreach ($qtdes as $posicao => $qtde) {
            $qtde = (int)$qtde;
            if (!$qtde) continue;
            $tamanho = $this->propBusiness->findTamanhoByGradeIdAndPosicao($item->getFichaTecnica()->getGradeId(), $posicao);
            $gradeTamanhoId = $tamanho['id'];
            $lpiq = new LoteProducaoItemQtde();

            $lpiq
                ->setLoteProducaoItem($item)
                ->setGradeTamanhoId($gradeTamanhoId)
                ->setQtde($qtde);
            $this->handleSavingEntityId($lpiq);
            $item->getQtdes()->add($lpiq);

        }
    }


}