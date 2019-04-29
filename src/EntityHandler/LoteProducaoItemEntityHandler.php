<?php

namespace App\EntityHandler;

use App\Entity\LoteProducaoItem;
use App\Entity\LoteProducaoItemQtde;
use CrosierSource\CrosierLibBaseBundle\APIClient\Base\PropAPIClient;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para a entidade LoteProducaoItem.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
class LoteProducaoItemEntityHandler extends EntityHandler
{

    /** @var PropAPIClient */
    private $propAPIClient;

    /**
     * @required
     * @param PropAPIClient $propAPIClient
     */
    public function setPropAPIClient(PropAPIClient $propAPIClient): void
    {
        $this->propAPIClient = $propAPIClient;
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
            $this->getDoctrine()->getEntityManager()->remove($qtde);
        }
        $item->getQtdes()->clear();
        $this->getDoctrine()->getEntityManager()->flush();


        foreach ($qtdes as $posicao => $qtde) {
            $qtde = (int)$qtde;
            if (!$qtde) continue;
            $tamanho = $this->propAPIClient->findTamanhoByGradeIdAndPosicao($item->getFichaTecnica()->getGradeId(), $posicao);
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