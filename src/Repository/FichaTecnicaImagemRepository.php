<?php

namespace App\Repository;


use App\Entity\FichaTecnicaImagem;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class FichaTecnicaImagemRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return FichaTecnicaImagem::class;
    }

}
