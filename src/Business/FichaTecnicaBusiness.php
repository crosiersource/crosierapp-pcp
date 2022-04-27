<?php

namespace App\Business;

use App\Entity\FichaTecnica;
use App\Entity\FichaTecnicaItem;
use App\Entity\FichaTecnicaItemQtde;
use App\Entity\FichaTecnicaPreco;
use App\Entity\Insumo;
use App\EntityHandler\FichaTecnicaEntityHandler;
use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierEntityIdAPIClient;
use CrosierSource\CrosierLibBaseBundle\Utils\NumberUtils\DecimalUtils;
use CrosierSource\CrosierLibRadxBundle\Business\Estoque\CalculoPreco;
use CrosierSource\CrosierLibRadxBundle\Entity\CRM\Cliente;
use CrosierSource\CrosierLibRadxBundle\Entity\Estoque\Unidade;
use CrosierSource\CrosierLibRadxBundle\Repository\CRM\ClienteRepository;
use CrosierSource\CrosierLibRadxBundle\Repository\Estoque\UnidadeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaBusiness
{

    private FichaTecnicaEntityHandler $fichaTecnicaEntityHandler;

    private EntityManagerInterface $doctrine;

    private CalculoPreco $calculoPreco;
    
    /**
     * @required
     * @param CalculoPreco $calculoPreco
     */
    public function setCalculoPreco(CalculoPreco $calculoPreco): void
    {
        $this->calculoPreco = $calculoPreco;
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

        if ($fichaTecnica->modoCalculo === 'MODO_1') {
            $fichaTecnica = $this->calcularModo1($fichaTecnica, $insumosArray['totalGlobal']);
        } elseif ($fichaTecnica->modoCalculo === 'MODO_2') {
            $fichaTecnica = $this->calcularModo2($fichaTecnica, $insumosArray['totalGlobal']);
        } elseif ($fichaTecnica->modoCalculo === 'MODO_3') {
            $fichaTecnica = $this->calcularModo3($fichaTecnica, $insumosArray['totalGlobal']);
        }

        /** @var FichaTecnicaPreco $preco */
        foreach ($fichaTecnica->getPrecos() as $preco) {
            $preco->dtCusto = new \DateTime();

            $preco->custoOperacional = ($fichaTecnica->custoOperacionalPadrao);
            $preco->custoFinanceiro = ($fichaTecnica->custoFinanceiroPadrao);
            $preco->margem = ($fichaTecnica->margemPadrao);
            $preco->prazo = ($fichaTecnica->prazoPadrao);

            $precoParams = [
                'prazo' => $preco->prazo,
                'margem' => $preco->margem,
                'custoOperacional' => $preco->custoOperacional,
                'custoFinanceiro' => $preco->custoFinanceiro,
                'precoCusto' => $preco->precoCusto,
                'precoPrazo' => 0.0,
                'precoVista' => 0.0,
                'coeficiente' => 0.0,
            ];
            if ($preco->precoCusto) {
                $this->calculoPreco->calcularPreco($precoParams);
            }
            $preco->precoPrazo = ((float)($precoParams['precoPrazo'] ?? 0.0));
            $preco->precoVista = ((float)($precoParams['precoVista'] ?? 0.0));
            $preco->coeficiente = ((float)($precoParams['coeficiente'] ?? 0.0));

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
            return strcasecmp($a->insumo->tipoInsumo->descricao, $b->insumo->tipoInsumo->descricao);
        });

        $fichaTecnicaItens = new ArrayCollection(iterator_to_array($iterator));


        $insumosArray = [];
        $tipoInsumoDescricao_aux = null;

        $totalGlobal = [];
        for ($i = 1; $i <= 15; $i++) {
            $totalGlobal[$i] = 0.0;
        }

        $c = -1;
        
        /** @var UnidadeRepository $repoUnidade */
        $repoUnidade = $this->doctrine->getRepository(Unidade::class);

        /** @var FichaTecnicaItem $item */
        foreach ($fichaTecnicaItens as $item) {
            if ($item->insumo->tipoInsumo->descricao !== $tipoInsumoDescricao_aux) {
                $tipoInsumoDescricao_aux = $item->insumo->tipoInsumo->descricao;

                $insumosArray[++$c] = [
                    'tipoInsumo' => $tipoInsumoDescricao_aux,
                    'itens' => [],
                    'totais' => []
                ];
                for ($i = 1; $i <= 15; $i++) {
                    $insumosArray[$c]['totais'][$i]['decimal'] = 0.0;
                }
            }
            /** @var Unidade $unidade */
            $unidade = $repoUnidade->find($item->insumo->unidadeProdutoId);
            $item->casasDecimais = $unidade->casasDecimais;
            $item->unidade = $unidade->label;
            $insumosArray[$c]['itens'][] = $item;
            
            $qtdesTamanhosArray = $item->getQtdesTamanhosArray();
            for ($i = 1; $i <= 15; $i++) {
                $precoCustoAtual = $item->insumo->getPrecoAtual()->precoCusto ?? 0.0;

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
                return strcasecmp($a->insumo->descricao, $b->insumo->descricao);
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
        $gradesTamanhosByPosicaoArray = $this->buildGradesTamanhosByPosicaoArray($fichaTecnica->gradeId);
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
        /** @var UnidadeRepository $repoUnidade */
        $repoUnidade = $this->doctrine->getRepository(Unidade::class);
        
        /** @var Unidade $unidade */
        $unidade = $repoUnidade->find($item->insumo->unidadeProdutoId);
        $array = [];
        for ($i = 1; $i <= 15; $i++) {
            $array[$i]['decimal'] = 0.0;
            $array[$i]['formatado'] = '-';
            /** @var FichaTecnicaItemQtde $qtde */
            foreach ($item->getQtdes() as $qtde) {
                $posicao = $this->findPosicaoByGradeTamanhoId($qtde->gradeTamanhoId);
                if ($posicao === $i) {

                    $array[$i]['decimal'] = (float)$qtde->qtde;
                    $array[$i]['formatado'] = $array[$i]['decimal'] > 0 ? number_format($array[$i]['decimal'], $unidade->casasDecimais, ',', '.') : '-';

                }
            }
        }
        $item->setQtdesTamanhosArray($array);
    }

    /**
     * calças, jaquetas, bermudas, etc
     * 02-04-06 >> 06
     * 08-10 >> 10
     * 12-14-16 >> 14
     * P-M-G >> M
     * XG-SG-SS >> SG
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
            $preco->fichaTecnica = $fichaTecnica;
            
            $tams = $pos1 . '-' . $pos2 . '-' . $pos3;
            $preco->descricao = ('TAM ' . $tams);

            $precoMedio = $vPos3 ?: $vPos2 ?: $vPos1;

            $preco->precoCusto = ($precoMedio);
            $fichaTecnica->getPrecos()->add($preco);
        }

        if ($vPos4 || $vPos5) {
            $preco = new FichaTecnicaPreco();
            $preco->fichaTecnica = ($fichaTecnica);

            $tams = $pos4 . '-' . $pos5;
            $preco->descricao = ('TAM ' . $tams);

            $precoMedio = $vPos5 ?: $vPos4;
            
            $preco->precoCusto = ($precoMedio);
            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos6 || $vPos7 || $vPos8) {
            $preco = new FichaTecnicaPreco();
            $preco->fichaTecnica = ($fichaTecnica);

            $tams = $pos6 . '-' . $pos7 . '-' . $pos8;
            $preco->descricao = ('TAM ' . $tams);

            $precoMedio = $vPos7 ?: $vPos8 ?: $vPos6;
            
            $preco->precoCusto = ($precoMedio);
            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos9 || $vPos10 || $vPos11) {
            $preco = new FichaTecnicaPreco();
            $preco->fichaTecnica = ($fichaTecnica);

            $tams = $pos9 . '-' . $pos10 . '-' . $pos11;
            $preco->descricao = ('TAM ' . $tams);

            $precoMedio = $vPos10 ?: $vPos11 ?: $vPos9;

            $preco->precoCusto = ($precoMedio);
            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos12 || $vPos13 || $vPos14) {
            $preco = new FichaTecnicaPreco();
            $preco->fichaTecnica = ($fichaTecnica);

            $tams = $pos12 . '-' . $pos13 . '-' . $pos14;
            $preco->descricao = ('TAM ' . $tams);

            $precoMedio = $vPos13 ?: $vPos14 ?: $vPos12;

            $preco->precoCusto = ($precoMedio);
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
     * 02-08 >> 06
     * 10-16 >> 14
     * P-M-G >> M
     * XG-SG-SS >> SG
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

            $preco->fichaTecnica = ($fichaTecnica);
            $tams = $pos1 . '-' . $pos2 . '-' . $pos3 . '-' . $pos4;
            $preco->descricao = ('TAM ' . $tams);

            $precoMedio = $vPos3 ?: $vPos4 ?: $vPos2 ?: $vPos1;
            $preco->precoCusto = ($precoMedio);

            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos5 || $vPos6 || $vPos7 || $vPos8) {
            $preco = new FichaTecnicaPreco();

            $preco->fichaTecnica = ($fichaTecnica);
            $tams = $pos5 . '-' . $pos6 . '-' . $pos7 . '-' . $pos8;
            $preco->descricao = ('TAM ' . $tams);

            $precoMedio = $vPos7 ?: $vPos8 ?: $vPos6 ?: $vPos5;
            $preco->precoCusto = ($precoMedio);

            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos9 || $vPos10 || $vPos11) {
            $preco = new FichaTecnicaPreco();

            $preco->fichaTecnica = ($fichaTecnica);
            $tams = $pos9 . '-' . $pos10 . '-' . $pos11;
            $preco->descricao = ('TAM ' . $tams);

            $precoMedio = $vPos10 ?: $vPos11 ?: $vPos9;
            $preco->precoCusto = ($precoMedio);

            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos12 || $vPos13 || $vPos14) {
            $preco = new FichaTecnicaPreco();
            $preco->fichaTecnica = ($fichaTecnica);

            $tams = $pos12 . '-' . $pos13 . '-' . $pos14;
            $preco->descricao = ('TAM ' . $tams);

            $precoMedio = $vPos13 ?: $vPos14 ?: $vPos12;

            $preco->precoCusto = ($precoMedio);
            $fichaTecnica->getPrecos()->add($preco);
        }

        return $fichaTecnica;
    }

    /**
     * Modo "IPÊ" (ANTIGO)
     * 02-04-06-08: 06
     * 10-12-14: 12
     * PP-P-M-G: M
     * GG-EG-SS: EG
     * 
     * 02-08 >> 06
     * 10-14 >> 12
     * 16-P-M-G >> M
     * XG-SG-SS >> SG
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

            $preco->fichaTecnica = ($fichaTecnica);
            $tams = $pos1 . '-' . $pos2 . '-' . $pos3 . '-' . $pos4;
            $preco->descricao = ('TAM ' . $tams);

            $precoMedio = $vPos3 ?: $vPos4 ?: $vPos2 ?: $vPos1;
            $preco->precoCusto = ($precoMedio);

            $fichaTecnica->getPrecos()->add($preco);
        }

        if ($vPos5 || $vPos6 || $vPos7) {
            $preco = new FichaTecnicaPreco();

            $preco->fichaTecnica = ($fichaTecnica);
            $tams = $pos5 . '-' . $pos6 . '-' . $pos7;
            $preco->descricao = ('TAM ' . $tams);

            $precoMedio = $vPos6 ?: $vPos7 ?: $vPos5;
            $preco->precoCusto = ($precoMedio);

            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos8 || $vPos9 || $vPos10 || $vPos11) {
            $preco = new FichaTecnicaPreco();

            $preco->fichaTecnica = ($fichaTecnica);
            $tams = $pos8 . '-' . $pos9 . '-' . $pos10 . '-' . $pos11;
            $preco->descricao = ('TAM ' . $tams);

            $precoMedio = $vPos10 ?: $vPos11 ?: $vPos9 ?: $vPos8;
            $preco->precoCusto = ($precoMedio);

            $fichaTecnica->getPrecos()->add($preco);
        }
        if ($vPos12 || $vPos13 || $vPos14) {
            $preco = new FichaTecnicaPreco();
            $preco->fichaTecnica = ($fichaTecnica);

            $tams = $pos12 . '-' . $pos13 . '-' . $pos14;
            $preco->descricao = ('TAM ' . $tams);

            $precoMedio = $vPos13 ?: $vPos14 ?: $vPos12;

            $preco->precoCusto = ($precoMedio);
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
        $novaFichaTecnica->descricao = ($novaDescricao);

        /** @var ClienteRepository $repoCliente */
        $repoCliente = $this->doctrine->getRepository(Cliente::class);
        /** @var Cliente $instituicao */
        $instituicao = $repoCliente->find($instituicaoId);

        $novaFichaTecnica->instituicao = ($instituicao);

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

        /** @var FichaTecnicaItem $item */
        foreach ($itens as $item) {
            if ($item->insumo->getId() === $insumo->getId()) {
                throw new \LogicException('Insumo já existente na ficha técnica.');
            }
        }

        $fichaTecnicaItem = new FichaTecnicaItem();
        $fichaTecnicaItem
            ->fichaTecnica = ($fichaTecnica);
        $fichaTecnicaItem->insumo = ($insumo);
        $this->fichaTecnicaEntityHandler->handleSavingEntityId($fichaTecnicaItem);

        $fichaTecnica->getItens()->add($fichaTecnicaItem);
        
        return $this->fichaTecnicaEntityHandler->save($fichaTecnica);
    }


    public function buildInstituicoesSelect2()
    {
        $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);

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

        $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);

        return $cache->get('grades', function (ItemInterface $item) {
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
    }

    /**
     * @param int $gradeId
     * @return array|null
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function findTamanhosByGradeId(int $gradeId): ?array
    {
        $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);

        return $cache->get('findTamanhosByGradeId_' . $gradeId, function (ItemInterface $item) use ($gradeId) {
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
    }


    /**
     * @param int $gradeId
     * @return array|null
     */
    public function findGradeTamanhoById(int $id): ?array
    {
        $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);

        return $cache->get('findGradeTamanhoById_' . $id, function (ItemInterface $item) use ($id) {
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
    }


    /**
     * @param int $gradeId
     * @param int $posicao
     * @return array|null
     */
    public function findTamanhoByGradeIdAndPosicao(int $gradeId, int $posicao): ?array
    {

        $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);

        return $cache->get('findTamanhoByGradeIdAndPosicao_' . $gradeId . '-' . $posicao, function (ItemInterface $item) use ($gradeId, $posicao) {
            $item->expiresAfter(3600);

            $tamanhos = $this->findTamanhosByGradeId($gradeId);
            foreach ($tamanhos as $tamanho) {
                if ($tamanho['posicao'] === $posicao) {
                    return $tamanho;
                }
            }

            return null;
        });
    }


    /**
     *
     * @param int $gradeId
     * @return array
     */
    public function buildGradesTamanhosByPosicaoArray(int $gradeId): array
    {
        $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);

        return $cache->get('buildGradesTamanhosByPosicaoArray_' . $gradeId, function (ItemInterface $item) use ($gradeId) {
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

    }

    /**
     *
     * @param int $gradeTamanhoId
     * @return int
     */
    public function findPosicaoByGradeTamanhoId(int $gradeTamanhoId): int
    {

        $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);

        return $cache->get('findPosicaoByGradeTamanhoId' . $gradeTamanhoId, function (ItemInterface $item) use ($gradeTamanhoId) {
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
    }


}
