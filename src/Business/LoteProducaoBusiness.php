<?php

namespace App\Business;

use App\Entity\LoteProducao;
use App\Entity\LoteProducaoItem;
use App\Entity\LoteProducaoItemQtde;

/**
 * Class LoteProducaoBusiness
 */
class LoteProducaoBusiness
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
            /** @var LoteProducaoItemQtde $qtde */
            foreach ($item->getQtdes() as $qtde) {
                $posicao = $this->fichaTecnicaBusiness->findPosicaoByGradeTamanhoId($qtde->gradeTamanhoId);
                if ($posicao === $i) {
                    $array[$i] = $qtde->qtde;
                }
            }
        }

        $gradesTamanhosByPosicaoArray = $this->fichaTecnicaBusiness->buildGradesTamanhosByPosicaoArray($item->fichaTecnica->gradeId);
        $item->fichaTecnica->setGradesTamanhosByPosicaoArray($gradesTamanhosByPosicaoArray);

        $item->setQtdesTamanhosArray($array);
    }


    /**
     * @param LoteProducao $loteProducao
     * @return array
     */
    public function buildDadosPorTipoInsumo(LoteProducao $loteProducao): array
    {
        $itens = $loteProducao->getItens()->toArray();


        uasort($itens, function ($a, $b) {
            return strcasecmp($a->get['tipoInsumo'], $b['tipoInsumo']);
        });

        $tipoInsumo = null;

        $dados = [];
        foreach ($itens as $item) {
            if ($tipoInsumo !== $item['tipoInsumo']) {
                $tipoInsumo = $item['tipoInsumo'];
            }
            $dados[$tipoInsumo][] = $item;
        }

        return $dados;
    }

}
