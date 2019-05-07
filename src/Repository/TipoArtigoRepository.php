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


    /**
     * Encontra todos os tipos de artigos da instituição
     * @param int $instituicaoId
     * @return array
     */
    public function findByInstituicao(int $instituicaoId): array {
        $dql = 'SELECT ta FROM App\Entity\TipoArtigo ta JOIN App\Entity\FichaTecnica ft WITH ft.tipoArtigo = ta WHERE ft.pessoaId = :instituicaoId GROUP BY ta.id ORDER BY ta.descricao';
        $qry = $this->getEntityManager()->createQuery($dql);
        $qry->setParameter('instituicaoId', $instituicaoId);
        return $qry->getResult();
    }

}
