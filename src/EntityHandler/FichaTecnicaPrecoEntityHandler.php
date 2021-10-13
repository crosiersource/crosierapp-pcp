<?php

namespace App\EntityHandler;

use App\Entity\FichaTecnicaPreco;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para a entidade FichaTecnicaPreco.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaPrecoEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return FichaTecnicaPreco::class;
    }

    /**
     * @param FichaTecnicaPreco $fichaTecnicaPreco
     */
    public function afterSave($fichaTecnicaPreco)
    {
        $this->getDoctrine()->getConnection()
            ->update('prod_fichatecnica',
                ['updated' => (new \DateTime())->format('Y-m-d H:i:s')],
                ['id' => $fichaTecnicaPreco->fichaTecnica->getId()]);
    }
    
    /**
     * @param FichaTecnicaPreco $fichaTecnicaPreco
     */
    public function afterDelete($fichaTecnicaPreco)
    {
        $this->getDoctrine()->getConnection()
            ->update('prod_fichatecnica',
                ['updated' => (new \DateTime())->format('Y-m-d H:i:s')],
                ['id' => $fichaTecnicaPreco->fichaTecnica->getId()]);
    }


}