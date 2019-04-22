<?php

namespace App\EntityHandler;

use App\Entity\TipoArtigo;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para a entidade TipoArtigo.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
class TipoArtigoEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return TipoArtigo::class;
    }
}