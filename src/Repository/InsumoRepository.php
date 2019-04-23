<?php

namespace App\Repository;


use App\Entity\Insumo;
use App\Entity\TipoInsumo;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Repository para a entidade Insumo.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class InsumoRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return Insumo::class;
    }

    public function handleFrombyFilters(QueryBuilder $qb)
    {
        return $qb->from($this->getEntityClass(), 'e')
            ->leftJoin(TipoInsumo::class, 'ti', 'WITH', 'ti = e.tipoInsumo');
    }

    public function findProximoCodigo() {
        $dql = "SELECT (max(e.codigo) + 1) as prox_codigo FROM App\Entity\Insumo e";
        return $this->getEntityManager()->createQuery($dql)->getResult()[0]['prox_codigo'];
    }


}
