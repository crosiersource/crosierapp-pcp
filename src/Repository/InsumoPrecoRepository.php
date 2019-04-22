<?php

namespace App\Repository;


use App\Entity\InsumoPreco;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade InsumoPreco.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class InsumoPrecoRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return InsumoPreco::class;
    }


}
