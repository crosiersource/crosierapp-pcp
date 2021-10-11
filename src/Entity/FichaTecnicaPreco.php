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
 *     normalizationContext={"groups"={"fichaTecnicaPreco","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"fichaTecnicaPreco"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/pcp/fichaTecnica/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "put"={"path"="/pcp/fichaTecnica/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "delete"={"path"="/pcp/fichaTecnica/{id}", "security"="is_granted('ROLE_PCP_ADMIN')"},
 *     },
 *     collectionOperations={
 *          "get"={"path"="/pcp/fichaTecnicaPreco", "security"="is_granted('ROLE_PCP')"},
 *          "post"={"path"="/pcp/fichaTecnicaPreco", "security"="is_granted('ROLE_PCP')"}
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
 *     "tipoFichaTecnica.descricao": "partial"
 * })
 * @ApiFilter(OrderFilter::class, properties={"id", "codigo", "descricao", "marca", "updated"}, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="App\EntityHandler\FichaTecnicaEntityHandler")
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
     * @Groups("fichaTecnicaPreco")
     */
    public ?float $coeficiente = null;

    
    /**
     * @var null|float
     *
     * @ORM\Column(name="custo_operacional", type="float", precision=10, scale=0, nullable=false)
     * @Groups("fichaTecnicaPreco")
     */
    public ?float $custoOperacional = null;

    
    /**
     * @var null|string
     *
     * @ORM\Column(name="descricao", type="string", length=200, nullable=false)
     * @Groups("fichaTecnicaPreco")
     */
    public ?string $descricao = null;

    
    /**
     * @var null|\DateTime
     *
     * @ORM\Column(name="dt_custo", type="date", nullable=false)
     * @Groups("fichaTecnicaPreco")
     */
    public ?\DateTime $dtCusto = null;

    
    /**
     * @var null|float
     *
     * @ORM\Column(name="margem", type="float", precision=10, scale=0, nullable=false)
     * @Groups("fichaTecnicaPreco")
     */
    public ?float $margem = null;

    
    /**
     * @var null|int
     *
     * @ORM\Column(name="prazo", type="integer", nullable=false)
     * @Groups("fichaTecnicaPreco")
     */
    public ?int $prazo = null;

    
    /**
     * @var null|float
     *
     * @ORM\Column(name="preco_custo", type="float", precision=10, scale=0, nullable=false)
     * @Groups("fichaTecnicaPreco")
     */
    public ?float $precoCusto = null;

    
    /**
     * @var null|float
     *
     * @ORM\Column(name="preco_prazo", type="float", precision=10, scale=0, nullable=false)
     * @Groups("fichaTecnicaPreco")
     */
    public ?float $precoPrazo = null;

    
    /**
     * @var null|float
     *
     * @ORM\Column(name="preco_vista", type="float", precision=10, scale=0, nullable=false)
     * @Groups("fichaTecnicaPreco")
     */
    public ?float $precoVista = null;

    
    /**
     * @var null|string
     *
     * @ORM\Column(name="custo_financeiro", type="decimal", precision=19, scale=2, nullable=false)
     * @Groups("fichaTecnicaPreco")
     */
    public ?string $custoFinanceiro = null;

    
    /**
     * @var null|FichaTecnica
     *
     * @ORM\ManyToOne(targetEntity="FichaTecnicaPreco")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fichatecnica_id", referencedColumnName="id")
     * })
     * @Groups("fichaTecnicaPreco")
     */
    public ?FichaTecnica $fichaTecnica = null;


    /**
     * @return void
     */
    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
        }
    }


}
