<?php

namespace App\Business;

use App\Entity\FichaTecnica;
use App\Entity\FichaTecnicaItem;
use App\Entity\FichaTecnicaPreco;
use App\Entity\Insumo;
use App\EntityHandler\FichaTecnicaEntityHandler;
use CrosierSource\CrosierLibBaseBundle\APIClient\Base\PessoaAPIClient;
use CrosierSource\CrosierLibBaseBundle\APIClient\Base\PropAPIClient;
use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierEntityIdAPIClient;
use CrosierSource\CrosierLibBaseBundle\Utils\NumberUtils\DecimalUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class FichaTecnicaBusiness
 *
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaBusiness
{

    /** @var PropAPIClient */
    private $propAPIClient;

    /** @var CrosierEntityIdAPIClient */
    private $crosierEntityIdAPIClient;

    /** @var FichaTecnicaEntityHandler */
    private $fichaTecnicaEntityHandler;

    /** @var PessoaAPIClient */
    private $pessoaAPIClient;


    /**
     * @required
     * @param PropAPIClient $propAPIClient
     */
    public function setPropAPIClient(PropAPIClient $propAPIClient): void
    {
        $this->propAPIClient = $propAPIClient;
    }

    /**
     * @required
     * @param CrosierEntityIdAPIClient $crosierEntityIdAPIClient
     */
    public function setGenericAPIClient(CrosierEntityIdAPIClient $crosierEntityIdAPIClient): void
    {
        $this->crosierEntityIdAPIClient = $crosierEntityIdAPIClient;
    }

    /**
     * @required
     * @param FichaTecnicaEntityHandler $fichaTecnicaEntityHandler
     */
    public function setFichaTecnicaEntityHandler(FichaTecnicaEntityHandler $fichaTecnicaEntityHandler): void
    {
        $this->fichaTecnicaEntityHandler = $fichaTecnicaEntityHandler;
    }

    /**
     * @required
     * @param PessoaAPIClient $pessoaAPIClient
     */
    public function setPessoaAPIClient(PessoaAPIClient $pessoaAPIClient): void
    {
        $this->pessoaAPIClient = $pessoaAPIClient;
    }


    /**
     * Constrói o array de qtdes/tamanhos para todos os itens da fichaTecnica.
     *
     * @param FichaTecnica $fichaTecnica
     */
    public function buildQtdesTamanhosArray(FichaTecnica $fichaTecnica): void
    {
        $gradesTamanhosByPosicaoArray = $this->propAPIClient->buildGradesTamanhosByPosicaoArray($fichaTecnica->getGradeId());
        $fichaTecnica->setGradesTamanhosByPosicaoArray($gradesTamanhosByPosicaoArray);
        foreach ($fichaTecnica->getItens() as $item) {
            $this->buildItemQtdesTamanhosByPosicaoArray($item);
        }
    }

    /**
     * Constrói o array de qtdes/tamanhos para o item.
     *
     * @param FichaTecnicaItem $item
     */
    public function buildItemQtdesTamanhosByPosicaoArray(FichaTecnicaItem $item): void
    {
        $array = [];
        for ($i = 1; $i <= 15; $i++) {
            $array[$i] = 0.000;
            foreach ($item->getQtdes() as $qtde) {
                $posicao = $this->propAPIClient->findPosicaoByGradeTamanhoId($qtde->getGradeTamanhoId());
                if ($posicao === $i) {
                    if ((float)$qtde->getQtde() > 0) {
                        $array[$i] = $qtde->getQtde();
                    }
                }
            }
        }
        $item->setQtdesTamanhosArray($array);
    }


    /**
     * @param FichaTecnica $fichaTecnica
     * @return array
     */
    public function buildInsumosArray(FichaTecnica $fichaTecnica): array
    {
        $this->buildQtdesTamanhosArray($fichaTecnica);
        $itens = $fichaTecnica->getItens();

        $iterator = $itens->getIterator();
        $iterator->uasort(function (FichaTecnicaItem $a, FichaTecnicaItem $b) {
            return strcasecmp($a->getInsumo()->getTipoInsumo()->getDescricao(), $b->getInsumo()->getTipoInsumo()->getDescricao());
        });

        $fichaTecnicaItens = new ArrayCollection(iterator_to_array($iterator));


        $insumosArray = [];
        $tipoInsumoDescricao_aux = null;

        $totalGlobal = [];
        for ($i = 1; $i <= 15; $i++) {
            $totalGlobal[$i] = 0.0;
        }

        $c = -1;

        /** @var FichaTecnicaItem $item */
        foreach ($fichaTecnicaItens as $item) {
            if ($item->getInsumo()->getTipoInsumo()->getDescricao() !== $tipoInsumoDescricao_aux) {
                $tipoInsumoDescricao_aux = $item->getInsumo()->getTipoInsumo()->getDescricao();

                $insumosArray[++$c] = [
                    'tipoInsumo' => $tipoInsumoDescricao_aux,
                    'itens' => [],
                    'totais' => []
                ];
                for ($i = 1; $i <= 15; $i++) {
                    $insumosArray[$c]['totais'][$i] = 0.0;
                }
            }
            $unidade = $this->propAPIClient->findUnidadeById($item->getInsumo()->getUnidadeProdutoId());
            $item->casasDecimais = $unidade['casasDecimais'];
            $item->unidade = $unidade['label'];
            $insumosArray[$c]['itens'][] = $item;
            $qtdesTamanhosArray = $item->getQtdesTamanhosArray();
            for ($i = 1; $i <= 15; $i++) {
                $precoCustoAtual = $item->getInsumo()->getPrecoAtual()->getPrecoCusto() ?? 0.0;

                $total = (float)bcmul($qtdesTamanhosArray[$i], $precoCustoAtual, 3);

                $insumosArray[$c]['totais'][$i] = (float) bcadd($insumosArray[$c]['totais'][$i], $total, 3);

                $totalGlobal[$i] = (float) bcadd($totalGlobal[$i], $total, 3);
            }
        }

        foreach ($insumosArray as &$r) {
            uasort($r['itens'], function ($a, $b) {
                /** @var FichaTecnicaItem $a */
                /** @var FichaTecnicaItem $b */
                return strcasecmp($a->getInsumo()->getDescricao(), $b->getInsumo()->getDescricao());
            });
        }

        foreach ($totalGlobal as $i => $tg) {
            $totalGlobal[$i] = $tg > 0 ? number_format($tg, 3, ',', '.') : '-';
        }

        $this->formatarDecimaisInsumosArray($insumosArray);
        return ['insumos' => $insumosArray, 'totalGlobal' => $totalGlobal];
    }

    private function formatarDecimaisInsumosArray(array &$insumosArray) {
        foreach ($insumosArray as &$item) {
            foreach ($item['totais'] as &$total) {
                $total = $total > 0 ? number_format($total, 3, ',', '.') : '-';
            }
            /** @var FichaTecnicaItem $fti */
            foreach ($item['itens'] as &$fti) {
                $unidade = $this->propAPIClient->findUnidadeById($fti->getInsumo()->getUnidadeProdutoId());
                $qtdesTamanhosArray = $fti->getQtdesTamanhosArray();
                foreach ($qtdesTamanhosArray as $i => $iValue) {
                    $qtdesTamanhosArray[$i] = $iValue > 0 ? number_format($iValue, $unidade['casasDecimais'], ',', '.') : '-';
                }
                $fti->setQtdesTamanhosArray($qtdesTamanhosArray);
            }

        }
    }

    /**
     * @param FichaTecnica $fichaTecnica
     * @return FichaTecnica
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     */
    public function calcularPrecos(FichaTecnica $fichaTecnica): FichaTecnica
    {
        $insumosArray = $this->buildInsumosArray($fichaTecnica);

        $fichaTecnica->getPrecos()->clear();
        $this->fichaTecnicaEntityHandler->save($fichaTecnica);

        if ($fichaTecnica->getModoCalculo() === 'MODO_1') {
            $fichaTecnica = $this->calcularModo1($fichaTecnica, $insumosArray['totalGlobal']);
        } elseif ($fichaTecnica->getModoCalculo() === 'MODO_2') {
            $fichaTecnica = $this->calcularModo2($fichaTecnica, $insumosArray['totalGlobal']);
        } elseif ($fichaTecnica->getModoCalculo() === 'MODO_3') {
            $fichaTecnica = $this->calcularModo3($fichaTecnica, $insumosArray['totalGlobal']);
        }

        /** @var FichaTecnicaPreco $preco */
        foreach ($fichaTecnica->getPrecos() as $preco) {
            $preco->setDtCusto(new \DateTime());

            $preco->setCustoOperacional($fichaTecnica->getCustoOperacionalPadrao());
            $preco->setCustoFinanceiro($fichaTecnica->getCustoFinanceiroPadrao());
            $preco->setMargem($fichaTecnica->getMargemPadrao());
            $preco->setPrazo($fichaTecnica->getPrazoPadrao());

            $precoParams = [
                'prazo' => $preco->getPrazo(),
                'margem' => $preco->getMargem(),
                'custoOperacional' => $preco->getCustoOperacional(),
                'custoFinanceiro' => $preco->getCustoFinanceiro(),
                'precoCusto' => $preco->getPrecoCusto(),
                'precoPrazo' => 0.0,
                'precoVista' => 0.0,
                'coeficiente' => 0.0,
            ];
            if ($preco->getPrecoCusto()) {
                $rPrecoParams = $this->crosierEntityIdAPIClient
                    ->setBaseURI($_SERVER['CROSIERAPPVENDEST_URL'])
                    ->get('/api/est/calcularPreco', $precoParams);
                $precoParams = json_decode($rPrecoParams, true);
            }
            $preco->setPrecoPrazo((float)$precoParams['precoPrazo']);
            $preco->setPrecoVista((float)$precoParams['precoVista']);
            $preco->setCoeficiente((float)$precoParams['coeficiente']);

            $this->fichaTecnicaEntityHandler->handleSavingEntityId($preco);
        }
        $this->fichaTecnicaEntityHandler->save($fichaTecnica);
        return $fichaTecnica;
    }

    /**
     * calças, jaquetas, bermudas, etc
     * 02-04-06 >> 06
     * 08-10 >> 10
     * 12-14-16 >> 16
     * P-M-G >> M
     * XG >> XG
     * SG >> SG
     * SS >> SS
     *
     * @param FichaTecnica $fichaTecnica
     * @param array $totalGlobal
     * @return FichaTecnica
     */
    private function calcularModo1(FichaTecnica $fichaTecnica, array $totalGlobal): FichaTecnica
    {
        $gradesTamanhosByPosicaoArray = $fichaTecnica->getGradesTamanhosByPosicaoArray();
        $gradesTamanhosByPosicaoArray[0] = null; //rta para poder usar o list() com array sem indice 0
        [$null, $pos1, $pos2, $pos3, $pos4, $pos5, $pos6, $pos7, $pos8, $pos9, $pos10, $pos11, $pos12, $pos13, $pos14, $pos15]
            = $gradesTamanhosByPosicaoArray;

        $totalGlobal[0] = null; //rta para poder usar o list() com array sem indice 0
        [$null, $vPos1, $vPos2, $vPos3, $vPos4, $vPos5, $vPos6, $vPos7, $vPos8, $vPos9, $vPos10, $vPos11, $vPos12, $vPos13, $vPos14, $vPos15]
            = $totalGlobal;

        if ($vPos1 || $vPos2 || $vPos3) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $tams = $pos1 . '-' . $pos2 . '-' . $pos3;
            $preco->setDescricao('TAM ' . $tams);

            $precoMedio = $vPos3 ?: $vPos2 ?: $vPos1;

            $preco->setPrecoCusto(DecimalUtils::parseStr($precoMedio));

            $fichaTecnica->getPrecos()->add($preco);
        }

        if ($vPos4 || $vPos5) {

            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);

            $tams = $pos4 . '-' . $pos5;
            $preco->setDescricao('TAM ' . $tams);

            $precoMedio = $vPos5 ?: $vPos4;
            $preco->setPrecoCusto(DecimalUtils::parseStr($precoMedio));

            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos6 || $vPos7 || $vPos8) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);

            $tams = $pos6 . '-' . $pos7 . '-' . $pos8;
            $preco->setDescricao('TAM ' . $tams);

            $precoMedio = $vPos8 ?: $vPos7 ?: $vPos6;
            $preco->setPrecoCusto(DecimalUtils::parseStr($precoMedio));

            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos9 || $vPos10 || $vPos11) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);

            $tams = $pos9 . '-' . $pos10 . '-' . $pos11;
            $preco->setDescricao('TAM ' . $tams);

            // preferencialmente pega o M->-> caso seja nulo, pega o G-> Por último o P->
            $precoMedio = $vPos10 ?: $vPos11 ?: $vPos9;

            $preco->setPrecoCusto(DecimalUtils::parseStr($precoMedio));
            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos12) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $preco->setDescricao('TAM ' . $pos12);
            $preco->setPrecoCusto(DecimalUtils::parseStr($vPos12));
            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos13) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $preco->setDescricao('TAM ' . $pos13);
            $preco->setPrecoCusto(DecimalUtils::parseStr($vPos13));
            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos14) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $preco->setDescricao('TAM ' . $pos14);
            $preco->setPrecoCusto(DecimalUtils::parseStr($vPos14));
            $fichaTecnica->getPrecos()->add($preco);
        }

        return $fichaTecnica;
    }

    /**
     * CAMISETAS
     * 02-08 >> 08
     * 10-16 >> 14
     * P-M-G >> M
     * XG >> XG
     * SG >> SG
     * SS >> SS
     *
     * @param FichaTecnica $fichaTecnica
     * @param array $totalGlobal
     * @return FichaTecnica
     */
    private function calcularModo2(FichaTecnica $fichaTecnica, array $totalGlobal): FichaTecnica
    {
        $gradesTamanhosByPosicaoArray = $fichaTecnica->getGradesTamanhosByPosicaoArray();
        $gradesTamanhosByPosicaoArray[0] = null; //rta para poder usar o list() com array sem indice 0
        [$null, $pos1, $pos2, $pos3, $pos4, $pos5, $pos6, $pos7, $pos8, $pos9, $pos10, $pos11, $pos12, $pos13, $pos14, $pos15]
            = $gradesTamanhosByPosicaoArray;

        $totalGlobal[0] = null; //rta para poder usar o list() com array sem indice 0
        [$null, $vPos1, $vPos2, $vPos3, $vPos4, $vPos5, $vPos6, $vPos7, $vPos8, $vPos9, $vPos10, $vPos11, $vPos12, $vPos13, $vPos14, $vPos15]
            = $totalGlobal;


        if ($vPos1 || $vPos2 || $vPos3 || $vPos4) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $tams = $pos1 . '-' . $pos2 . '-' . $pos3 . '-' . $pos4;
            $preco->setDescricao('TAM ' . $tams);

            $precoMedio = $vPos4 ?: $vPos3 ?: $vPos2 ?: $vPos1;
            $preco->setPrecoCusto(DecimalUtils::parseStr($precoMedio));

            $fichaTecnica->getPrecos()->add($preco);
        }

        if ($vPos5 || $vPos6 || $vPos7 || $vPos8) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $tams = $pos5 . '-' . $pos6 . '-' . $pos7 . '-' . $pos8;
            $preco->setDescricao('TAM ' . $tams);

            $precoMedio = $vPos8 ?: $vPos7 ?: $vPos6 ?: $vPos5;
            $preco->setPrecoCusto(DecimalUtils::parseStr($precoMedio));

            $fichaTecnica->getPrecos()->add($preco);
        }

        if ($vPos9 || $vPos10 || $vPos11) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $tams = $pos9 . '-' . $pos10 . '-' . $pos11;
            $preco->setDescricao('TAM ' . $tams);

            $precoMedio = $vPos10 ?: $vPos11 ?: $vPos9;
            $preco->setPrecoCusto(DecimalUtils::parseStr($precoMedio));

            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos12) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $preco->setDescricao('TAM ' . $pos12);
            $preco->setPrecoCusto(DecimalUtils::parseStr($vPos12));
            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos13) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $preco->setDescricao('TAM ' . $pos13);
            $preco->setPrecoCusto(DecimalUtils::parseStr($vPos13));
            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos14) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $preco->setDescricao('TAM ' . $pos14);
            $preco->setPrecoCusto(DecimalUtils::parseStr($vPos14));
            $fichaTecnica->getPrecos()->add($preco);
        }

        return $fichaTecnica;
    }

    /**
     * "IPÊ"
     * 08-06-04-02
     * 14-12-10
     * G-M-P-PP
     * SS-EG-GG
     *
     * @param FichaTecnica $fichaTecnica
     * @param array $totalGlobal
     * @return FichaTecnica
     */
    private function calcularModo3(FichaTecnica $fichaTecnica, array $totalGlobal): FichaTecnica
    {
        $gradesTamanhosByPosicaoArray = $fichaTecnica->getGradesTamanhosByPosicaoArray();
        $gradesTamanhosByPosicaoArray[0] = null; //rta para poder usar o list() com array sem indice 0
        [$null, $pos1, $pos2, $pos3, $pos4, $pos5, $pos6, $pos7, $pos8, $pos9, $pos10, $pos11, $pos12, $pos13, $pos14, $pos15]
            = $gradesTamanhosByPosicaoArray;

        $totalGlobal[0] = null; //rta para poder usar o list() com array sem indice 0
        [$null, $vPos1, $vPos2, $vPos3, $vPos4, $vPos5, $vPos6, $vPos7, $vPos8, $vPos9, $vPos10, $vPos11, $vPos12, $vPos13, $vPos14, $vPos15]
            = $totalGlobal;

        if ($vPos1 || $vPos2 || $vPos3 || $vPos4) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $tams = $pos1 . '-' . $pos2 . '-' . $pos3 . '-' . $pos4;
            $preco->setDescricao('TAM ' . $tams);

            $precoMedio = $vPos4 ?: $vPos3 ?: $vPos2 ?: $vPos1;
            $preco->setPrecoCusto(DecimalUtils::parseStr($precoMedio));

            $fichaTecnica->getPrecos()->add($preco);
        }

        if ($vPos5 || $vPos6 || $vPos7) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $tams = $pos5 . '-' . $pos6 . '-' . $pos7;
            $preco->setDescricao('TAM ' . $tams);

            $precoMedio = $vPos7 ?: $vPos6 ?: $vPos5;
            $preco->setPrecoCusto(DecimalUtils::parseStr($precoMedio));

            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos8 || $vPos9 || $vPos10 || $vPos11) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $tams = $pos8 . '-' . $pos9 . '-' . $pos10 . '-' . $pos11;
            $preco->setDescricao('TAM ' . $tams);

            $precoMedio = $vPos10 ?: $vPos11 ?: $vPos9 ?: $vPos8;
            $preco->setPrecoCusto(DecimalUtils::parseStr($precoMedio));

            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos12 || $vPos13 || $vPos14) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);

            $tams = '';

            if ($pos12) {
                $tams .= $pos12 . '-';
            }
            if ($pos13) {
                $tams .= $pos13 . '-';
            }
            if ($pos14) {
                $tams .= $pos14 . '-';
            }

            $tams = substr($tams, 0, -1);


            $preco->setDescricao('TAM ' . $tams);

            $precoMedio = $vPos14 ?: $vPos13 ?: $vPos12;
            $preco->setPrecoCusto(DecimalUtils::parseStr($precoMedio));

            $fichaTecnica->getPrecos()->add($preco);
        }

        return $fichaTecnica;
    }


    /**
     * @param FichaTecnica $fichaTecnicaOrigem
     * @param int $instituicao
     * @return FichaTecnica
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     */
    public function clonar(FichaTecnica $fichaTecnicaOrigem, int $instituicao, string $novaDescricao): FichaTecnica
    {

        $this->fichaTecnicaEntityHandler->getDoctrine()->getEntityManager()->beginTransaction();

        $novaFichaTecnica = clone $fichaTecnicaOrigem;
        $novaFichaTecnica->setDescricao($novaDescricao);
        $novaFichaTecnica->setPessoaId($instituicao);

        /** @var FichaTecnica $novaFichaTecnica */
        $novaFichaTecnica = $this->fichaTecnicaEntityHandler->save($novaFichaTecnica);

        $this->fichaTecnicaEntityHandler->getDoctrine()->getEntityManager()->commit();

        return $novaFichaTecnica;


    }

    /**
     * @param FichaTecnica $fichaTecnica
     * @param Insumo $insumo
     * @return FichaTecnica
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     */
    public function addInsumo(FichaTecnica $fichaTecnica, Insumo $insumo): FichaTecnica
    {
        $itens = $fichaTecnica->getItens();

        foreach ($itens as $item) {
            if ($item->getInsumo()->getId() === $insumo->getId()) {
                throw new \LogicException('Insumo já existente na ficha técnica.');
            }
        }

        $fichaTecnicaItem = new FichaTecnicaItem();
        $fichaTecnicaItem
            ->setFichaTecnica($fichaTecnica)
            ->setInsumo($insumo);
        $this->fichaTecnicaEntityHandler->handleSavingEntityId($fichaTecnicaItem);

        $fichaTecnica->getItens()->add($fichaTecnicaItem);
        /** @var FichaTecnica $rFichaTecnica */
        $rFichaTecnica = $this->fichaTecnicaEntityHandler->save($fichaTecnica);
        return $rFichaTecnica;
    }


    /**
     * @return false|string
     */
    public function buildInstituicoesSelect2()
    {
        $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache');

        $arrInstituicoes = $cache->get('buildInstituicoesSelect2', function (ItemInterface $item) {
            $instituicoes = $this->pessoaAPIClient->findByFilters([['categ.descricao', 'LIKE', 'CLIENTE_PCP']], 0, 99999999)['results'];

            uasort($instituicoes, function ($a, $b) {
                return strcasecmp($a['nomeMontado'], $b['nomeMontado']);
            });
            $arrInstituicoes = [];
            $arrInstituicoes[] = ['id' => '', 'text' => '...'];
            foreach ($instituicoes as $instituicao) {
                $arrInstituicoes[] = ['id' => $instituicao['id'], 'text' => $instituicao['nomeMontado']];
            }
            return $arrInstituicoes;
        });

        return json_encode($arrInstituicoes);
    }


}