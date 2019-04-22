<?php

namespace App\Repository;


use App\Entity\FichaTecnica;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade FichaTecnica.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class FichaTecnicaRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return FichaTecnica::class;
    }


}
