<?php

namespace App\Repository;


use App\Entity\FichaTecnica;
use App\Entity\TipoArtigo;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use CrosierSource\CrosierLibRadxBundle\Entity\CRM\Cliente;
use Doctrine\ORM\QueryBuilder;

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

    public function handleFrombyFilters(QueryBuilder $qb)
    {
        return $qb->from($this->getEntityClass(), 'e')
            ->leftJoin(TipoArtigo::class, 'ta', 'WITH', 'ta = e.tipoArtigo')
            ->leftJoin(Cliente::class, 'cliente', 'WITH', 'cliente = e.cliente');
    }


}
