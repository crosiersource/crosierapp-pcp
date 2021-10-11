<?php

namespace App\EntityHandler;

use App\Entity\TipoArtigo;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\ResultSetMapping;

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

    /**
     * @param $tipoArtigo
     * @return void
     * @throws ViewException
     */
    public function beforeSave(/** @var TipoArtigo $tipoArtigo */ $tipoArtigo)
    {
        if (!$tipoArtigo->getCodigo()) {
            try {
                $prox = $this->getDoctrine()
                    ->createNativeQuery('SELECT max(codigo)+1 as prox FROM prod_tipo_artigo', (new ResultSetMapping())->addScalarResult('prox', 'prox'))
                    ->getOneOrNullResult()['prox'];
                $tipoArtigo->codigo = $prox;
            } catch (NonUniqueResultException $e) {
                throw new ViewException('Não foi possível obter o próximo código');
            }
        }
    }


}