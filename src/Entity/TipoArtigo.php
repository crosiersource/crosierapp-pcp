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
 *     normalizationContext={"groups"={"tipoArtigo","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"tipoArtigo"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/pcp/tipoArtigo/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "put"={"path"="/pcp/tipoArtigo/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "delete"={"path"="/pcp/tipoArtigo/{id}", "security"="is_granted('ROLE_PCP_ADMIN')"},
 *     },
 *     collectionOperations={
 *          "get"={"path"="/pcp/tipoArtigo", "security"="is_granted('ROLE_PCP')"},
 *          "post"={"path"="/pcp/tipoArtigo", "security"="is_granted('ROLE_PCP')"}
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
 *     "tipoTipoArtigo.descricao": "partial"
 * })
 * @ApiFilter(OrderFilter::class, properties={"id", "codigo", "descricao", "marca", "updated"}, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="App\EntityHandler\TipoArtigoEntityHandler")
 *
 * @ORM\Table(name="prod_tipo_artigo")
 * @ORM\Entity(repositoryClass="App\Repository\TipoArtigoRepository")
 *
 * @author Carlos Eduardo Pauluk
 */
class TipoArtigo implements EntityId
{

    use EntityIdTrait;


    /**
     * @var null|int
     *
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     * @Groups("tipoArtigo")
     */
    public ?int $codigo = null;

    
    /**
     * @var null|string
     *
     * @ORM\Column(name="descricao", type="string", length=100, nullable=false)
     * @Groups("tipoArtigo")
     */
    public ?string $descricao = null;

    
    /**
     * @var null|string
     *
     * @ORM\Column(name="modo_calculo", type="string", length=15, nullable=false)
     * @Groups("tipoArtigo")
     */
    public ?string $modoCalculo = null;

    
    /**
     * @var null|int
     *
     * @ORM\Column(name="subdepto_id", type="bigint", nullable=false)
     * @Groups("tipoArtigo")
     */
    public ?int $subdeptoId = null;

    
    /**
     * @return string
     * @Groups("tipoArtigo")
     */
    public function getDescricaoMontada(): string
    {
        return $this->getCodigo(true) . ' - ' . $this->descricao;
    }

    /**
     * @return int|string|null
     */
    public function getCodigo(?bool $format = false)
    {
        if ($format) {
            return str_pad($this->codigo, 3, '0', STR_PAD_LEFT);
        }

        return $this->codigo;
    }


}
