<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * FichaTecnica
 *
 * @ORM\Table(name="prod_fichatecnica")
 * @ORM\Entity(repositoryClass="App\Repository\FichaTecnicaRepository")
 *
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnica implements EntityId
{

    use EntityIdTrait;

    /**
     * @var null|string
     *
     * @ORM\Column(name="descricao", type="string", length=200, nullable=false)
     * @Groups("entity")
     */
    private $descricao;

    /**
     * @var null|TipoArtigo
     *
     * @ORM\ManyToOne(targetEntity="TipoArtigo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_artigo_id", referencedColumnName="id")
     * })
     * @Groups("entity")
     */
    private $tipoArtigo;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="bloqueada", type="boolean", nullable=false)
     * @Groups("entity")
     */
    private $bloqueada;

    /**
     * @var null|float
     *
     * @ORM\Column(name="custo_operacional_padrao", type="float", precision=10, scale=3, nullable=false)
     * @Groups("entity")
     */
    private $custoOperacionalPadrao;

    /**
     * @var null|float
     *
     * @ORM\Column(name="margem_padrao", type="float", precision=10, scale=3, nullable=false)
     * @Groups("entity")
     */
    private $margemPadrao;

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
     * @ORM\Column(name="prazo_padrao", type="integer", nullable=false)
     * @Groups("entity")
     */
    private $prazoPadrao;

    /**
     * @var null|string
     *
     * @ORM\Column(name="custo_financeiro_padrao", type="decimal", precision=19, scale=2, nullable=false)
     * @Groups("entity")
     */
    private $custoFinanceiroPadrao;

    /**
     * @var null|string
     *
     * @ORM\Column(name="modo_calculo", type="string", length=15, nullable=false)
     * @Groups("entity")
     */
    private $modoCalculo;

    /**
     * @var null|int
     *
     * @ORM\Column(name="grade_id", type="bigint", nullable=false)
     * @Groups("entity")
     */
    private $gradeId;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="oculta", type="boolean", nullable=false)
     * @Groups("entity")
     */
    private $oculta;

    /**
     * @var null|integer
     * @ORM\Column(name="pessoa_id", type="bigint", nullable=false)
     * @Groups("entity")
     *
     */
    private $pessoaId;

    /**
     * @var null|string
     *
     * @ORM\Column(name="pessoa_nome", type="string", length=300, nullable=false)
     * @Groups("entity")
     */
    private $pessoaNome;


    /**
     * @return bool|null
     */
    public function getBloqueada(): ?bool
    {
        return $this->bloqueada;
    }

    /**
     * @param bool|null $bloqueada
     * @return FichaTecnica
     */
    public function setBloqueada(?bool $bloqueada): FichaTecnica
    {
        $this->bloqueada = $bloqueada;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getCustoOperacionalPadrao(): ?float
    {
        return $this->custoOperacionalPadrao;
    }

    /**
     * @param float|null $custoOperacionalPadrao
     * @return FichaTecnica
     */
    public function setCustoOperacionalPadrao(?float $custoOperacionalPadrao): FichaTecnica
    {
        $this->custoOperacionalPadrao = $custoOperacionalPadrao;
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
     * @return FichaTecnica
     */
    public function setDescricao(?string $descricao): FichaTecnica
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMargemPadrao(): ?float
    {
        return $this->margemPadrao;
    }

    /**
     * @param float|null $margemPadrao
     * @return FichaTecnica
     */
    public function setMargemPadrao(?float $margemPadrao): FichaTecnica
    {
        $this->margemPadrao = $margemPadrao;
        return $this;
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
     * @return FichaTecnica
     */
    public function setObs(?string $obs): FichaTecnica
    {
        $this->obs = $obs;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPrazoPadrao(): ?int
    {
        return $this->prazoPadrao;
    }

    /**
     * @param int|null $prazoPadrao
     * @return FichaTecnica
     */
    public function setPrazoPadrao(?int $prazoPadrao): FichaTecnica
    {
        $this->prazoPadrao = $prazoPadrao;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getCustoFinanceiroPadrao(): ?string
    {
        return $this->custoFinanceiroPadrao;
    }

    /**
     * @param null|string $custoFinanceiroPadrao
     * @return FichaTecnica
     */
    public function setCustoFinanceiroPadrao(?string $custoFinanceiroPadrao): FichaTecnica
    {
        $this->custoFinanceiroPadrao = $custoFinanceiroPadrao;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getModoCalculo(): ?string
    {
        return $this->modoCalculo;
    }

    /**
     * @param null|string $modoCalculo
     * @return FichaTecnica
     */
    public function setModoCalculo(?string $modoCalculo): FichaTecnica
    {
        $this->modoCalculo = $modoCalculo;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getGradeId(): ?int
    {
        return $this->gradeId;
    }

    /**
     * @param int|null $gradeId
     * @return FichaTecnica
     */
    public function setGradeId(?int $gradeId): FichaTecnica
    {
        $this->gradeId = $gradeId;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getOculta(): ?bool
    {
        return $this->oculta;
    }

    /**
     * @param bool|null $oculta
     * @return FichaTecnica
     */
    public function setOculta(?bool $oculta): FichaTecnica
    {
        $this->oculta = $oculta;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPessoaId(): ?int
    {
        return $this->pessoaId;
    }

    /**
     * @param int|null $pessoaId
     * @return FichaTecnica
     */
    public function setPessoaId(?int $pessoaId): FichaTecnica
    {
        $this->pessoaId = $pessoaId;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPessoaNome(): ?string
    {
        return $this->pessoaNome;
    }

    /**
     * @param null|string $pessoaNome
     * @return FichaTecnica
     */
    public function setPessoaNome(?string $pessoaNome): FichaTecnica
    {
        $this->pessoaNome = $pessoaNome;
        return $this;
    }

    /**
     * @return TipoArtigo|null
     */
    public function getTipoArtigo(): ?TipoArtigo
    {
        return $this->tipoArtigo;
    }

    /**
     * @param TipoArtigo|null $tipoArtigo
     * @return FichaTecnica
     */
    public function setTipoArtigo(?TipoArtigo $tipoArtigo): FichaTecnica
    {
        $this->tipoArtigo = $tipoArtigo;
        return $this;
    }


}
