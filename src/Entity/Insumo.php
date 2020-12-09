<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * Insumo
 *
 * @ORM\Table(name="prod_insumo")
 * @ORM\Entity(repositoryClass="App\Repository\InsumoRepository")
 *
 * @author Carlos Eduardo Pauluk
 */
class Insumo implements EntityId
{

    use EntityIdTrait;

    /**
     * @var null|int
     *
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     * @Groups("entity")
     */
    private ?int $codigo;

    /**
     * @var null|string
     *
     * @ORM\Column(name="descricao", type="string", length=200, nullable=false)
     * @Groups("entity")
     */
    private ?string $descricao;

    /**
     * @var null|int
     *
     * @ORM\Column(name="unidade_produto_id", type="bigint", nullable=false)
     * @Groups("entity")
     */
    private ?int $unidadeProdutoId;

    /**
     * @var null|TipoInsumo
     *
     * @ORM\ManyToOne(targetEntity="TipoInsumo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_insumo_id", referencedColumnName="id")
     * })
     * @Groups("entity")
     */
    private ?TipoInsumo $tipoInsumo;

    /**
     *
     * @ORM\Column(name="json_data", type="json")
     * @var null|array
     * @NotUppercase()
     * @Groups("entity")
     */
    public ?array $jsonData = null;

    /**
     * Transient (json_data->>"$.dt_custo")
     * @var null|\DateTime
     *
     * @ORM\Column(name="dt_custo", type="date", nullable=false)
     * @Groups("entity")
     */
    public ?\DateTime $dtCusto;

    /**
     * Transient (json_data->>"$.preco_custo")
     * @var null|float
     *
     * @ORM\Column(name="preco_custo", type="float", precision=10, scale=0, nullable=false)
     * @Groups("entity")
     */
    public ?float $precoCusto;


    /**
     *
     * @var InsumoPreco[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="InsumoPreco",
     *      mappedBy="insumo",
     *      orphanRemoval=true,
     *     cascade={"all"}
     * )
     */
    private $precos;

    /**
     * Transient.
     * @Groups("entity")
     * @MaxDepth(2)
     * @var InsumoPreco
     */
    private InsumoPreco $precoAtual;


    public function __construct()
    {
        $this->precos = new ArrayCollection();
    }


    public function getCodigo($format = false)
    {
        if ($format) {
            return str_pad($this->codigo, 3, '0', STR_PAD_LEFT);
        }

        return $this->codigo;
    }

    /**
     * @param int|null $codigo
     * @return Insumo
     */
    public function setCodigo(?int $codigo): Insumo
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
     * @return Insumo
     */
    public function setDescricao(?string $descricao): Insumo
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getUnidadeProdutoId(): ?int
    {
        return $this->unidadeProdutoId;
    }

    /**
     * @param int|null $unidadeProdutoId
     * @return Insumo
     */
    public function setUnidadeProdutoId(?int $unidadeProdutoId): Insumo
    {
        $this->unidadeProdutoId = $unidadeProdutoId;
        return $this;
    }

    /**
     * @return TipoInsumo|null
     */
    public function getTipoInsumo(): ?TipoInsumo
    {
        return $this->tipoInsumo;
    }

    /**
     * @param TipoInsumo|null $tipoInsumo
     * @return Insumo
     */
    public function setTipoInsumo(?TipoInsumo $tipoInsumo): Insumo
    {
        $this->tipoInsumo = $tipoInsumo;
        return $this;
    }

    /**
     * @return InsumoPreco[]|ArrayCollection
     */
    public function getPrecos()
    {
        return $this->precos;
    }

    /**
     * @param InsumoPreco[]|ArrayCollection $precos
     * @return Insumo
     */
    public function setPrecos($precos)
    {
        $this->precos = $precos;
        return $this;
    }

    /**
     * @param InsumoPreco $precoAtual
     * @return Insumo
     */
    public function setPrecoAtual(?InsumoPreco $precoAtual): Insumo
    {
        $this->precoAtual = $precoAtual;
        return $this;
    }


    /**
     * @return null|InsumoPreco
     */
    public function getPrecoAtual(): ?InsumoPreco
    {
        if ($this->precos) {
            foreach ($this->precos as $preco) {
                if ($preco->getAtual()) {
                    $this->precoAtual = $preco;
                    break;
                }
            }

            if (!$this->precoAtual) {
                $iterator = $this->precos->getIterator();
                $iterator->uasort(function (InsumoPreco $a, InsumoPreco $b) {
                    return $a->getDtCusto() >= $b->getDtCusto();
                });
                $precos = new ArrayCollection(iterator_to_array($iterator));

                $this->precoAtual = $precos[0];
            }
        }
        return $this->precoAtual;
    }


}
