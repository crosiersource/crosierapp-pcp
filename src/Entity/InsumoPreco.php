<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
     * @var null|float
     *
     * @ORM\Column(name="coeficiente", type="float", precision=10, scale=0, nullable=false)
     * @Groups("entity")
     */
    private $coeficiente;

    /**
     * @var null|float
     *
     * @ORM\Column(name="custo_operacional", type="float", precision=10, scale=0, nullable=false)
     * @Groups("entity")
     */
    private $custoOperacional;

    /**
     * @var null|\DateTime
     *
     * @ORM\Column(name="dt_custo", type="date", nullable=false)
     * @Groups("entity")
     */
    private $dtCusto;

    /**
     * @var null|float
     *
     * @ORM\Column(name="margem", type="float", precision=10, scale=0, nullable=false)
     * @Groups("entity")
     */
    private $margem;

    /**
     * @var null|int
     *
     * @ORM\Column(name="prazo", type="integer", nullable=false)
     * @Groups("entity")
     */
    private $prazo;

    /**
     * @var null|float
     *
     * @ORM\Column(name="preco_custo", type="float", precision=10, scale=0, nullable=false)
     * @Groups("entity")
     */
    private $precoCusto;

    /**
     * @var null|float
     *
     * @ORM\Column(name="preco_prazo", type="float", precision=10, scale=0, nullable=false)
     * @Groups("entity")
     */
    private $precoPrazo;

    /**
     * @var null|float
     *
     * @ORM\Column(name="preco_vista", type="float", precision=10, scale=0, nullable=false)
     * @Groups("entity")
     */
    private $precoVista;

    /**
     * @var null|int
     *
     * @ORM\Column(name="fornecedor_id", type="bigint", nullable=true)
     * @Groups("entity")
     */
    private $fornecedorId;

    /**
     * @var null|int
     *
     * @ORM\Column(name="insumo_id", type="bigint", nullable=false)
     * @Groups("entity")
     */
    private $insumoId;

    /**
     * @var null|string
     *
     * @ORM\Column(name="custo_financeiro", type="decimal", precision=19, scale=2, nullable=false)
     * @Groups("entity")
     */
    private $custoFinanceiro;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="atual", type="boolean", nullable=false)
     * @Groups("entity")
     */
    private $atual;

    /**
     * @return float|null
     */
    public function getCoeficiente(): ?float
    {
        return $this->coeficiente;
    }

    /**
     * @param float|null $coeficiente
     * @return InsumoPreco
     */
    public function setCoeficiente(?float $coeficiente): InsumoPreco
    {
        $this->coeficiente = $coeficiente;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getCustoOperacional(): ?float
    {
        return $this->custoOperacional;
    }

    /**
     * @param float|null $custoOperacional
     * @return InsumoPreco
     */
    public function setCustoOperacional(?float $custoOperacional): InsumoPreco
    {
        $this->custoOperacional = $custoOperacional;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDtCusto(): ?\DateTime
    {
        return $this->dtCusto;
    }

    /**
     * @param \DateTime|null $dtCusto
     * @return InsumoPreco
     */
    public function setDtCusto(?\DateTime $dtCusto): InsumoPreco
    {
        $this->dtCusto = $dtCusto;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMargem(): ?float
    {
        return $this->margem;
    }

    /**
     * @param float|null $margem
     * @return InsumoPreco
     */
    public function setMargem(?float $margem): InsumoPreco
    {
        $this->margem = $margem;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPrazo(): ?int
    {
        return $this->prazo;
    }

    /**
     * @param int|null $prazo
     * @return InsumoPreco
     */
    public function setPrazo(?int $prazo): InsumoPreco
    {
        $this->prazo = $prazo;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrecoCusto(): ?float
    {
        return $this->precoCusto;
    }

    /**
     * @param float|null $precoCusto
     * @return InsumoPreco
     */
    public function setPrecoCusto(?float $precoCusto): InsumoPreco
    {
        $this->precoCusto = $precoCusto;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrecoPrazo(): ?float
    {
        return $this->precoPrazo;
    }

    /**
     * @param float|null $precoPrazo
     * @return InsumoPreco
     */
    public function setPrecoPrazo(?float $precoPrazo): InsumoPreco
    {
        $this->precoPrazo = $precoPrazo;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrecoVista(): ?float
    {
        return $this->precoVista;
    }

    /**
     * @param float|null $precoVista
     * @return InsumoPreco
     */
    public function setPrecoVista(?float $precoVista): InsumoPreco
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
     * @return int|null
     */
    public function getInsumoId(): ?int
    {
        return $this->insumoId;
    }

    /**
     * @param int|null $insumoId
     * @return InsumoPreco
     */
    public function setInsumoId(?int $insumoId): InsumoPreco
    {
        $this->insumoId = $insumoId;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getCustoFinanceiro(): ?string
    {
        return $this->custoFinanceiro;
    }

    /**
     * @param null|string $custoFinanceiro
     * @return InsumoPreco
     */
    public function setCustoFinanceiro(?string $custoFinanceiro): InsumoPreco
    {
        $this->custoFinanceiro = $custoFinanceiro;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getAtual(): ?bool
    {
        return $this->atual;
    }

    /**
     * @param bool|null $atual
     * @return InsumoPreco
     */
    public function setAtual(?bool $atual): InsumoPreco
    {
        $this->atual = $atual;
        return $this;
    }


}
