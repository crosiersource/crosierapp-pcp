<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * TipoArtigo
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
     * @var null|string
     *
     * @ORM\Column(name="modo_calculo", type="string", length=15, nullable=false)
     * @Groups("entity")
     */
    private $modoCalculo;

    /**
     * @var null|int
     *
     * @ORM\Column(name="subdepto_id", type="bigint", nullable=false)
     * @Groups("entity")
     */
    private $subdeptoId;

    /**
     * @return string
     * @Groups("entity")
     */
    public function getDescricaoMontada(): string
    {
        return $this->getCodigo(true) . ' - ' . $this->getDescricao();
    }

    public function getCodigo($format = false)
    {
        if ($format) {
            return str_pad($this->codigo, 3, '0', STR_PAD_LEFT);
        }

        return $this->codigo;
    }

    /**
     * @param int|null $codigo
     * @return TipoArtigo
     */
    public function setCodigo(?int $codigo): TipoArtigo
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
     * @return TipoArtigo
     */
    public function setDescricao(?string $descricao): TipoArtigo
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getModoCalculo(): ?string
    {
        return $this->modoCalculo;
    }

    /**
     * @param null|string $modoCalculo
     * @return TipoArtigo
     */
    public function setModoCalculo(?string $modoCalculo): TipoArtigo
    {
        $this->modoCalculo = $modoCalculo;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSubdeptoId(): ?int
    {
        return $this->subdeptoId;
    }

    /**
     * @param int|null $subdeptoId
     * @return TipoArtigo
     */
    public function setSubdeptoId(?int $subdeptoId): TipoArtigo
    {
        $this->subdeptoId = $subdeptoId;
        return $this;
    }


}
