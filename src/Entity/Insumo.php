<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"insumo","tipoInsumo","insumoPreco","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"insumo"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/pcp/insumo/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "put"={"path"="/pcp/insumo/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "delete"={"path"="/pcp/insumo/{id}", "security"="is_granted('ROLE_PCP_ADMIN')"},
 *     },
 *     collectionOperations={
 *          "get"={"path"="/pcp/insumo", "security"="is_granted('ROLE_PCP')"},
 *          "post"={"path"="/pcp/insumo", "security"="is_granted('ROLE_PCP')"}
 *     },
 *
 *     attributes={
 *          "pagination_items_per_page"=10,
 *          "formats"={"jsonld", "csv"={"text/csv"}}
 *     }
 * )
 *
 *
 * @ApiFilter(SearchFilter::class, properties={
 *     "codigo": "exact",
 *     "descricao": "partial",
 *     "marca": "partial",
 *     "tipoInsumo": "exact",
 *     "tipoInsumo.descricao": "partial"
 * })
 * @ApiFilter(OrderFilter::class, properties={"id", "codigo", "descricao", "marca", "tipoInsumo.codigo", "updated"}, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="App\EntityHandler\InsumoEntityHandler")
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
     * @Groups("insumo")
     */
    public ?int $codigo = null;

    /**
     * @var null|string
     *
     * @ORM\Column(name="descricao", type="string", length=200, nullable=false)
     * @Groups("insumo")
     */
    public ?string $descricao = null;

    /**
     * @var null|string
     *
     * @ORM\Column(name="marca", type="string", length=200, nullable=true)
     * @Groups("insumo")
     */
    public ?string $marca = null;

    /**
     * @ORM\Column(name="unidade_produto_id", type="bigint", nullable=false)
     * @var null|int
     */
    public ?int $unidadeProdutoId = null;

    /**
     * @var null|TipoInsumo
     *
     * @ORM\ManyToOne(targetEntity="TipoInsumo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_insumo_id", referencedColumnName="id")
     * })
     * @Groups("insumo")
     */
    public ?TipoInsumo $tipoInsumo = null;

    /**
     *
     * @ORM\Column(name="json_data", type="json")
     * @var null|array
     * @NotUppercase()
     * @Groups("insumo")
     */
    public ?array $jsonData = null;


    /**
     * @var null|\DateTime
     *
     * @ORM\Column(name="dt_custo", type="date", nullable=false)
     * @Groups("insumo")
     */
    public ?\DateTime $dtCusto = null;


    /**
     * @var null|float
     *
     * @ORM\Column(name="preco_custo", type="float", precision=15, scale=2, nullable=false)
     */
    public ?float $precoCusto = null;

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
    public $precos;

    /**
     * Transient.
     * @Groups("insumo")
     * @MaxDepth(2)
     */
    public ?InsumoPreco $precoAtual = null;


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
     * @return InsumoPreco[]|ArrayCollection
     */
    public function getPrecos()
    {
        return $this->precos;
    }

    /**
     * @param InsumoPreco[]|ArrayCollection $precos
     */
    public function setPrecos($precos): Insumo
    {
        $this->precos = $precos;
        return $this;
    }

    public function getPrecoAtual(): ?InsumoPreco
    {
        try {
            if (!$this->precoAtual) {
                if ($this->precos) {
                    foreach ($this->precos as $preco) {
                        if ($preco->atual) {
                            $this->precoAtual = $preco;
                            break;
                        }
                    }

                    if (!$this->precoAtual) {
                        $iterator = $this->precos->getIterator();
                        $iterator->uasort(function (InsumoPreco $a, InsumoPreco $b) {
                            return $a->dtCusto >= $b->dtCusto;
                        });
                        $precos = new ArrayCollection(iterator_to_array($iterator));

                        $this->precoAtual = $precos[0];
                    }
                }
            }
        } catch (\Exception $e) {
            $this->precoAtual = null;
        }
        return $this->precoAtual;
    }


    /**
     * Para aceitar tanto em string quanto em double.
     * @Groups("insumo")
     * @SerializedName("precoCusto")
     * @return float
     */
    public function getPrecoCustoFormatted(): float
    {
        return (float)$this->precoCusto;
    }


    /**
     * Para aceitar tanto em string quanto em double.
     * @Groups("insumo")
     * @SerializedName("precoCusto")
     * @param float $precoCusto
     */
    public function setPrecoCustoFormatted(float $precoCusto)
    {
        $this->precoCusto = $precoCusto;
    }


    /**
     * Para aceitar tanto em string quanto em double.
     * @Groups("insumo")
     * @SerializedName("unidadeProdutoId")
     * @return int
     */
    public function getUnidadeProdutoIdFormatted(): int
    {
        return $this->unidadeProdutoId;
    }


    /**
     * Para aceitar tanto em string quanto em double.
     * @Groups("insumo")
     * @SerializedName("unidadeProdutoId")
     * @param int $unidadeProdutoId
     */
    public function setUnidadeProdutoIdFormatted(int $unidadeProdutoId)
    {
        $this->unidadeProdutoId = $unidadeProdutoId;
    }
    
    

    

}
