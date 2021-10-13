<?php

namespace App\EntityHandler;

use App\Business\FichaTecnicaBusiness;
use App\Entity\FichaTecnicaItem;
use App\Entity\FichaTecnicaItemQtde;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para a entidade FichaTecnicaItem.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaItemEntityHandler extends EntityHandler
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

    /**
     * @param FichaTecnicaItem $fichaTecnicaItem
     */
    public function afterSave($fichaTecnicaItem)
    {
        $this->getDoctrine()->getConnection()
            ->update('prod_fichatecnica',
                ['updated' => (new \DateTime())->format('Y-m-d H:i:s')],
                ['id' => $fichaTecnicaItem->fichaTecnica->getId()]);
    }

    /**
     * @param FichaTecnicaItem $fichaTecnicaItem
     */
    public function afterDelete($fichaTecnicaItem)
    {
        $this->getDoctrine()->getConnection()
            ->update('prod_fichatecnica',
                ['updated' => (new \DateTime())->format('Y-m-d H:i:s')],
                ['id' => $fichaTecnicaItem->fichaTecnica->getId()]);
    }


    public function getEntityClass()
    {
        return FichaTecnicaItem::class;
    }

    public function handleSaveArrayQtdes(FichaTecnicaItem $item, array $qtdes): void
    {
        /** @var FichaTecnicaItemQtde $qtde */
        foreach ($item->getQtdes() as $qtde) {
            $qtde->fichaTecnicaItem = null;
            $this->getDoctrine()->remove($qtde);
        }
        $item->getQtdes()->clear();
        $this->getDoctrine()->flush();

        $formatter = new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL);
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 3);

        foreach ($qtdes as $posicao => $qtde) {
            if (!$qtde) continue;
            $qtde = $formatter->parse($qtde);

            $tamanho = $this->fichaTecnicaBusiness->findTamanhoByGradeIdAndPosicao($item->fichaTecnica->gradeId, $posicao);
            $gradeTamanhoId = $tamanho['id'];
            $lpiq = new FichaTecnicaItemQtde();

            $lpiq->fichaTecnicaItem = ($item);
            $lpiq->gradeTamanhoId = ($gradeTamanhoId);
            $lpiq->qtde = ($qtde);
            $this->handleSavingEntityId($lpiq);
            $item->getQtdes()->add($lpiq);
        }
    }

}
