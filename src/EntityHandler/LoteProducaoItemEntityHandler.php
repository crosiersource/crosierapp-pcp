<?php

namespace App\EntityHandler;

use App\Business\FichaTecnicaBusiness;
use App\Entity\LoteProducaoItem;
use App\Entity\LoteProducaoItemQtde;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * @author Carlos Eduardo Pauluk
 */
class LoteProducaoItemEntityHandler extends EntityHandler
{

    private FichaTecnicaBusiness $fichaTecnicaBusiness;

    /**
     * @required
     * @param FichaTecnicaBusiness $fichaTecnicaBusiness
     */
    public function setFichaTecnicaBusiness(FichaTecnicaBusiness $fichaTecnicaBusiness): void
    {
        $this->fichaTecnicaBusiness = $fichaTecnicaBusiness;
    }


    public function getEntityClass()
    {
        return LoteProducaoItem::class;
    }

    public function beforeSave($loteProducaoItem)
    {
        /** @var LoteProducaoItem $loteProducaoItem */
        if (!$loteProducaoItem->ordem) {
            $loteProducao = $loteProducaoItem->loteProducao;
            $maxOrdem = 0;
            foreach ($loteProducao->getItens() as $item) {
                $maxOrdem = $maxOrdem < $item->getOrdem() ? $item->getOrdem() : $maxOrdem;
            }
            $loteProducaoItem->ordem = ($maxOrdem + 1);
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
            $tamanho = $this->fichaTecnicaBusiness->findTamanhoByGradeIdAndPosicao($item->fichaTecnica->gradeId, $posicao);
            $gradeTamanhoId = $tamanho['id'];
            $lpiq = new LoteProducaoItemQtde();

            $lpiq->loteProducaoItem = ($item);
            $lpiq->gradeTamanhoId = ($gradeTamanhoId);
            $lpiq->qtde = ($qtde);
            $this->handleSavingEntityId($lpiq);
            $item->getQtdes()->add($lpiq);

        }
    }


}
