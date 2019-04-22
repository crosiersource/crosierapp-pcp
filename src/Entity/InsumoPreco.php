<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * InsumoPreco
 *
 * @ORM\Table(name="prod_insumo_preco")
 * @ORM\Entity(repositoryClass="App\Repository\InsumoPrecoRepository")
 *
 * @author Carlos Eduardo Pauluk
 */
class InsumoPreco implements EntityId
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
     * @var int|null
     *
     * @ORM\Column(name="fornecedor_id", type="bigint", nullable=true)
     */
    private $fornecedorId;

    /**
     * @var int
     *
     * @ORM\Column(name="insumo_id", type="bigint", nullable=false)
     */
    private $insumoId;

    /**
     * @var string
     *
     * @ORM\Column(name="custo_financeiro", type="decimal", precision=19, scale=2, nullable=false)
     */
    private $custoFinanceiro;

    /**
     * @var bool
     *
     * @ORM\Column(name="atual", type="boolean", nullable=false)
     */
    private $atual;

    /**
     * @return float
     */
    public function getCoeficiente(): float
    {
        return $this->coeficiente;
    }

    /**
     * @param float $coeficiente
     * @return InsumoPreco
     */
    public function setCoeficiente(float $coeficiente): InsumoPreco
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
     * @return InsumoPreco
     */
    public function setCustoOperacional(float $custoOperacional): InsumoPreco
    {
        $this->custoOperacional = $custoOperacional;
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
     * @return InsumoPreco
     */
    public function setDtCusto(\DateTime $dtCusto): InsumoPreco
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
     * @return InsumoPreco
     */
    public function setMargem(float $margem): InsumoPreco
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
     * @return InsumoPreco
     */
    public function setPrazo(int $prazo): InsumoPreco
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
     * @return InsumoPreco
     */
    public function setPrecoCusto(float $precoCusto): InsumoPreco
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
     * @return InsumoPreco
     */
    public function setPrecoPrazo(float $precoPrazo): InsumoPreco
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
     * @return InsumoPreco
     */
    public function setPrecoVista(float $precoVista): InsumoPreco
    {
        $this->precoVista = $precoVista;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getFornecedorId(): ?int
    {
        return $this->fornecedorId;
    }

    /**
     * @param int|null $fornecedorId
     * @return InsumoPreco
     */
    public function setFornecedorId(?int $fornecedorId): InsumoPreco
    {
        $this->fornecedorId = $fornecedorId;
        return $this;
    }

    /**
     * @return int
     */
    public function getInsumoId(): int
    {
        return $this->insumoId;
    }

    /**
     * @param int $insumoId
     * @return InsumoPreco
     */
    public function setInsumoId(int $insumoId): InsumoPreco
    {
        $this->insumoId = $insumoId;
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
     * @return InsumoPreco
     */
    public function setCustoFinanceiro(string $custoFinanceiro): InsumoPreco
    {
        $this->custoFinanceiro = $custoFinanceiro;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAtual(): bool
    {
        return $this->atual;
    }

    /**
     * @param bool $atual
     * @return InsumoPreco
     */
    public function setAtual(bool $atual): InsumoPreco
    {
        $this->atual = $atual;
        return $this;
    }


}
