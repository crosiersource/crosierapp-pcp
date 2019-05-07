<?php

namespace App\EntityHandler;

use App\Entity\FichaTecnica;
use CrosierSource\CrosierLibBaseBundle\APIClient\Base\PessoaAPIClient;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para a entidade FichaTecnica.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaEntityHandler extends EntityHandler
{
    /** @var PessoaAPIClient */
    private $pessoaAPIClient;

    /**
     * @required
     * @param PessoaAPIClient $pessoaAPIClient
     */
    public function setPessoaAPIClient(PessoaAPIClient $pessoaAPIClient): void
    {
        $this->pessoaAPIClient = $pessoaAPIClient;
    }


    public function getEntityClass()
    {
        return FichaTecnica::class;
    }

    public function beforeSave($fichaTecnica)
    {
        /** @var FichaTecnica $fichaTecnica */
        $pessoa = $this->pessoaAPIClient->findById($fichaTecnica->getPessoaId());
        $fichaTecnica->setPessoaNome($pessoa['nomeMontado']);


    }


}