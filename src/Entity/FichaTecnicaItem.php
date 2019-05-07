<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\ManyToOne(targetEntity="FichaTecnica", inversedBy="itens")
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
     *
     * @var FichaTecnicaItemQtde[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="FichaTecnicaItemQtde",
     *      mappedBy="fichaTecnicaItem",
     *      orphanRemoval=true,
     *     cascade={"all"}
     * )
     */
    private $qtdes;

    /**
     * Transient.
     *
     * @var array
     */
    private $qtdesTamanhosArray;

    /**
     * Transient.
     *
     * @var float
     */
    private $totalQtdes;


    public function __construct()
    {
        $this->qtdes = new ArrayCollection();
    }

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

    /**
     * @return FichaTecnicaItemQtde[]|ArrayCollection
     */
    public function getQtdes()
    {
        return $this->qtdes;
    }

    /**
     * @param FichaTecnicaItemQtde[]|ArrayCollection $qtdes
     * @return FichaTecnicaItem
     */
    public function setQtdes($qtdes)
    {
        $this->qtdes = $qtdes;
        return $this;
    }

    /**
     * Retorna a qtde para cada tamanho.
     * @return array
     */
    public function getQtdesTamanhosArray(): array
    {
        return $this->qtdesTamanhosArray;
    }

    /**
     * @param array $qtdesTamanhosArray
     */
    public function setQtdesTamanhosArray(array $qtdesTamanhosArray): void
    {
        $this->qtdesTamanhosArray = $qtdesTamanhosArray;
    }

    /**
     * @return float
     */
    public function getTotalQtdes(): float
    {
        $this->totalQtdes = (float)0;
        if ($this->getQtdes()) {
            foreach ($this->getQtdes() as $qtde) {
                $this->totalQtdes = bcadd($this->totalQtdes, $qtde->getQtde(), 3);
            }
        }
        return $this->totalQtdes;
    }


    /**
     * @return mixed
     */
    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
            $qtdes = $this->getQtdes();
            $novasQtdes = new ArrayCollection();
            foreach ($qtdes as $qtde) {
                $novoQtde = clone $qtde;
                $novoQtde->setFichaTecnicaItem($this);
                $novasQtdes->add($novoQtde);
            }
            $this->qtdes = $novasQtdes;

        }
    }


}
