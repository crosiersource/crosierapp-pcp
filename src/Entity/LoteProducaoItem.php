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
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"loteProducaoItem","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"loteProducaoItem"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/pcp/loteProducaoItem/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "put"={"path"="/pcp/loteProducaoItem/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "delete"={"path"="/pcp/loteProducaoItem/{id}", "security"="is_granted('ROLE_PCP_ADMIN')"},
 *     },
 *     collectionOperations={
 *          "get"={"path"="/pcp/loteProducaoItem", "security"="is_granted('ROLE_PCP')"},
 *          "post"={"path"="/pcp/loteProducaoItem", "security"="is_granted('ROLE_PCP')"}
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
 *     "tipoLoteProducaoItem.descricao": "partial"
 * })
 * @ApiFilter(OrderFilter::class, properties={"id", "codigo", "descricao", "marca", "updated"}, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="App\EntityHandler\LoteProducaoItemEntityHandler")
 *
 * @ORM\Table(name="prod_lote_producao_item")
 * @ORM\Entity
 *
 * @author Carlos Eduardo Pauluk
 */
class LoteProducaoItem implements EntityId
{

    use EntityIdTrait;


    /**
     * @var null|string
     *
     * @ORM\Column(name="pedido", type="string", length=50, nullable=true)
     * @Groups("loteProducaoItem")
     */
    public ?string $pedido = null;

    
    /**
     * @var null|string
     *
     * @ORM\Column(name="obs", type="string", length=5000, nullable=true)
     * @Groups("loteProducaoItem")
     */
    public ?string $obs = null;

    
    /**
     * @var null|int
     *
     * @ORM\Column(name="ordem", type="integer", nullable=false)
     * @Groups("loteProducaoItem")
     */
    public ?int $ordem = null;

    
    /**
     * @var null|FichaTecnica
     *
     * @ORM\ManyToOne(targetEntity="FichaTecnica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fichatecnica_id", referencedColumnName="id")
     * })
     * @Groups("loteProducaoItem")
     */
    public ?FichaTecnica $fichaTecnica = null;

    
    /**
     * @var null|LoteProducao
     *
     * @ORM\ManyToOne(targetEntity="LoteProducao",inversedBy="itens")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lote_producao_id", referencedColumnName="id")
     * })
     * @Groups("loteProducaoItem")
     */
    public ?LoteProducao $loteProducao = null;

    
    /**
     *
     * @var LoteProducaoItemQtde[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="LoteProducaoItemQtde",
     *      mappedBy="loteProducaoItem",
     *      orphanRemoval=true,
     *     cascade={"all"}
     * )
     */
    private $qtdes;

    /**
     * Transient.
     *
     * @var array
     */
    private array $qtdesTamanhosArray;

    /**
     * Transient.
     *
     * @var integer
     */
    public int $totalQtdes;


    public function __construct()
    {
        $this->qtdes = new ArrayCollection();
    }

    

    /**
     * @Groups("loteProducaoItem")
     * @return LoteProducaoItemQtde[]|ArrayCollection
     */
    public function getQtdes()
    {
        return $this->qtdes;
    }

    /**
     * @param LoteProducaoItemQtde[]|ArrayCollection $qtdes
     * @return LoteProducaoItem
     */
    public function setQtdes($qtdes): LoteProducaoItem
    {
        $this->qtdes = $qtdes;
        return $this;
    }


    /**
     * @Groups("loteProducaoItem")
     * @return array
     */
    public function getQtdesTamanhosArray(): array
    {
        return $this->qtdesTamanhosArray;
    }

    /**
     * @param array $qtdesTamanhosArray
     */
    public function setQtdesTamanhosArray(array $qtdesTamanhosArray): void
    {
        $this->qtdesTamanhosArray = $qtdesTamanhosArray;
    }

    /**
     * @Groups("loteProducaoItem")
     * @return int
     */
    public function getTotalQtdes(): int
    {
        $this->totalQtdes = 0;
        if ($this->getQtdes()) {
            foreach ($this->getQtdes() as $qtde) {
                $this->totalQtdes += $qtde->getQtde();
            }
        }
        return $this->totalQtdes;
    }


}
