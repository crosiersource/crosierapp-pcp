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
        if (!$insumo->codigo) {
            /** @var InsumoRepository $repoInsumo */
            $repoInsumo = $this->getDoctrine()->getRepository(Insumo::class);
            $insumo->codigo = ($repoInsumo->findProximoCodigo());
        }

        $adicionarPreco = true;
        if ($insumo->getId()) {
            $adicionarPreco = $insumo->precoCusto !== $insumo->getPrecoAtual()->precoCusto;
        }

        $agora = new \DateTime();
        
        if ($adicionarPreco) {
            $insumo->dtCusto = $agora;
            $insumoPrecos = $insumo->getPrecos();
            foreach ($insumoPrecos as $preco) {
                $preco->atual = false;
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
            $insumoPreco->precoCusto = $insumo->precoCusto;
            $insumoPreco->dtCusto = $agora;

            // para resetar o precoAtual
            $insumo->precoAtual = (null);
            $this->handleSavingEntityId($insumoPreco);
            $insumo->getPrecos()->add($insumoPreco);
            $insumo->getPrecoAtual();
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
