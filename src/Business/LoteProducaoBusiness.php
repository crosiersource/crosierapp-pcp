<?php

namespace App\Business;

use App\Entity\LoteProducao;
use App\Entity\LoteProducaoItem;
use CrosierSource\CrosierLibBaseBundle\APIClient\Base\PropAPIClient;

/**
 * Class LoteProducaoBusiness
 */
class LoteProducaoBusiness
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


    /**
     * Constrói o array de qtdes/tamanhos para todos os itens do lote.
     *
     * @param LoteProducao $lote
     */
    public function buildLoteQtdesTamanhosArray(LoteProducao $lote): void
    {
        foreach ($lote->getItens() as $item) {
            $this->buildItemQtdesTamanhosByPosicaoArray($item);
        }
    }

    /**
     * Constrói o array de qtdes/tamanhhos para o item.
     *
     * @param LoteProducaoItem $item
     */
    public function buildItemQtdesTamanhosByPosicaoArray(LoteProducaoItem $item): void
    {
        $array = [];
        for ($i = 1; $i <= 15; $i++) {
            $array[$i] = null;
            foreach ($item->getQtdes() as $qtde) {
                $posicao = $this->propAPIClient->findPosicaoByGradeTamanhoId($qtde->getGradeTamanhoId());
                if ($posicao === $i) {
                    $array[$i] = $qtde->getQtde();
                }
            }
        }

        $gradesTamanhosByPosicaoArray = $this->propAPIClient->buildGradesTamanhosByPosicaoArray($item->getFichaTecnica()->getGradeId());
        $item->getFichaTecnica()->setGradesTamanhosByPosicaoArray($gradesTamanhosByPosicaoArray);

        $item->setQtdesTamanhosArray($array);
    }

}