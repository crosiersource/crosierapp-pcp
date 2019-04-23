<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Insumo
 *
 * @ORM\Table(name="prod_insumo")
 * @ORM\Entity(repositoryClass="App\Repository\InsumoRepository")
 *
 * @author Carlos Eduardo Pauluk
 */
class Insumo implements EntityId
{

    use EntityIdTrait;

    /**
     * @var null|int
     *
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     * @Groups("entity")
     */
    private $codigo;

    /**
     * @var null|string
     *
     * @ORM\Column(name="descricao", type="string", length=200, nullable=false)
     * @Groups("entity")
     */
    private $descricao;

    /**
     * @var null|int
     *
     * @ORM\Column(name="unidade_produto_id", type="bigint", nullable=false)
     * @Groups("entity")
     */
    private $unidadeProdutoId;

    /**
     * @var null|TipoInsumo
     *
     * @ORM\ManyToOne(targetEntity="TipoInsumo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_insumo_id", referencedColumnName="id")
     * })
     * @Groups("entity")
     */
    private $tipoInsumo;


    public function getCodigo($format = false)
    {
        if ($format) {
            return str_pad($this->codigo, 3, '0', STR_PAD_LEFT);
        }

        return $this->codigo;
    }

    /**
     * @param int|null $codigo
     * @return Insumo
     */
    public function setCodigo(?int $codigo): Insumo
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * @param null|string $descricao
     * @return Insumo
     */
    public function setDescricao(?string $descricao): Insumo
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getUnidadeProdutoId(): ?int
    {
        return $this->unidadeProdutoId;
    }

    /**
     * @param int|null $unidadeProdutoId
     * @return Insumo
     */
    public function setUnidadeProdutoId(?int $unidadeProdutoId): Insumo
    {
        $this->unidadeProdutoId = $unidadeProdutoId;
        return $this;
    }

    /**
     * @return TipoInsumo|null
     */
    public function getTipoInsumo(): ?TipoInsumo
    {
        return $this->tipoInsumo;
    }

    /**
     * @param TipoInsumo|null $tipoInsumo
     * @return Insumo
     */
    public function setTipoInsumo(?TipoInsumo $tipoInsumo): Insumo
    {
        $this->tipoInsumo = $tipoInsumo;
        return $this;
    }


}
