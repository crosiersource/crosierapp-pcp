<?php

namespace App\Business;

use App\Entity\FichaTecnica;
use App\Entity\FichaTecnicaItem;
use App\Entity\FichaTecnicaPreco;
use App\Entity\Insumo;
use App\EntityHandler\FichaTecnicaEntityHandler;
use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierEntityIdAPIClient;
use CrosierSource\CrosierLibBaseBundle\Entity\Base\Pessoa;
use CrosierSource\CrosierLibBaseBundle\Repository\Base\PessoaRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\NumberUtils\DecimalUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class FichaTecnicaBusiness
 *
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaBusiness
{

    /** @var CrosierEntityIdAPIClient */
    private $crosierEntityIdAPIClient;

    /** @var FichaTecnicaEntityHandler */
    private $fichaTecnicaEntityHandler;

    /** @var PropBusiness */
    private $propBusiness;

    /** @var EntityManagerInterface */
    private $doctrine;

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
     * @param PropBusiness $propBusiness
     */
    public function setPropBusiness(PropBusiness $propBusiness): void
    {
        $this->propBusiness = $propBusiness;
    }

    /**
     * @required
     * @param EntityManagerInterface $doctrine
     */
    public function setDoctrine(EntityManagerInterface $doctrine): void
    {
        $this->doctrine = $doctrine;
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
                    $insumosArray[$c]['totais'][$i]['decimal'] = 0.0;
                }
            }
            $unidade = $this->propBusiness->findUnidadeById($item->getInsumo()->getUnidadeProdutoId());
            $item->casasDecimais = $unidade['casasDecimais'];
            $item->unidade = $unidade['label'];
            $insumosArray[$c]['itens'][] = $item;
            $qtdesTamanhosArray = $item->getQtdesTamanhosArray();
            for ($i = 1; $i <= 15; $i++) {
                $precoCustoAtual = $item->getInsumo()->getPrecoAtual()->getPrecoCusto() ?? 0.0;

                $total = (float)bcmul($qtdesTamanhosArray[$i]['decimal'], $precoCustoAtual, 3);

                $insumosArray[$c]['totais'][$i]['decimal'] = (float)bcadd($insumosArray[$c]['totais'][$i]['decimal'], $total, 3);
                $tSoma = $insumosArray[$c]['totais'][$i]['decimal'];
                $insumosArray[$c]['totais'][$i]['formatado'] = $tSoma > 0 ? number_format($tSoma, 2, ',', '.') : '-';

                $totalGlobal[$i] = (float)bcadd($totalGlobal[$i], $total, 3);
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
            $totalGlobal[$i] = $tg > 0 ? number_format($tg, 2, ',', '.') : '-';
        }

//        $this->formatarDecimaisInsumosArray($insumosArray);
        return ['insumos' => $insumosArray, 'totalGlobal' => $totalGlobal];
    }

    /**
     * Constrói o array de qtdes/tamanhos para todos os itens da fichaTecnica.
     *
     * @param FichaTecnica $fichaTecnica
     */
    public function buildQtdesTamanhosArray(FichaTecnica $fichaTecnica): void
    {
        $gradesTamanhosByPosicaoArray = $this->propBusiness->buildGradesTamanhosByPosicaoArray($fichaTecnica->getGradeId());
        $fichaTecnica->setGradesTamanhosByPosicaoArray($gradesTamanhosByPosicaoArray);
        foreach ($fichaTecnica->getItens() as $item) {
            $this->buildItemQtdesTamanhosByPosicaoArray($item);
        }
    }

