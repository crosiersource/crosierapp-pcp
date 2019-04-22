<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * FichaTecnicaPreco
 *
 * @ORM\Table(name="prod_confeccao_preco")
 * @ORM\Entity(repositoryClass="App\Repository\FichaTecnicaPrecoRepository")
 *
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaPreco implements EntityId
{

    use EntityIdTrait;

    /**
     * @var float
     *
     * @ORM\Column(name="coeficiente", type="float", precision=10, scale=0, nullable=false)
     */
    private $coeficiente;

    /**
     * @var float
     *
     * @ORM\Column(name="custo_operacional", type="float", precision=10, scale=0, nullable=false)
     */
    private $custoOperacional;

    /**
     * @var string
     *
     * @ORM\Column(name="descricao", type="string", length=200, nullable=false)
     */
    private $descricao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_custo", type="date", nullable=false)
     */
    private $dtCusto;

    /**
     * @var float
     *
     * @ORM\Column(name="margem", type="float", precision=10, scale=0, nullable=false)
     */
    private $margem;

    /**
     * @var int
     *
     * @ORM\Column(name="prazo", type="integer", nullable=false)
     */
    private $prazo;

    /**
     * @var float
     *
     * @ORM\Column(name="preco_custo", type="float", precision=10, scale=0, nullable=false)
     */
    private $precoCusto;

    /**
     * @var float
     *
     * @ORM\Column(name="preco_prazo", type="float", precision=10, scale=0, nullable=false)
     */
    private $precoPrazo;

    /**
     * @var float
     *
     * @ORM\Column(name="preco_vista", type="float", precision=10, scale=0, nullable=false)
     */
    private $precoVista;

    /**
     * @var string
     *
     * @ORM\Column(name="custo_financeiro", type="decimal", precision=19, scale=2, nullable=false)
     */
    private $custoFinanceiro;

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
     * @return float
     */
    public function getCoeficiente(): float
    {
        return $this->coeficiente;
    }

    /**
     * @param float $coeficiente
     * @return FichaTecnicaPreco
     */
    public function setCoeficiente(float $coeficiente): FichaTecnicaPreco
    {
        $this->coeficiente = $coeficiente;
        return $this;
    }

    /**
     * @return float
     */
    public function getCustoOperacional(): float
    {
        return $this->custoOperacional;
    }

    /**
     * @param float $custoOperacional
     * @return FichaTecnicaPreco
     */
    public function setCustoOperacional(float $custoOperacional): FichaTecnicaPreco
    {
        $this->custoOperacional = $custoOperacional;
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
     * @return FichaTecnicaPreco
     */
    public function setDescricao(string $descricao): FichaTecnicaPreco
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDtCusto(): \DateTime
    {
        return $this->dtCusto;
    }

    /**
     * @param \DateTime $dtCusto
     * @return FichaTecnicaPreco
     */
    public function setDtCusto(\DateTime $dtCusto): FichaTecnicaPreco
    {
        $this->dtCusto = $dtCusto;
        return $this;
    }

    /**
     * @return float
     */
    public function getMargem(): float
    {
        return $this->margem;
    }

    /**
     * @param float $margem
     * @return FichaTecnicaPreco
     */
    public function setMargem(float $margem): FichaTecnicaPreco
    {
        $this->margem = $margem;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrazo(): int
    {
        return $this->prazo;
    }

    /**
     * @param int $prazo
     * @return FichaTecnicaPreco
     */
    public function setPrazo(int $prazo): FichaTecnicaPreco
    {
        $this->prazo = $prazo;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrecoCusto(): float
    {
        return $this->precoCusto;
    }

    /**
     * @param float $precoCusto
     * @return FichaTecnicaPreco
     */
    public function setPrecoCusto(float $precoCusto): FichaTecnicaPreco
    {
        $this->precoCusto = $precoCusto;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrecoPrazo(): float
    {
        return $this->precoPrazo;
    }

    /**
     * @param float $precoPrazo
     * @return FichaTecnicaPreco
     */
    public function setPrecoPrazo(float $precoPrazo): FichaTecnicaPreco
    {
        $this->precoPrazo = $precoPrazo;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrecoVista(): float
    {
        return $this->precoVista;
    }

    /**
     * @param float $precoVista
     * @return FichaTecnicaPreco
     */
    public function setPrecoVista(float $precoVista): FichaTecnicaPreco
    {
        $this->precoVista = $precoVista;
        return $this;
    }

    /**
     * @return string
     */
    public function getCustoFinanceiro(): string
    {
        return $this->custoFinanceiro;
    }

    /**
     * @param string $custoFinanceiro
     * @return FichaTecnicaPreco
     */
    public function setCustoFinanceiro(string $custoFinanceiro): FichaTecnicaPreco
    {
        $this->custoFinanceiro = $custoFinanceiro;
        return $this;
    }

    /**
     * @return FichaTecnica
     */
    public function getConfeccao(): FichaTecnica
    {
        return $this->confeccao;
    }

    /**
     * @param FichaTecnica $confeccao
     * @return FichaTecnicaPreco
     */
    public function setConfeccao(FichaTecnica $confeccao): FichaTecnicaPreco
    {
        $this->confeccao = $confeccao;
        return $this;
    }


}
