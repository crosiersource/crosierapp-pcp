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
     * @var null|int
     *
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     * @Groups("entity")
     */
    private $codigo;

    /**
     * @var null|string
     *
     * @ORM\Column(name="descricao", type="string", length=100, nullable=false)
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


    public function getCodigo($format = false)
    {
        if ($format) {
            return str_pad($this->codigo, 3, '0', STR_PAD_LEFT);
        }

        return $this->codigo;
    }

    /**
     * @return null|string
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * @return string
     * @Groups("entity")
     */
    public function getDescricaoMontada(): string
    {
        return $this->getCodigo(true) . ' - ' . $this->getDescricao();
    }

    /**
     * @param null|string $descricao
     * @return TipoInsumo
     */
    public function setDescricao(?string $descricao): TipoInsumo
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
     * @return TipoInsumo
     */
    public function setUnidadeProdutoId(?int $unidadeProdutoId): TipoInsumo
    {
        $this->unidadeProdutoId = $unidadeProdutoId;
        return $this;
    }


}
