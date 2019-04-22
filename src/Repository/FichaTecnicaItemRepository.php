<?php

namespace App\Repository;


use App\Entity\FichaTecnicaItem;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade FichaTecnicaItem.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class FichaTecnicaItemRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return FichaTecnicaItem::class;
    }


}
