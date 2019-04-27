<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * FichaTecnicaPreco
 *
 * @ORM\Table(name="prod_fichatecnica_preco")
 * @ORM\Entity(repositoryClass="App\Repository\FichaTecnicaPrecoRepository")
 *
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaPreco implements EntityId
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
     * @var null|string
     *
     * @ORM\Column(name="descricao", type="string", length=200, nullable=false)
     * @Groups("entity")
     */
    private $descricao;

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
     * @var null|string
     *
     * @ORM\Column(name="custo_financeiro", type="decimal", precision=19, scale=2, nullable=false)
     * @Groups("entity")
     */
    private $custoFinanceiro;

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
     * @return float|null
     */
    public function getCoeficiente(): ?float
    {
        return $this->coeficiente;
    }

    /**
     * @param float|null $coeficiente
     * @return FichaTecnicaPreco
     */
    public function setCoeficiente(?float $coeficiente): FichaTecnicaPreco
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
     * @return FichaTecnicaPreco
     */
    public function setCustoOperacional(?float $custoOperacional): FichaTecnicaPreco
    {
        $this->custoOperacional = $custoOperacional;
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
     * @return FichaTecnicaPreco
     */
    public function setDescricao(?string $descricao): FichaTecnicaPreco
    {
        $this->descricao = $descricao;
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
     * @return FichaTecnicaPreco
     */
    public function setDtCusto(?\DateTime $dtCusto): FichaTecnicaPreco
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
     * @return FichaTecnicaPreco
     */
    public function setMargem(?float $margem): FichaTecnicaPreco
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
     * @return FichaTecnicaPreco
     */
    public function setPrazo(?int $prazo): FichaTecnicaPreco
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
     * @return FichaTecnicaPreco
     */
    public function setPrecoCusto(?float $precoCusto): FichaTecnicaPreco
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
     * @return FichaTecnicaPreco
     */
    public function setPrecoPrazo(?float $precoPrazo): FichaTecnicaPreco
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
     * @return FichaTecnicaPreco
     */
    public function setPrecoVista(?float $precoVista): FichaTecnicaPreco
    {
        $this->precoVista = $precoVista;
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
     * @return FichaTecnicaPreco
     */
    public function setCustoFinanceiro(?string $custoFinanceiro): FichaTecnicaPreco
    {
        $this->custoFinanceiro = $custoFinanceiro;
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
     * @return FichaTecnicaPreco
     */
    public function setFichaTecnica(?FichaTecnica $fichaTecnica): FichaTecnicaPreco
    {
        $this->fichaTecnica = $fichaTecnica;
        return $this;
    }


}
