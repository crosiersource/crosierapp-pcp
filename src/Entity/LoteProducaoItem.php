<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * LoteProducaoItem
 *
 * @ORM\Table(name="prod_lote_producao_item")
 * @ORM\Entity
 *
 * @author Carlos Eduardo Pauluk
 */
class LoteProducaoItem implements EntityId
{

    use EntityIdTrait;


    /**
     * @var null|string
     *
     * @ORM\Column(name="obs", type="string", length=5000, nullable=true)
     * @Groups("entity")
     */
    private $obs;

    /**
     * @var null|int
     *
     * @ORM\Column(name="ordem", type="integer", nullable=false)
     * @Groups("entity")
     */
    private $ordem;

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
     * @var null|LoteProducao
     *
     * @ORM\ManyToOne(targetEntity="LoteProducao",inversedBy="itens")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lote_producao_id", referencedColumnName="id")
     * })
     * @Groups("entity")
     */
    private $loteProducao;

    /**
     *
     * @var LoteProducaoItemQtde[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="LoteProducaoItemQtde",
     *      mappedBy="loteProducaoItem",
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
     * @var integer
     */
    private $totalQtdes;


    public function __construct()
    {
        $this->qtdes = new ArrayCollection();
    }

    /**
     * @return null|string
     */
    public function getObs(): ?string
    {
        return $this->obs;
    }

    /**
     * @param null|string $obs
     * @return LoteProducaoItem
     */
    public function setObs(?string $obs): LoteProducaoItem
    {
        $this->obs = $obs;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getOrdem(): ?int
    {
        return $this->ordem;
    }

    /**
     * @param int|null $ordem
     * @return LoteProducaoItem
     */
    public function setOrdem(?int $ordem): LoteProducaoItem
    {
        $this->ordem = $ordem;
        return $this;
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
     * @return LoteProducaoItem
     */
    public function setFichaTecnica(?FichaTecnica $fichaTecnica): LoteProducaoItem
    {
        $this->fichaTecnica = $fichaTecnica;
        return $this;
    }

    /**
     * @return LoteProducao|null
     */
    public function getLoteProducao(): ?LoteProducao
    {
        return $this->loteProducao;
    }

    /**
     * @param LoteProducao|null $loteProducao
     * @return LoteProducaoItem
     */
    public function setLoteProducao(?LoteProducao $loteProducao): LoteProducaoItem
    {
        $this->loteProducao = $loteProducao;
        return $this;
    }

    /**
     * @return LoteProducaoItemQtde[]|ArrayCollection
     */
    public function getQtdes()
    {
        return $this->qtdes;
    }

    /**
     * @param LoteProducaoItemQtde[]|ArrayCollection $qtdes
     * @return LoteProducaoItem
     */
    public function setQtdes($qtdes): LoteProducaoItem
    {
        $this->qtdes = $qtdes;
        return $this;
    }


    /**
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
     * @return int
     */
    public function getTotalQtdes(): int
    {
        $this->totalQtdes = 0;
        if ($this->getQtdes()) {
            foreach ($this->getQtdes() as $qtde) {
                $this->totalQtdes += $qtde->getQtde();
            }
        }
        return $this->totalQtdes;
    }


}
