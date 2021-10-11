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
 *     normalizationContext={"groups"={"tipoInsumo","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"tipoInsumo"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/pcp/tipoInsumo/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "put"={"path"="/pcp/tipoInsumo/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "delete"={"path"="/pcp/tipoInsumo/{id}", "security"="is_granted('ROLE_PCP_ADMIN')"},
 *     },
 *     collectionOperations={
 *          "get"={"path"="/pcp/tipoInsumo", "security"="is_granted('ROLE_PCP')"},
 *          "post"={"path"="/pcp/tipoInsumo", "security"="is_granted('ROLE_PCP')"}
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
 *     "tipoTipoInsumo.descricao": "partial"
 * })
 * @ApiFilter(OrderFilter::class, properties={"id", "codigo", "descricao", "marca", "updated"}, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="App\EntityHandler\TipoInsumoEntityHandler")
 *
 * @ORM\Table(name="prod_tipo_insumo")
 * @ORM\Entity(repositoryClass="App\Repository\TipoInsumoRepository")
 *
 * @author Carlos Eduardo Pauluk
 */
class TipoInsumo implements EntityId
{

    use EntityIdTrait;

    /**
     * @var null|int
     *
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     * @Groups("tipoInsumo")
     */
    public ?int $codigo = null;

    
    /**
     * @var null|string
     *
     * @ORM\Column(name="descricao", type="string", length=100, nullable=false)
     * @Groups("tipoInsumo")
     */
    public ?string $descricao = null;

    
    /**
     * @var null|int
     *
     * @ORM\Column(name="unidade_produto_id", type="bigint", nullable=false)
     * @Groups("tipoInsumo")
     */
    public ?int $unidadeProdutoId = null;


    public function getCodigo($format = false)
    {
        if ($format) {
            return str_pad($this->codigo, 3, '0', STR_PAD_LEFT);
        }

        return $this->codigo;
    }

    /**
     * @return string
     * @Groups("tipoInsumo")
     */
    public function getDescricaoMontada(): string
    {
        return $this->getCodigo(true) . ' - ' . $this->descricao;
    }


}
