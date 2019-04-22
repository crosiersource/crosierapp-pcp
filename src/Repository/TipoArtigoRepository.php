<?php

namespace App\Repository;


use App\Entity\TipoArtigo;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade TipoArtigo.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class TipoArtigoRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return TipoArtigo::class;
    }


}
