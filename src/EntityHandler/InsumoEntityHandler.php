<?php

namespace App\EntityHandler;

use App\Entity\Insumo;
use App\Entity\InsumoPreco;
use App\Repository\InsumoRepository;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

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
            $insumo->codigo = ($repoInsumo->findProximoCodigo());
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

                if ($insumo->getPrecoAtual()->dtCusto->format('Y-m-d') === $precoAtual['dt_custo'] &&
                    $insumo->getPrecoAtual()->precoCusto === (float)$precoAtual['preco_custo']) {
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
            $insumoPreco->insumo = ($insumo);
            $insumoPreco->atual = (true);
            $insumoPreco->coeficiente = (0);
            $insumoPreco->custoFinanceiro = (0);
            $insumoPreco->custoOperacional = (0);
            $insumoPreco->fornecedorId = (null);
            $insumoPreco->margem = (0);
            $insumoPreco->prazo = (0);
            $insumoPreco->precoVista = (0);
            $insumoPreco->precoPrazo = (0);
            if ($insumo->getPrecoAtual()) {
                if ($insumo->getPrecoAtual()->precoCusto) {
                    $insumoPreco->precoCusto = ($insumo->getPrecoAtual()->precoCusto);
                }
                if ($insumo->getPrecoAtual()->dtCusto) {
                    $insumoPreco->dtCusto = (clone $insumo->getPrecoAtual()->dtCusto);
                }
            }

            // para resetar o precoAtual
            $insumo->precoAtual = (null);
            $this->handleSavingEntityId($insumoPreco);
            $insumo->getPrecos()->add($insumoPreco);
            $insumo->getPrecoAtual();
        }

        if ($insumo->getPrecoAtual()) {
            if ($insumo->getPrecoAtual()->precoCusto) {
                $insumo->jsonData['preco_custo'] = $insumo->getPrecoAtual()->precoCusto;
            }
            if ($insumo->getPrecoAtual()->dtCusto) {
                $insumo->jsonData['dt_custo'] = $insumo->getPrecoAtual()->dtCusto->format('Y-m-d');
            }
        }

    }

    
    public function delete($insumo)
    {
        $insumo->jsonData['visivel'] = 'N';
        $this->save($insumo);
    }

    /**
     * @param $entityId
     */
    public function afterSave($entityId)
    {
        $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);
        $cache->clear();
    }


}
