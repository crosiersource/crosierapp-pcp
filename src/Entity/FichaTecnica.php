<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use CrosierSource\CrosierLibRadxBundle\Entity\CRM\Cliente;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"fichaTecnica","tipoArtigo","cliente","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"fichaTecnica"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/pcp/fichaTecnica/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "put"={"path"="/pcp/fichaTecnica/{id}", "security"="is_granted('ROLE_PCP')"},
 *          "delete"={"path"="/pcp/fichaTecnica/{id}", "security"="is_granted('ROLE_PCP_ADMIN')"},
 *     },
 *     collectionOperations={
 *          "get"={"path"="/pcp/fichaTecnica", "security"="is_granted('ROLE_PCP')"},
 *          "post"={"path"="/pcp/fichaTecnica", "security"="is_granted('ROLE_PCP')"}
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
 * @ORM\Table(name="prod_fichatecnica")
 * @ORM\Entity(repositoryClass="App\Repository\FichaTecnicaRepository")
 *
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnica implements EntityId
{

    use EntityIdTrait;


    /**
     * @var null|string
     *
     * @ORM\Column(name="descricao", type="string", length=200, nullable=false)
     * @Groups("fichaTecnica")
     */
    public ?string $descricao = null;


    /**
     * @var null|TipoArtigo
     *
     * @ORM\ManyToOne(targetEntity="TipoArtigo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_artigo_id", referencedColumnName="id")
     * })
     * @Groups("fichaTecnica")
     */
    public ?TipoArtigo $tipoArtigo = null;


    /**
     * @var null|bool
     *
     * @ORM\Column(name="bloqueada", type="boolean", nullable=false)
     * @Groups("fichaTecnica")
     */
    public ?bool $bloqueada = null;


    /**
     * @var null|float
     *
     * @ORM\Column(name="custo_operacional_padrao", type="float", precision=10, scale=3, nullable=false)
     * @Groups("fichaTecnica")
     */
    public ?float $custoOperacionalPadrao = null;


    /**
     * @var null|float
     *
     * @ORM\Column(name="margem_padrao", type="float", precision=10, scale=2, nullable=false)
     * @Groups("fichaTecnica")
     */
    public ?float $margemPadrao = null;


    /**
     * @var null|string
     *
     * @ORM\Column(name="obs", type="string", length=5000, nullable=true)
     * @Groups("fichaTecnica")
     */
    public ?string $obs = null;


    /**
     * @var null|int
     *
     * @ORM\Column(name="prazo_padrao", type="integer", nullable=false)
     * @Groups("fichaTecnica")
     */
    public ?int $prazoPadrao = null;


    /**
     * @var null|string
     *
     * @ORM\Column(name="custo_financeiro_padrao", type="decimal", precision=19, scale=2, nullable=false)
     * @Groups("fichaTecnica")
     */
    public ?string $custoFinanceiroPadrao = null;


    /**
     * @var null|string
     *
     * @ORM\Column(name="modo_calculo", type="string", length=15, nullable=false)
     * @Groups("fichaTecnica")
     */
    public ?string $modoCalculo = null;


    /**
     * @var null|int
     *
     * @ORM\Column(name="grade_id", type="bigint", nullable=false)
     * @Groups("fichaTecnica")
     */
    public ?int $gradeId = null;


    /**
     * Transient.
     *
     * @var array
     */
    public array $gradesTamanhosByPosicaoArray = [];


    /**
     * @var null|bool
     *
     * @ORM\Column(name="oculta", type="boolean", nullable=false)
     * @Groups("fichaTecnica")
     */
    public ?bool $oculta = null;


    /**
     * @var null|Cliente
     *
     * @ORM\ManyToOne(targetEntity="CrosierSource\CrosierLibRadxBundle\Entity\CRM\Cliente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     * })
     * @Groups("entity","cliente")
     */
    public ?Cliente $instituicao = null;


    /**
     *
     * @var FichaTecnicaItem[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="FichaTecnicaItem",
     *      mappedBy="fichaTecnica",
     *      orphanRemoval=true,
     *     cascade={"all"}
     * )
     */
    public $itens;


    /**
     *
     * @var FichaTecnicaPreco[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="FichaTecnicaPreco",
     *      mappedBy="fichaTecnica",
     *      orphanRemoval=true,
     *     cascade={"all"}
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $precos;


    public function __construct()
    {
        $this->itens = new ArrayCollection();
        $this->precos = new ArrayCollection();
    }


    /**
     * @Groups("fichaTecnica")
     * @return null|string
     */
    public function getDescricaoMontada(): ?string
    {
        if ($this->id && $this->descricao) {
            return str_pad($this->id, 6, '0', STR_PAD_LEFT) . ' - ' . $this->descricao;
        }
        return null;
    }


    /**
     * Ex.:
     * (
     * [1] => 02
     * [2] => 04
     * [3] => 06
     * [4] => 08
     * [5] => 10
     * [6] => 12
     * [7] => 14
     * [8] => 16
     * [9] => P
     * [10] => M
     * [11] => G
     * [12] => XG
     * [13] => SG
     * [14] => SS
     * [15] => -
     * )
     *
     * @Groups("fichaTecnica")
     *
     * @return array
     */
    public function getGradesTamanhosByPosicaoArray(): array
    {
        return $this->gradesTamanhosByPosicaoArray;
    }

    /**
     * @param array $gradesTamanhosByPosicaoArray
     */
    public function setGradesTamanhosByPosicaoArray(array $gradesTamanhosByPosicaoArray): void
    {
        $this->gradesTamanhosByPosicaoArray = $gradesTamanhosByPosicaoArray;
    }
    

    /**
     * @return void
     */
    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
            $itens = $this->getItens();
            $novosItens = new ArrayCollection();
            /** @var FichaTecnicaItem $item */
            foreach ($itens as $item) {
                $novoItem = clone $item;
                $novoItem->fichaTecnica = ($this);
                $novosItens->add($novoItem);
            }
            $this->itens = $novosItens;

            $precos = $this->getPrecos();
            $novosPrecos = new ArrayCollection();
            /** @var FichaTecnicaPreco $preco */
            foreach ($precos as $preco) {
                $novoPreco = clone $preco;
                $novoPreco->fichaTecnica = ($this);
                $novosPrecos->add($novoPreco);
            }
            $this->precos = $novosPrecos;
        }
    }


    /**
     * @Groups("fichaTecnica")
     * @return FichaTecnicaItem[]|ArrayCollection
     */
    public function getItens()
    {
        return $this->itens;
    }


    /**
     * @Groups("fichaTecnica")
     * @return FichaTecnicaPreco[]|ArrayCollection
     */
    public function getPrecos()
    {
        return $this->precos;
    }


}
