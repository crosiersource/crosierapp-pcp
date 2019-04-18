<?php

namespace App\Repository;

use App\Entity\Coisa;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade Coisa.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class CoisaRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return Coisa::class;
    }


}
