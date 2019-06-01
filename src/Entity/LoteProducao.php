<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * LoteProducao
 *
 * @ORM\Table(name="prod_lote_producao")
 * @ORM\Entity(repositoryClass="App\Repository\LoteProducaoRepository")
 *
 * @author Carlos Eduardo Pauluk
 */
class LoteProducao implements EntityId
{

    use EntityIdTrait;

    /**
     * @var null|int
     *
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     * @Groups("entity")
     */
    private $codigo;

    /**
     * @var null|string
     *
     * @ORM\Column(name="descricao", type="string", length=200, nullable=false)
     * @Groups("entity")
     */
    private $descricao;

    /**
     * @var null|\DateTime
     *
     * @ORM\Column(name="dt_lote", type="date", nullable=true)
     * @Groups("entity")
     */
    private $dtLote;

    /**
     *
     * @var LoteProducaoItem[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="LoteProducaoItem",
     *      mappedBy="loteProducao",
     *      orphanRemoval=true
     * )
     */
    private $itens;

    public function __construct()
    {
        $this->itens = new ArrayCollection();
    }


    /**
     * @return int|null
     */
    public function getCodigo(): ?int
    {
        return $this->codigo;
    }

    /**
     * @param int|null $codigo
     * @return LoteProducao
     */
    public function setCodigo(?int $codigo): LoteProducao
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * @param null|string $descricao
     * @return LoteProducao
     */
    public function setDescricao(?string $descricao): LoteProducao
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDtLote(): ?\DateTime
    {
        return $this->dtLote;
    }

    /**
     * @param \DateTime|null $dtLote
     * @return LoteProducao
     */
    public function setDtLote(?\DateTime $dtLote): LoteProducao
    {
        $this->dtLote = $dtLote;
        return $this;
    }


    /**
     *
     * @return Collection|LoteProducaoItem[]
     */
    public function getItens(): Collection
    {
        return $this->itens;
    }

    /**
     * @param LoteProducaoItem[]|ArrayCollection $itens
     * @return LoteProducao
     */
    public function setItens($itens)
    {
        $this->itens = $itens;
        return $this;
    }


}
