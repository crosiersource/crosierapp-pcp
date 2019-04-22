<?php

namespace App\Repository;


use App\Entity\TipoInsumo;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade TipoInsumo.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class TipoInsumoRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return TipoInsumo::class;
    }


}
