<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * FichaTecnicaItem
 *
 * @ORM\Table(name="prod_fichatecnica_item")
 * @ORM\Entity(repositoryClass="App\Repository\FichaTecnicaItemRepository")
 *
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaItem implements EntityId
{

    use EntityIdTrait;


    /**
     * @var null|FichaTecnica
     *
     * @ORM\ManyToOne(targetEntity="FichaTecnica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fichatecnica_id", referencedColumnName="id")
     * })
     * @Groups("entity")
     */
    private $fichaTecnica;

    /**
     * @var null|Insumo
     *
     * @ORM\ManyToOne(targetEntity="Insumo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="insumo_id", referencedColumnName="id")
     * })
     * @Groups("entity")
     */
    private $insumo;


    /**
     * @return FichaTecnica|null
     */
    public function getFichaTecnica(): ?FichaTecnica
    {
        return $this->fichaTecnica;
    }

    /**
     * @param FichaTecnica|null $fichaTecnica
     * @return FichaTecnicaItem
     */
    public function setFichaTecnica(?FichaTecnica $fichaTecnica): FichaTecnicaItem
    {
        $this->fichaTecnica = $fichaTecnica;
        return $this;
    }

    /**
     * @return Insumo|null
     */
    public function getInsumo(): ?Insumo
    {
        return $this->insumo;
    }

    /**
     * @param Insumo|null $insumo
     * @return FichaTecnicaItem
     */
    public function setInsumo(?Insumo $insumo): FichaTecnicaItem
    {
        $this->insumo = $insumo;
        return $this;
    }


}
