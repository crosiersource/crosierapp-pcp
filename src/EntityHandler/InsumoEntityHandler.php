<?php

namespace App\EntityHandler;

use App\Entity\Insumo;
use App\Entity\InsumoPreco;
use App\Repository\InsumoRepository;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * EntityHandler para a entidade Insumo.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
class InsumoEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return Insumo::class;
    }

    public function beforeSave($insumo)
    {
        if (!isset($insumo->jsonData['visivel'])) {
            $insumo->jsonData['visivel'] = 'S';
        }
        /** @var Insumo $insumo */
        if (!$insumo->getCodigo()) {
            /** @var InsumoRepository $repoInsumo */
            $repoInsumo = $this->getDoctrine()->getRepository(Insumo::class);
            $insumo->setCodigo($repoInsumo->findProximoCodigo());
        }

        $adicionarPreco = true;
        if ($insumo->getId()) {
            $sql = 'SELECT dt_custo, preco_custo FROM prod_insumo_preco WHERE insumo_id = :insumoId AND atual IS TRUE';

            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('dt_custo', 'dt_custo');
            $rsm->addScalarResult('preco_custo', 'preco_custo');
            $query = $this->getDoctrine()->createNativeQuery($sql, $rsm);
            $query->setParameter('insumoId', $insumo->getId());
            $precoAtual = $query->getResult()[0] ?? null;

            if ($precoAtual && isset($precoAtual['dt_custo']) && isset($precoAtual['preco_custo'])) {

                if ($insumo->getPrecoAtual()->getDtCusto()->format('Y-m-d') === $precoAtual['dt_custo'] &&
                    $insumo->getPrecoAtual()->getPrecoCusto() === (float)$precoAtual['preco_custo']) {
                    $adicionarPreco = false;
                }
            }
        }

        if ($adicionarPreco) {
            $insumoPrecos = $insumo->getPrecos();
            foreach ($insumoPrecos as $preco) {
                $preco->setAtual(false);
            }

            $insumoPreco = new InsumoPreco();
            $insumoPreco->setInsumo($insumo);
            $insumoPreco->setAtual(true);
            $insumoPreco->setCoeficiente(0);
            $insumoPreco->setCustoFinanceiro(0);
            $insumoPreco->setCustoOperacional(0);
            $insumoPreco->setFornecedorId(null);
            $insumoPreco->setMargem(0);
            $insumoPreco->setPrazo(0);
            $insumoPreco->setPrecoVista(0);
            $insumoPreco->setPrecoPrazo(0);
            if ($insumo->getPrecoAtual()) {
                if ($insumo->getPrecoAtual()->getPrecoCusto()) {
                    $insumoPreco->setPrecoCusto($insumo->getPrecoAtual()->getPrecoCusto());
                }
                if ($insumo->getPrecoAtual()->getDtCusto()) {
                    $insumoPreco->setDtCusto(clone $insumo->getPrecoAtual()->getDtCusto());
                }
            }

            // para resetar o precoAtual
            $insumo->setPrecoAtual(null);
            $this->handleSavingEntityId($insumoPreco);
            $insumo->getPrecos()->add($insumoPreco);
            $insumo->getPrecoAtual();
        }

        if ($insumo->getPrecoAtual()) {
            if ($insumo->getPrecoAtual()->getPrecoCusto()) {
                $insumo->jsonData['preco_custo'] = $insumo->getPrecoAtual()->getPrecoCusto();
            }
            if ($insumo->getPrecoAtual()->getDtCusto()) {
                $insumo->jsonData['dt_custo'] = $insumo->getPrecoAtual()->getDtCusto()->format('Y-m-d');
            }
        }

    }

    
    public function delete($insumo)
    {
        $insumo->jsonData['visivel'] = 'N';
        $this->save($insumo);
    }


}
