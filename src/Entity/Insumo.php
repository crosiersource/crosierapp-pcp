<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;

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
     * @var int
     *
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="descricao", type="string", length=200, nullable=false)
     */
    private $descricao;

    /**
     * @var int
     *
     * @ORM\Column(name="unidade_produto_id", type="bigint", nullable=false)
     */
    private $unidadeProdutoId;

    /**
     * @var TipoInsumo
     *
     * @ORM\ManyToOne(targetEntity="TipoInsumo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_insumo_id", referencedColumnName="id")
     * })
     */
    private $tipoInsumo;

    /**
     * @return int
     */
    public function getCodigo(): int
    {
        return $this->codigo;
    }

    /**
     * @param int $codigo
     * @return Insumo
     */
    public function setCodigo(int $codigo): Insumo
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescricao(): string
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     * @return Insumo
     */
    public function setDescricao(string $descricao): Insumo
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * @return int
     */
    public function getUnidadeProdutoId(): int
    {
        return $this->unidadeProdutoId;
    }

    /**
     * @param int $unidadeProdutoId
     * @return Insumo
     */
    public function setUnidadeProdutoId(int $unidadeProdutoId): Insumo
    {
        $this->unidadeProdutoId = $unidadeProdutoId;
        return $this;
    }

    /**
     * @return TipoInsumo
     */
    public function getTipoInsumo(): TipoInsumo
    {
        return $this->tipoInsumo;
    }

    /**
     * @param TipoInsumo $tipoInsumo
     * @return Insumo
     */
    public function setTipoInsumo(TipoInsumo $tipoInsumo): Insumo
    {
        $this->tipoInsumo = $tipoInsumo;
        return $this;
    }


}
