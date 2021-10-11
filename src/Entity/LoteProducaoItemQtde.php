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
 *     normalizationContext={"groups"={"loteProducaoItemQtde","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"loteProducaoItemQtde"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/pcp/loteProducaoItemQtde/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "put"={"path"="/pcp/loteProducaoItemQtde/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "delete"={"path"="/pcp/loteProducaoItemQtde/{id}", "security"="is_granted('ROLE_PCP_ADMIN')"},
 *     },
 *     collectionOperations={
 *          "get"={"path"="/pcp/loteProducaoItemQtde", "security"="is_granted('ROLE_PCP')"},
 *          "post"={"path"="/pcp/loteProducaoItemQtde", "security"="is_granted('ROLE_PCP')"}
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
 *     "tipoLoteProducaoItemQtde.descricao": "partial"
 * })
 * @ApiFilter(OrderFilter::class, properties={"id", "codigo", "descricao", "marca", "updated"}, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="App\EntityHandler\LoteProducaoItemQtdeEntityHandler")
 *
 * @ORM\Table(name="prod_lote_producao_item_qtde")
 * @ORM\Entity
 *
 * @author Carlos Eduardo Pauluk
 */
class LoteProducaoItemQtde implements EntityId
{

    use EntityIdTrait;

    /**
     * @var null|int
     *
     * @ORM\Column(name="qtde", type="integer", nullable=false)
     * @Groups("loteProducaoItemQtde")
     */
    public ?int $qtde = null;

    
    /**
     * @var null|int
     *
     * @ORM\Column(name="grade_tamanho_id", type="bigint", nullable=false)
     * @Groups("loteProducaoItemQtde")
     */
    public ?int $gradeTamanhoId = null;

    
    /**
     * @var null|LoteProducaoItem
     *
     * @ORM\ManyToOne(targetEntity="LoteProducaoItem", inversedBy="qtdes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lote_producao_item_id", referencedColumnName="id")
     * })
     * @Groups("loteProducaoItemQtde")
     */
    public ?LoteProducaoItem $loteProducaoItem = null;



}
