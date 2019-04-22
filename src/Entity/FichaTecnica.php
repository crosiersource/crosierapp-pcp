<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * FichaTecnica
 *
 * @ORM\Table(name="prod_confeccao")
 * @ORM\Entity(repositoryClass="App\Repository\FichaTecnicaRepository")
 *
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnica implements EntityId
{

    use EntityIdTrait;

    /**
     * @var bool
     *
     * @ORM\Column(name="bloqueada", type="boolean", nullable=false)
     */
    private $bloqueada;

    /**
     * @var float
     *
     * @ORM\Column(name="custo_operacional_padrao", type="float", precision=10, scale=3, nullable=false)
     */
    private $custoOperacionalPadrao;

    /**
     * @var string
     *
     * @ORM\Column(name="descricao", type="string", length=200, nullable=false)
     */
    private $descricao;

    /**
     * @var float
     *
     * @ORM\Column(name="margem_padrao", type="float", precision=10, scale=3, nullable=false)
     */
    private $margemPadrao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="obs", type="string", length=5000, nullable=true)
     */
    private $obs;

    /**
     * @var int
     *
     * @ORM\Column(name="prazo_padrao", type="integer", nullable=false)
     */
    private $prazoPadrao;

    /**
     * @var string
     *
     * @ORM\Column(name="custo_financeiro_padrao", type="decimal", precision=19, scale=2, nullable=false)
     */
    private $custoFinanceiroPadrao;

    /**
     * @var string
     *
     * @ORM\Column(name="modo_calculo", type="string", length=15, nullable=false)
     */
    private $modoCalculo;

    /**
     * @var int
     *
     * @ORM\Column(name="grade_id", type="bigint", nullable=false)
     */
    private $gradeId;

    /**
     * @var bool
     *
     * @ORM\Column(name="oculta", type="boolean", nullable=false)
     */
    private $oculta;

    /**
     * @var integer|null
     *
     */
    private $instituicao;

    /**
     * @var TipoArtigo
     *
     * @ORM\ManyToOne(targetEntity="TipoArtigo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_artigo_id", referencedColumnName="id")
     * })
     */
    private $tipoArtigo;

    /**
     * @return bool
     */
    public function isBloqueada(): bool
    {
        return $this->bloqueada;
    }

    /**
     * @param bool $bloqueada
     * @return FichaTecnica
     */
    public function setBloqueada(bool $bloqueada): FichaTecnica
    {
        $this->bloqueada = $bloqueada;
        return $this;
    }

    /**
     * @return float
     */
    public function getCustoOperacionalPadrao(): float
    {
        return $this->custoOperacionalPadrao;
    }

    /**
     * @param float $custoOperacionalPadrao
     * @return FichaTecnica
     */
    public function setCustoOperacionalPadrao(float $custoOperacionalPadrao): FichaTecnica
    {
        $this->custoOperacionalPadrao = $custoOperacionalPadrao;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescricao(): string
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     * @return FichaTecnica
     */
    public function setDescricao(string $descricao): FichaTecnica
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * @return float
     */
    public function getMargemPadrao(): float
    {
        return $this->margemPadrao;
    }

    /**
     * @param float $margemPadrao
     * @return FichaTecnica
     */
    public function setMargemPadrao(float $margemPadrao): FichaTecnica
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
     * @return int
     */
    public function getPrazoPadrao(): int
    {
        return $this->prazoPadrao;
    }

    /**
     * @param int $prazoPadrao
     * @return FichaTecnica
     */
    public function setPrazoPadrao(int $prazoPadrao): FichaTecnica
    {
        $this->prazoPadrao = $prazoPadrao;
        return $this;
    }

    /**
     * @return string
     */
    public function getCustoFinanceiroPadrao(): string
    {
        return $this->custoFinanceiroPadrao;
    }

    /**
     * @param string $custoFinanceiroPadrao
     * @return FichaTecnica
     */
    public function setCustoFinanceiroPadrao(string $custoFinanceiroPadrao): FichaTecnica
    {
        $this->custoFinanceiroPadrao = $custoFinanceiroPadrao;
        return $this;
    }

    /**
     * @return string
     */
    public function getModoCalculo(): string
    {
        return $this->modoCalculo;
    }

    /**
     * @param string $modoCalculo
     * @return FichaTecnica
     */
    public function setModoCalculo(string $modoCalculo): FichaTecnica
    {
        $this->modoCalculo = $modoCalculo;
        return $this;
    }

    /**
     * @return int
     */
    public function getGradeId(): int
    {
        return $this->gradeId;
    }

    /**
     * @param int $gradeId
     * @return FichaTecnica
     */
    public function setGradeId(int $gradeId): FichaTecnica
    {
        $this->gradeId = $gradeId;
        return $this;
    }

    /**
     * @return bool
     */
    public function isOculta(): bool
    {
        return $this->oculta;
    }

    /**
     * @param bool $oculta
     * @return FichaTecnica
     */
    public function setOculta(bool $oculta): FichaTecnica
    {
        $this->oculta = $oculta;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getInstituicao(): ?int
    {
        return $this->instituicao;
    }

    /**
     * @param int|null $instituicao
     * @return FichaTecnica
     */
    public function setInstituicao(?int $instituicao): FichaTecnica
    {
        $this->instituicao = $instituicao;
        return $this;
    }

    /**
     * @return TipoArtigo
     */
    public function getTipoArtigo(): TipoArtigo
    {
        return $this->tipoArtigo;
    }

    /**
     * @param TipoArtigo $tipoArtigo
     * @return FichaTecnica
     */
    public function setTipoArtigo(TipoArtigo $tipoArtigo): FichaTecnica
    {
        $this->tipoArtigo = $tipoArtigo;
        return $this;
    }


}