//    private function formatarDecimaisInsumosArray(array &$insumosArray) {
//        foreach ($insumosArray as &$item) {
//            foreach ($item['totais'] as &$total) {
//                $total = $total > 0 ? number_format($total, 3, ',', '.') : '-';
//            }
//            /** @var FichaTecnicaItem $fti */
//            foreach ($item['itens'] as &$fti) {
//                $unidade = $this->propBusiness->findUnidadeById($fti->getInsumo()->getUnidadeProdutoId());
//                $qtdesTamanhosArray = $fti->getQtdesTamanhosArray();
//                foreach ($qtdesTamanhosArray as $i => $iValue) {
//                    $qtdesTamanhosArray[$i] = $iValue > 0 ? number_format($iValue, $unidade['casasDecimais'], ',', '.') : '-';
//                }
//                $fti->setQtdesTamanhosArray($qtdesTamanhosArray);
//            }
//
//        }
//    }

    /**
     * Constrói o array de qtdes/tamanhos para o item.
     *
     * @param FichaTecnicaItem $item
     */
    public function buildItemQtdesTamanhosByPosicaoArray(FichaTecnicaItem $item): void
    {
        $unidade = $this->propBusiness->findUnidadeById($item->getInsumo()->getUnidadeProdutoId());
        $array = [];
        for ($i = 1; $i <= 15; $i++) {
            $array[$i]['decimal'] = 0.0;
            $array[$i]['formatado'] = '-';
            foreach ($item->getQtdes() as $qtde) {
                $posicao = $this->propBusiness->findPosicaoByGradeTamanhoId($qtde->getGradeTamanhoId());
                if ($posicao === $i) {

                    $array[$i]['decimal'] = (float)$qtde->getQtde();
                    $array[$i]['formatado'] = $array[$i]['decimal'] > 0 ? number_format($array[$i]['decimal'], $unidade['casasDecimais'], ',', '.') : '-';

                }
            }
        }
        $item->setQtdesTamanhosArray($array);
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
            = $this->totaisAsFloat($totalGlobal);

        if ($vPos1 || $vPos2 || $vPos3) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $tams = $pos1 . '-' . $pos2 . '-' . $pos3;
            $preco->setDescricao('TAM ' . $tams);

            $precoMedio = $vPos3 ?: $vPos2 ?: $vPos1;

            $preco->setPrecoCusto($precoMedio);

            $fichaTecnica->getPrecos()->add($preco);
        }

        if ($vPos4 || $vPos5) {

            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);

            $tams = $pos4 . '-' . $pos5;
            $preco->setDescricao('TAM ' . $tams);

            $precoMedio = $vPos5 ?: $vPos4;
            $preco->setPrecoCusto($precoMedio);

            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos6 || $vPos7 || $vPos8) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);

            $tams = $pos6 . '-' . $pos7 . '-' . $pos8;
            $preco->setDescricao('TAM ' . $tams);

            $precoMedio = $vPos8 ?: $vPos7 ?: $vPos6;
            $preco->setPrecoCusto($precoMedio);

            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos9 || $vPos10 || $vPos11) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);

            $tams = $pos9 . '-' . $pos10 . '-' . $pos11;
            $preco->setDescricao('TAM ' . $tams);

            // preferencialmente pega o M->-> caso seja nulo, pega o G-> Por último o P->
            $precoMedio = $vPos10 ?: $vPos11 ?: $vPos9;

            $preco->setPrecoCusto($precoMedio);
            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos12) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $preco->setDescricao('TAM ' . $pos12);
            $preco->setPrecoCusto($vPos12);
            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos13) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $preco->setDescricao('TAM ' . $pos13);
            $preco->setPrecoCusto($vPos13);
            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos14) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $preco->setDescricao('TAM ' . $pos14);
            $preco->setPrecoCusto($vPos14);
            $fichaTecnica->getPrecos()->add($preco);
        }

        return $fichaTecnica;
    }

    /**
     * @param array $totalGlobal
     * @return array
     */
    private function totaisAsFloat(array $totalGlobal): array
    {
        $totaisAsFloat = [];
        ksort($totalGlobal);
        foreach ($totalGlobal as $t) {
            $totaisAsFloat[] = DecimalUtils::parseStr($t);
        }
        return $totaisAsFloat;
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
            = $this->totaisAsFloat($totalGlobal);


        if ($vPos1 || $vPos2 || $vPos3 || $vPos4) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $tams = $pos1 . '-' . $pos2 . '-' . $pos3 . '-' . $pos4;
            $preco->setDescricao('TAM ' . $tams);

            $precoMedio = $vPos4 ?: $vPos3 ?: $vPos2 ?: $vPos1;
            $preco->setPrecoCusto($precoMedio);

            $fichaTecnica->getPrecos()->add($preco);
        }

        if ($vPos5 || $vPos6 || $vPos7 || $vPos8) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $tams = $pos5 . '-' . $pos6 . '-' . $pos7 . '-' . $pos8;
            $preco->setDescricao('TAM ' . $tams);

            $precoMedio = $vPos8 ?: $vPos7 ?: $vPos6 ?: $vPos5;
            $preco->setPrecoCusto($precoMedio);

            $fichaTecnica->getPrecos()->add($preco);
        }

        if ($vPos9 || $vPos10 || $vPos11) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $tams = $pos9 . '-' . $pos10 . '-' . $pos11;
            $preco->setDescricao('TAM ' . $tams);

            $precoMedio = $vPos10 ?: $vPos11 ?: $vPos9;
            $preco->setPrecoCusto($precoMedio);

            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos12) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $preco->setDescricao('TAM ' . $pos12);
            $preco->setPrecoCusto($vPos12);
            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos13) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $preco->setDescricao('TAM ' . $pos13);
            $preco->setPrecoCusto($vPos13);
            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos14) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $preco->setDescricao('TAM ' . $pos14);
            $preco->setPrecoCusto($vPos14);
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
        [$pos0, $pos1, $pos2, $pos3, $pos4, $pos5, $pos6, $pos7, $pos8, $pos9, $pos10, $pos11, $pos12, $pos13, $pos14, $pos15]
            = $gradesTamanhosByPosicaoArray;

        $totalGlobal[0] = null; //rta para poder usar o list() com array sem indice 0
        [$vPos0, $vPos1, $vPos2, $vPos3, $vPos4, $vPos5, $vPos6, $vPos7, $vPos8, $vPos9, $vPos10, $vPos11, $vPos12, $vPos13, $vPos14, $vPos15]
            = $this->totaisAsFloat($totalGlobal);

        if ($vPos1 || $vPos2 || $vPos3 || $vPos4) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $tams = $pos1 . '-' . $pos2 . '-' . $pos3 . '-' . $pos4;
            $preco->setDescricao('TAM ' . $tams);

            $precoMedio = $vPos4 ?: $vPos3 ?: $vPos2 ?: $vPos1;
            $preco->setPrecoCusto($precoMedio);

            $fichaTecnica->getPrecos()->add($preco);
        }

        if ($vPos5 || $vPos6 || $vPos7) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $tams = $pos5 . '-' . $pos6 . '-' . $pos7;
            $preco->setDescricao('TAM ' . $tams);

            $precoMedio = $vPos7 ?: $vPos6 ?: $vPos5;
            $preco->setPrecoCusto($precoMedio);

            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos8 || $vPos9 || $vPos10 || $vPos11) {
            $preco = new FichaTecnicaPreco();

            $preco->setFichaTecnica($fichaTecnica);
            $tams = $pos8 . '-' . $pos9 . '-' . $pos10 . '-' . $pos11;
            $preco->setDescricao('TAM ' . $tams);

            $precoMedio = $vPos10 ?: $vPos11 ?: $vPos9 ?: $vPos8;
            $preco->setPrecoCusto($precoMedio);

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
            $preco->setPrecoCusto($precoMedio);

            $fichaTecnica->getPrecos()->add($preco);
        }

        return $fichaTecnica;
    }


    /**
     * @param FichaTecnica $fichaTecnicaOrigem
     * @param int $instituicaoId
     * @param string $novaDescricao
     * @return FichaTecnica
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     */
    public function clonar(FichaTecnica $fichaTecnicaOrigem, int $instituicaoId, string $novaDescricao): FichaTecnica
    {

        $this->fichaTecnicaEntityHandler->getDoctrine()->beginTransaction();

        $novaFichaTecnica = clone $fichaTecnicaOrigem;
        $novaFichaTecnica->setDescricao($novaDescricao);

        /** @var PessoaRepository $repoPessoa */
        $repoPessoa = $this->doctrine->getRepository(Pessoa::class);
        /** @var Pessoa $instituicao */
        $instituicao = $repoPessoa->find($instituicaoId);

        $novaFichaTecnica->setInstituicao($instituicao);

        /** @var FichaTecnica $novaFichaTecnica */
        $novaFichaTecnica = $this->fichaTecnicaEntityHandler->save($novaFichaTecnica);

        $this->fichaTecnicaEntityHandler->getDoctrine()->commit();

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


    public function buildInstituicoesSelect2()
    {
        $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache');

        $arrInstituicoes = $cache->get('buildInstituicoesSelect2', function (ItemInterface $item) {

            /** @var PessoaRepository $repoPessoa */
            $repoPessoa = $this->doctrine->getRepository(Pessoa::class);

            $instituicoes = $repoPessoa->findByFiltersSimpl([['categ.descricao', 'LIKE', 'CLIENTE_PCP']], null, 0, -1);

            uasort($instituicoes, function ($a, $b) {
                /** @var Pessoa $a */
                /** @var Pessoa $b */
                return strcasecmp(trim($a->getNomeMontado()), trim($b->getNomeMontado()));
            });
            $arrInstituicoes = [];
            $arrInstituicoes[] = ['id' => '', 'text' => '...'];
            /** @var Pessoa $instituicao */
            foreach ($instituicoes as $instituicao) {
                $arrInstituicoes[] = ['id' => $instituicao->getId(), 'text' => $instituicao->getNomeMontado()];
            }
            return $arrInstituicoes;
        });

        return json_encode($arrInstituicoes);
    }


}