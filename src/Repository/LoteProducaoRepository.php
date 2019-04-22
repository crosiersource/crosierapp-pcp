<?php

namespace App\Repository;


use App\Entity\LoteProducao;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade LoteProducao.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class LoteProducaoRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return LoteProducao::class;
    }


}
