<?php

namespace App\Repository;


use App\Entity\Insumo;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade Insumo.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class InsumoRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return Insumo::class;
    }


}
