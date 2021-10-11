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
 *     normalizationContext={"groups"={"fichaTecnicaItemQtde","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"fichaTecnicaItemQtde"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/pcp/fichaTecnica/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "put"={"path"="/pcp/fichaTecnica/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "delete"={"path"="/pcp/fichaTecnica/{id}", "security"="is_granted('ROLE_PCP_ADMIN')"},
 *     },
 *     collectionOperations={
 *          "get"={"path"="/pcp/fichaTecnicaItemQtde", "security"="is_granted('ROLE_PCP')"},
 *          "post"={"path"="/pcp/fichaTecnicaItemQtde", "security"="is_granted('ROLE_PCP')"}
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
 * @EntityHandler(entityHandlerClass="App\EntityHandler\FichaTecnicaItemQtdeEntityHandler")
 *
 * @ORM\Table(name="prod_fichatecnica_item_qtde")
 * @ORM\Entity
 *
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaItemQtde implements EntityId
{

    use EntityIdTrait;


    /**
     * @var null|string
     *
     * @ORM\Column(name="qtde", type="decimal", precision=15, scale=3, nullable=true)
     * @Groups("fichaTecnicaItemQtde")
     */
    public ?string $qtde = null;

    /**
     * @var null|int
     *
     * @ORM\Column(name="grade_tamanho_id", type="bigint", nullable=false)
     * @Groups("fichaTecnicaItemQtde")
     */
    public ?int $gradeTamanhoId = null;

    /**
     * @var null|FichaTecnicaItem
     *
     * @ORM\ManyToOne(targetEntity="FichaTecnicaItem", inversedBy="qtdes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fichatecnica_item_id", referencedColumnName="id")
     * })
     * @Groups("fichaTecnicaItemQtde")
     */
    public ?FichaTecnicaItem $fichaTecnicaItem = null;



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
