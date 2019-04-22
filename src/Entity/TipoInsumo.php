<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * TipoInsumo
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
     * @var int
     *
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     * @Groups("entity")
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="descricao", type="string", length=100, nullable=false)
     * @Groups("entity")
     */
    private $descricao;

    /**
     * @var int
     *
     * @ORM\Column(name="unidade_produto_id", type="bigint", nullable=false)
     * @Groups("entity")
     */
    private $unidadeProdutoId;

    /**
     * @return int
     */
    public function getCodigo(): int
    {
        return $this->codigo;
    }

    /**
     * @param int $codigo
     * @return TipoInsumo
     */
    public function setCodigo(int $codigo): TipoInsumo
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
     * @return TipoInsumo
     */
    public function setDescricao(string $descricao): TipoInsumo
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
     * @return TipoInsumo
     */
    public function setUnidadeProdutoId(int $unidadeProdutoId): TipoInsumo
    {
        $this->unidadeProdutoId = $unidadeProdutoId;
        return $this;
    }


}
