<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"insumoPreco","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"insumoPreco"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/pcp/insumoPreco/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "put"={"path"="/pcp/insumoPreco/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "delete"={"path"="/pcp/insumoPreco/{id}", "security"="is_granted('ROLE_PCP_ADMIN')"},
 *     },
 *     collectionOperations={
 *          "get"={"path"="/pcp/insumoPreco", "security"="is_granted('ROLE_PCP')"},
 *          "post"={"path"="/pcp/insumoPreco", "security"="is_granted('ROLE_PCP')"}
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
 *     "tipoInsumoPreco.descricao": "partial"
 * })
 * @ApiFilter(OrderFilter::class, properties={"id", "codigo", "descricao", "marca", "updated"}, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="App\EntityHandler\InsumoPrecoEntityHandler")
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
     * @var null|Insumo
     *
     * @ORM\ManyToOne(targetEntity="Insumo", inversedBy="precos", inversedBy="precos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="insumo_id", referencedColumnName="id")
     * })
     */
    public ?Insumo $insumo = null;

    
    /**
     * @var null|float
     *
     * @ORM\Column(name="coeficiente", type="float", precision=10, scale=0, nullable=false)
     * @Groups("insumoPreco")
     */
    public ?float $coeficiente = null;

    
    /**
     * @var null|float
     *
     * @ORM\Column(name="custo_operacional", type="float", precision=10, scale=0, nullable=false)
     * @Groups("insumoPreco")
     */
    public ?float $custoOperacional = null;

    
    /**
     * @var null|\DateTime
     *
     * @ORM\Column(name="dt_custo", type="date", nullable=false)
     * @Groups("insumoPreco")
     */
    public ?\DateTime $dtCusto = null;

    
    /**
     * @var null|float
     *
     * @ORM\Column(name="margem", type="float", precision=10, scale=0, nullable=false)
     * @Groups("insumoPreco")
     */
    public ?float $margem = null;

    
    /**
     * @var null|int
     *
     * @ORM\Column(name="prazo", type="integer", nullable=false)
     * @Groups("insumoPreco")
     */
    public ?int $prazo = null;

    
    /**
     * @var null|float
     *
     * @ORM\Column(name="preco_custo", type="float", precision=15, scale=2, nullable=false)
     * @Groups("insumoPreco")
     */
    public ?float $precoCusto = null;

    
    /**
     * @var null|float
     *
     * @ORM\Column(name="preco_prazo", type="float", precision=10, scale=0, nullable=false)
     * @Groups("insumoPreco")
     */
    public ?float $precoPrazo = null;

    
    /**
     * @var null|float
     *
     * @ORM\Column(name="preco_vista", type="float", precision=10, scale=0, nullable=false)
     * @Groups("insumoPreco")
     */
    public ?float $precoVista = null;

    
    /**
     * @var null|int
     *
     * @ORM\Column(name="fornecedor_id", type="bigint", nullable=true)
     * @Groups("insumoPreco")
     */
    public ?int $fornecedorId = null;

    
    /**
     * @var null|string
     *
     * @ORM\Column(name="custo_financeiro", type="decimal", precision=19, scale=2, nullable=false)
     * @Groups("insumoPreco")
     */
    public ?string $custoFinanceiro = null;

    
    /**
     * @var null|bool
     *
     * @ORM\Column(name="atual", type="boolean", nullable=false)
     * @Groups("insumoPreco")
     */
    public ?bool $atual = null;

    
    
}
