<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"loteProducao","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"loteProducao"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/pcp/loteProducao/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "put"={"path"="/pcp/loteProducao/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "delete"={"path"="/pcp/loteProducao/{id}", "security"="is_granted('ROLE_PCP_ADMIN')"},
 *     },
 *     collectionOperations={
 *          "get"={"path"="/pcp/loteProducao", "security"="is_granted('ROLE_PCP')"},
 *          "post"={"path"="/pcp/loteProducao", "security"="is_granted('ROLE_PCP')"}
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
 *     "tipoLoteProducao.descricao": "partial"
 * })
 * @ApiFilter(OrderFilter::class, properties={"id", "codigo", "descricao", "marca", "updated"}, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="App\EntityHandler\LoteProducaoEntityHandler")
 *
 * @ORM\Table(name="prod_lote_producao")
 * @ORM\Entity(repositoryClass="App\Repository\LoteProducaoRepository")
 *
 * @author Carlos Eduardo Pauluk
 */
class LoteProducao implements EntityId
{

    use EntityIdTrait;

    /**
     * @var null|int
     *
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     * @Groups("loteProducao")
     */
    public ?int $codigo = null;

    
    /**
     * @var null|string
     *
     * @ORM\Column(name="descricao", type="string", length=200, nullable=false)
     * @Groups("loteProducao")
     */
    public ?string $descricao = null;

    
    /**
     * @var null|\DateTime
     *
     * @ORM\Column(name="dt_lote", type="date", nullable=true)
     * @Groups("loteProducao")
     */
    public ?\DateTime $dtLote = null;

    
    /**
     *
     * @var LoteProducaoItem[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="LoteProducaoItem",
     *      mappedBy="loteProducao",
     *      orphanRemoval=true
     * )
     */
    private $itens;

    public function __construct()
    {
        $this->itens = new ArrayCollection();
    }


    

    /**
     * @Groups("loteProducao")
     * @return Collection|LoteProducaoItem[]
     */
    public function getItens(): Collection
    {
        return $this->itens;
    }

    /**
     * @param LoteProducaoItem[]|ArrayCollection $itens
     * @return LoteProducao
     */
    public function setItens($itens): LoteProducao
    {
        $this->itens = $itens;
        return $this;
    }


}
