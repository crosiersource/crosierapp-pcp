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
 *     normalizationContext={"groups"={"fichaTecnicaItem","fichaTecnica","insumo","tipoInsumo","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"fichaTecnicaItem"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/pcp/fichaTecnicaItem/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "put"={"path"="/pcp/fichaTecnicaItem/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "delete"={"path"="/pcp/fichaTecnicaItem/{id}", "security"="is_granted('ROLE_PCP_ADMIN')"},
 *     },
 *     collectionOperations={
 *          "get"={"path"="/pcp/fichaTecnicaItem", "security"="is_granted('ROLE_PCP')"},
 *          "post"={"path"="/pcp/fichaTecnicaItem", "security"="is_granted('ROLE_PCP')"}
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
 *     "tipoFichaTecnicaItem.descricao": "partial"
 * })
 * @ApiFilter(OrderFilter::class, properties={"id", "codigo", "descricao", "marca", "updated"}, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="App\EntityHandler\FichaTecnicaItemEntityHandler")
 *
 * @ORM\Table(name="prod_fichatecnica_item")
 * @ORM\Entity(repositoryClass="App\Repository\FichaTecnicaItemRepository")
 *
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaItem implements EntityId
{

    use EntityIdTrait;


    /**
     * @var null|FichaTecnica
     *
     * @ORM\ManyToOne(targetEntity="FichaTecnica", inversedBy="itens")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fichatecnica_id", referencedColumnName="id")
     * })
     * @Groups("fichaTecnicaItem")
     */
    public ?FichaTecnica $fichaTecnica = null;


    /**
     * @var null|Insumo
     *
     * @ORM\ManyToOne(targetEntity="Insumo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="insumo_id", referencedColumnName="id")
     * })
     * @Groups("fichaTecnicaItem")
     */
    public ?Insumo $insumo = null;

    /**
     * @ORM\OneToMany(
     *      targetEntity="FichaTecnicaItemQtde",
     *      mappedBy="fichaTecnicaItem",
     *      orphanRemoval=true,
     *     cascade={"all"}
     * )
     *
     * @var null|array|ArrayCollection
     */
    private $qtdes;

    /**
     * Transient.
     *
     * @var null|array
     */
    private ?array $qtdesTamanhosArray;


    public function __construct()
    {
        $this->qtdes = new ArrayCollection();
    }


    /**
     * @return FichaTecnicaItemQtde[]|ArrayCollection
     */
    public function getQtdes()
    {
        return $this->qtdes;
    }


    /**
     * @Groups("fichaTecnicaItem")
     * Retorna a qtde para cada tamanho.
     * @return array
     */
    public function getQtdesTamanhosArray(): array
    {
        return $this->qtdesTamanhosArray;
    }

    
    /**
     * @param array|null $qtdesTamanhosArray
     */
    public function setQtdesTamanhosArray(?array $qtdesTamanhosArray): void
    {
        $this->qtdesTamanhosArray = $qtdesTamanhosArray;
    }
    

    /**
     * @Groups("fichaTecnicaItem")
     * @return float
     */
    public function getTotalQtdes(): float
    {
        $totalQtdes = (float)0;
        if ($this->getQtdes()) {
            foreach ($this->getQtdes() as $qtde) {
                $totalQtdes = bcadd($totalQtdes, $qtde->getQtde(), 3);
            }
        }
        return $totalQtdes;
    }


    /**
     * @return void
     */
    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
            $qtdes = $this->getQtdes();
            $novasQtdes = new ArrayCollection();
            foreach ($qtdes as $qtde) {
                $novoQtde = clone $qtde;
                $novoQtde->setFichaTecnicaItem($this);
                $novasQtdes->add($novoQtde);
            }
            $this->qtdes = $novasQtdes;

        }
    }


}
