<?php

namespace App\Repository;


use App\Entity\FichaTecnicaPreco;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade FichaTecnicaPreco.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class FichaTecnicaPrecoRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return FichaTecnicaPreco::class;
    }


}
