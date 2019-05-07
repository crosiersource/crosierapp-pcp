<?php

namespace App\EntityHandler;

use App\Entity\FichaTecnicaItem;
use App\Entity\FichaTecnicaItemQtde;
use CrosierSource\CrosierLibBaseBundle\APIClient\Base\PropAPIClient;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para a entidade FichaTecnicaItem.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaItemEntityHandler extends EntityHandler
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
        return FichaTecnicaItem::class;
    }

    public function handleSaveArrayQtdes(FichaTecnicaItem $item, array $qtdes): void
    {

        foreach ($item->getQtdes() as $qtde) {
            $qtde->setFichaTecnicaItem(null);
            $this->getDoctrine()->getEntityManager()->remove($qtde);
        }
        $item->getQtdes()->clear();
        $this->getDoctrine()->getEntityManager()->flush();

        $formatter = new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL);
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 3);

        foreach ($qtdes as $posicao => $qtde) {
            if (!$qtde) continue;
            $qtde = $formatter->parse($qtde);

            $tamanho = $this->propAPIClient->findTamanhoByGradeIdAndPosicao($item->getFichaTecnica()->getGradeId(), $posicao);
            $gradeTamanhoId = $tamanho['id'];
            $lpiq = new FichaTecnicaItemQtde();

            $lpiq
                ->setFichaTecnicaItem($item)
                ->setGradeTamanhoId($gradeTamanhoId)
                ->setQtde($qtde);
            $this->handleSavingEntityId($lpiq);
            $item->getQtdes()->add($lpiq);

        }
    }

}