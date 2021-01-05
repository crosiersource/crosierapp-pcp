<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use CrosierSource\CrosierLibRadxBundle\Entity\CRM\Cliente;
use Doctrine\Common\Collections\ArrayCollection;
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
    private ?string $descricao = null;

    /**
     * @var null|TipoArtigo
     *
     * @ORM\ManyToOne(targetEntity="TipoArtigo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_artigo_id", referencedColumnName="id")
     * })
     * @Groups("entity")
     */
    private ?TipoArtigo $tipoArtigo = null;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="bloqueada", type="boolean", nullable=false)
     * @Groups("entity")
     */
    private ?bool $bloqueada = null;

    /**
     * @var null|float
     *
     * @ORM\Column(name="custo_operacional_padrao", type="float", precision=10, scale=3, nullable=false)
     * @Groups("entity")
     */
    private ?float $custoOperacionalPadrao = null;

    /**
     * @var null|float
     *
     * @ORM\Column(name="margem_padrao", type="float", precision=10, scale=2, nullable=false)
     * @Groups("entity")
     */
    private ?float $margemPadrao = null;

    /**
     * @var null|string
     *
     * @ORM\Column(name="obs", type="string", length=5000, nullable=true)
     * @Groups("entity")
     */
    private ?string $obs = null;

    /**
     * @var null|int
     *
     * @ORM\Column(name="prazo_padrao", type="integer", nullable=false)
     * @Groups("entity")
     */
    private ?int $prazoPadrao = null;

    /**
     * @var null|string
     *
     * @ORM\Column(name="custo_financeiro_padrao", type="decimal", precision=19, scale=2, nullable=false)
     * @Groups("entity")
     */
    private ?string $custoFinanceiroPadrao = null;

    /**
     * @var null|string
     *
     * @ORM\Column(name="modo_calculo", type="string", length=15, nullable=false)
     * @Groups("entity")
     */
    private ?string $modoCalculo = null;

    /**
     * @var null|int
     *
     * @ORM\Column(name="grade_id", type="bigint", nullable=false)
     * @Groups("entity")
     */
    private ?int $gradeId = null;

    /**
     * Transient.
     *
     * @var array
     */
    private array $gradesTamanhosByPosicaoArray;

    /**
     * @var null|bool
     *
     * @ORM\Column(name="oculta", type="boolean", nullable=false)
     * @Groups("entity")
     */
    private ?bool $oculta = null;


    /**
     * @var null|Cliente
     *
     * @ORM\ManyToOne(targetEntity="CrosierSource\CrosierLibRadxBundle\Entity\CRM\Cliente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     * })
     * @Groups("entity")
     */
    private ?Cliente $instituicao = null;

    /**
     *
     * @var FichaTecnicaItem[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="FichaTecnicaItem",
     *      mappedBy="fichaTecnica",
     *      orphanRemoval=true,
     *     cascade={"all"}
     * )
     */
    private $itens;


    /**
     *
     * @var FichaTecnicaPreco[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="FichaTecnicaPreco",
     *      mappedBy="fichaTecnica",
     *      orphanRemoval=true,
     *     cascade={"all"}
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $precos;


    public function __construct()
    {
        $this->itens = new ArrayCollection();
        $this->precos = new ArrayCollection();
    }


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
     * @return null|string
     */
    public function getDescricaoMontada(): ?string
    {
        if ($this->id && $this->descricao) {
            return str_pad($this->id, 6, '0', STR_PAD_LEFT) . ' - ' . $this->descricao;
        }
        return null;
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
     * @return Cliente|null
     */
    public function getInstituicao(): ?Cliente
    {
        return $this->instituicao;
    }

    /**
     * @param Cliente|null $instituicao
     * @return FichaTecnica
     */
    public function setInstituicao(?Cliente $instituicao): FichaTecnica
    {
        $this->instituicao = $instituicao;
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

    /**
     * Ex.:
     * ‌Array
     * (
     * [1] => 02
     * [2] => 04
     * [3] => 06
     * [4] => 08
     * [5] => 10
     * [6] => 12
     * [7] => 14
     * [8] => 16
     * [9] => P
     * [10] => M
     * [11] => G
     * [12] => XG
     * [13] => SG
     * [14] => SS
     * [15] => -
     * )
     * @return array
     */
    public function getGradesTamanhosByPosicaoArray(): array
    {
        return $this->gradesTamanhosByPosicaoArray;
    }

    /**
     * @param array $gradesTamanhosByPosicaoArray
     */
    public function setGradesTamanhosByPosicaoArray(array $gradesTamanhosByPosicaoArray): void
    {
        $this->gradesTamanhosByPosicaoArray = $gradesTamanhosByPosicaoArray;
    }

    /**
     * @return mixed
     */
    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
            $itens = $this->getItens();
            $novosItens = new ArrayCollection();
            foreach ($itens as $item) {
                $novoItem = clone $item;
                $novoItem->setFichaTecnica($this);
                $novosItens->add($novoItem);
            }
            $this->itens = $novosItens;

            $precos = $this->getPrecos();
            $novosPrecos = new ArrayCollection();
            foreach ($precos as $preco) {
                $novoPreco = clone $preco;
                $novoPreco->setFichaTecnica($this);
                $novosPrecos->add($novoPreco);
            }
            $this->precos = $novosPrecos;
        }
    }

    /**
     * @return FichaTecnicaItem[]|ArrayCollection
     */
    public function getItens()
    {
        return $this->itens;
    }

    /**
     * @param FichaTecnicaItem[]|ArrayCollection $itens
     * @return FichaTecnica
     */
    public function setItens($itens): FichaTecnica
    {
        $this->itens = $itens;
        return $this;
    }

    /**
     * @return FichaTecnicaPreco[]|ArrayCollection
     */
    public function getPrecos()
    {
        return $this->precos;
    }

    /**
     * @param FichaTecnicaPreco[]|ArrayCollection $precos
     * @return FichaTecnica
     */
    public function setPrecos($precos): FichaTecnica
    {
        $this->precos = $precos;
        return $this;
    }


}
