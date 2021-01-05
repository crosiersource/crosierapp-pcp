<?php

namespace App\Business;

use App\Entity\FichaTecnica;
use App\Entity\FichaTecnicaItem;
use App\Entity\FichaTecnicaPreco;
use App\Entity\Insumo;
use App\EntityHandler\FichaTecnicaEntityHandler;
use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierEntityIdAPIClient;
use CrosierSource\CrosierLibBaseBundle\Utils\NumberUtils\DecimalUtils;
use CrosierSource\CrosierLibRadxBundle\Entity\CRM\Cliente;
use CrosierSource\CrosierLibRadxBundle\Repository\CRM\ClienteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaBusiness
{

    private CrosierEntityIdAPIClient $crosierEntityIdAPIClient;

    private FichaTecnicaEntityHandler $fichaTecnicaEntityHandler;

    private EntityManagerInterface $doctrine;

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
            $preco->setPrecoPrazo((float)$precoParams['precoPrazo'] ?? 0.0);
            $preco->setPrecoVista((float)$precoParams['precoVista'] ?? 0.0);
            $preco->setCoeficiente((float)$precoParams['coeficiente'] ?? 0.0);

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
            $unidade = $this->findUnidadeById($item->getInsumo()->getUnidadeProdutoId());
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
        $gradesTamanhosByPosicaoArray = $this->buildGradesTamanhosByPosicaoArray($fichaTecnica->getGradeId());
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
        $unidade = $this->findUnidadeById($item->getInsumo()->getUnidadeProdutoId());
        $array = [];
        for ($i = 1; $i <= 15; $i++) {
            $array[$i]['decimal'] = 0.0;
            $array[$i]['formatado'] = '-';
            foreach ($item->getQtdes() as $qtde) {
                $posicao = $this->findPosicaoByGradeTamanhoId($qtde->getGradeTamanhoId());
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

        /** @var ClienteRepository $repoCliente */
        $repoCliente = $this->doctrine->getRepository(Cliente::class);
        /** @var Cliente $instituicao */
        $instituicao = $repoCliente->find($instituicaoId);

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
            $conn = $this->doctrine->getConnection();
            $rsInstituicoes = $conn->fetchAllAssociative('SELECT id, nome FROM crm_cliente WHERE json_data->>"$.cliente_pcp" = \'S\' ORDER BY nome');

            $arrInstituicoes = [];
            $arrInstituicoes[] = ['id' => '', 'text' => '...'];
            foreach ($rsInstituicoes as $instituicao) {
                $arrInstituicoes[] = [
                    'id' => $instituicao['id'],
                    'text' => $instituicao['nome']
                ];
            }
            return $arrInstituicoes;
        });

        return json_encode($arrInstituicoes);
    }


    /**
     * @return array
     */
    public function findGrades(): array
    {

        $cache = new FilesystemAdapter();

        $rGrades = $cache->get('grades', function (ItemInterface $item) {
            $item->expiresAfter(3600);


            $conn = $this->doctrine->getConnection();
            $rsGrades = $conn->fetchAssociative('SELECT valor FROM cfg_app_config WHERE app_uuid = :appUUID AND chave = :chave',
                ['appUUID' => $_SERVER['CROSIERAPP_UUID'], 'chave' => 'grades_tamanhos.json']);
            $grades = json_decode($rsGrades['valor'], true);

            $rGrades = [];

            foreach ($grades as $grade) {
                $gradeId = $grade['gradeId'];
                $tamanhosArr = [];
                $tamanhos = $this->findTamanhosByGradeId($gradeId);
                foreach ($tamanhos as $tamanho) {
                    $tamanhosArr[] = $tamanho['tamanho'];
                }
                $tamanhosStr = str_pad($gradeId, 3, '0', STR_PAD_LEFT) . ' (' . implode('-', $tamanhosArr) . ')';
                $rGrades[$gradeId] = $tamanhosStr;
            }

            return $rGrades;
        });


        return $rGrades;
    }

    /**
     * @param int $gradeId
     * @return array|null
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function findTamanhosByGradeId(int $gradeId): ?array
    {
        $cache = new FilesystemAdapter();

        $grades = $cache->get('findTamanhosByGradeId_' . $gradeId, function (ItemInterface $item) use ($gradeId) {
            $item->expiresAfter(3600);

            $conn = $this->doctrine->getConnection();
            $rsGrades = $conn->fetchAssociative('SELECT valor FROM cfg_app_config WHERE app_uuid = :appUUID AND chave = :chave',
                ['appUUID' => $_SERVER['CROSIERAPP_UUID'], 'chave' => 'grades_tamanhos.json']);
            $grades = json_decode($rsGrades['valor'], true);

            foreach ($grades as $grade) {
                if ($grade['gradeId'] === $gradeId) {
                    return $grade['tamanhos'];
                }
            }
            return false;
        });
        return $grades;
    }


    /**
     * @param int $gradeId
     * @return array|null
     */
    public function findGradeTamanhoById(int $id): ?array
    {
        $cache = new FilesystemAdapter();

        $tamanho = $cache->get('findGradeTamanhoById_' . $id, function (ItemInterface $item) use ($id) {
            $item->expiresAfter(3600);

            $conn = $this->doctrine->getConnection();
            $rsGrades = $conn->fetchAssociative('SELECT valor FROM cfg_app_config WHERE app_uuid = :appUUID AND chave = :chave',
                ['appUUID' => $_SERVER['CROSIERAPP_UUID'], 'chave' => 'grades_tamanhos.json']);
            $grades = json_decode($rsGrades['valor'], true);

            foreach ($grades as $grade) {
                foreach ($grade['tamanhos'] as $tamanho) {
                    if ($tamanho['id'] === $id) {
                        return $tamanho;
                    }
                }
            }

            return null;
        });
        return $tamanho;
    }


    /**
     * @param int $gradeId
     * @param int $posicao
     * @return array|null
     */
    public function findTamanhoByGradeIdAndPosicao(int $gradeId, int $posicao): ?array
    {

        $cache = new FilesystemAdapter();

        $tamanho = $cache->get('findTamanhoByGradeIdAndPosicao_' . $gradeId . '-' . $posicao, function (ItemInterface $item) use ($gradeId, $posicao) {
            $item->expiresAfter(3600);

            $tamanhos = $this->findTamanhosByGradeId($gradeId);
            foreach ($tamanhos as $tamanho) {
                if ($tamanho['posicao'] === $posicao) {
                    return $tamanho;
                }
            }

            return null;
        });

        return $tamanho;
    }


    /**
     *
     * @param int $gradeId
     * @return array
     */
    public function buildGradesTamanhosByPosicaoArray(int $gradeId): array
    {
        $cache = new FilesystemAdapter();

        $gradesTamanhosByPosicaoArray = $cache->get('buildGradesTamanhosByPosicaoArray_' . $gradeId, function (ItemInterface $item) use ($gradeId) {
            $item->expiresAfter(3600);

            $tamanhos = $this->findTamanhosByGradeId($gradeId);
            $gradesTamanhosByPosicaoArray = [];

            for ($i = 1; $i <= 15; $i++) {
                foreach ($tamanhos as $tamanho) {
                    $gradesTamanhosByPosicaoArray[$i] = '-';
                    if ($i === $tamanho['posicao']) {
                        $gradesTamanhosByPosicaoArray[$tamanho['posicao']] = $tamanho['tamanho'];
                        break;
                    }
                }
            }
            return $gradesTamanhosByPosicaoArray;
        });

        return $gradesTamanhosByPosicaoArray;

    }

    /**
     *
     * @param int $gradeTamanhoId
     * @return int
     */
    public function findPosicaoByGradeTamanhoId(int $gradeTamanhoId): int
    {

        $cache = new FilesystemAdapter();

        $posicao = $cache->get('findPosicaoByGradeTamanhoId' . $gradeTamanhoId, function (ItemInterface $item) use ($gradeTamanhoId) {
            $item->expiresAfter(3600);

            $conn = $this->doctrine->getConnection();
            $rsGrades = $conn->fetchAssociative('SELECT valor FROM cfg_app_config WHERE app_uuid = :appUUID AND chave = :chave',
                ['appUUID' => $_SERVER['CROSIERAPP_UUID'], 'chave' => 'grades_tamanhos.json']);
            $grades = json_decode($rsGrades['valor'], true);

            foreach ($grades as $grade) {
                $gradeId = $grade['gradeId'];
                $tamanhos = $this->findTamanhosByGradeId($gradeId);
                foreach ($tamanhos as $tamanho) {
                    if ($tamanho['id'] === $gradeTamanhoId) {
                        return $tamanho['posicao'];
                    }
                }
            }

            return -1;
        });
        return $posicao;
    }


    /**
     * @return array
     */
    public function findUnidades(): array
    {

        $cache = new FilesystemAdapter();

        $rUnidades = $cache->get('unidades', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            $conn = $this->doctrine->getConnection();
            $rsGrades = $conn->fetchAssociative('SELECT valor FROM cfg_app_config WHERE app_uuid = :appUUID AND chave = :chave',
                ['appUUID' => $_SERVER['CROSIERAPP_UUID'], 'chave' => 'unidades.json']);
            return json_decode($rsGrades['valor'], true);
        });


        return $rUnidades;
    }


    /**
     * Encontra uma unidade por seu id no json UNIDADES.
     *
     * @param int $unidadeId
     * @return array|null
     */
    public function findUnidadeById(int $unidadeId): ?array
    {
        $cache = new FilesystemAdapter();

        $unidade = $cache->get('findUnidadeById' . $unidadeId, function (ItemInterface $item) use ($unidadeId) {
            $item->expiresAfter(3600);

            $conn = $this->doctrine->getConnection();
            $rsUnidades = $conn->fetchAssociative('SELECT valor FROM cfg_app_config WHERE app_uuid = :appUUID AND chave = :chave',
                ['appUUID' => $_SERVER['CROSIERAPP_UUID'], 'chave' => 'unidades.json']);
            $unidades = json_decode($rsUnidades['valor'], true);

            foreach ($unidades as $unidade) {
                if ($unidadeId === $unidade['id']) {
                    return $unidade;
                }

            }

            return null;
        });
        return $unidade;
    }


}
