<?php

namespace App\EntityHandler;

use App\Entity\LoteProducao;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para a entidade LoteProducao.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
class LoteProducaoEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return LoteProducao::class;
    }

    /**
     * @param $entityId
     * @return mixed|void
     */
    public function beforeSave($loteProducao)
    {
        /** @var LoteProducao $loteProducao */
        if (!$loteProducao->codigo) {
            $proxCodigo = $this->getDoctrine()->getRepository(LoteProducao::class)->findProx('codigo');
            $loteProducao->codigo = ($proxCodigo);
        }
    }


}