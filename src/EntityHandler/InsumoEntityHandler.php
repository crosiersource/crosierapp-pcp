<?php

namespace App\EntityHandler;

use App\Entity\Insumo;
use App\Repository\InsumoRepository;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

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
        /** @var Insumo $insumo */
        if (!$insumo->getCodigo()) {
            /** @var InsumoRepository $repoInsumo */
            $repoInsumo = $this->getDoctrine()->getRepository(Insumo::class);
            $insumo->setCodigo($repoInsumo->findProximoCodigo());
        }

    }


}