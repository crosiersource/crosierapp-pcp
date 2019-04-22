<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * FichaTecnicaItem
 *
 * @ORM\Table(name="prod_confeccao_item")
 * @ORM\Entity(repositoryClass="App\Repository\FichaTecnicaItemRepository")
 *
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaItem implements EntityId
{

    use EntityIdTrait;


    /**
     * @var FichaTecnica
     *
     * @ORM\ManyToOne(targetEntity="FichaTecnica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="confeccao_id", referencedColumnName="id")
     * })
     */
    private $confeccao;

    /**
     * @var Insumo
     *
     * @ORM\ManyToOne(targetEntity="Insumo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="insumo_id", referencedColumnName="id")
     * })
     */
    private $insumo;

    /**
     * @return FichaTecnica
     */
    public function getConfeccao(): FichaTecnica
    {
        return $this->confeccao;
    }

    /**
     * @param FichaTecnica $confeccao
     * @return FichaTecnicaItem
     */
    public function setConfeccao(FichaTecnica $confeccao): FichaTecnicaItem
    {
        $this->confeccao = $confeccao;
        return $this;
    }

    /**
     * @return Insumo
     */
    public function getInsumo(): Insumo
    {
        return $this->insumo;
    }

    /**
     * @param Insumo $insumo
     * @return FichaTecnicaItem
     */
    public function setInsumo(Insumo $insumo): FichaTecnicaItem
    {
        $this->insumo = $insumo;
        return $this;
    }


}
