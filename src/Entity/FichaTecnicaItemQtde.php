<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * FichaTecnicaItemQtde
 *
 * @ORM\Table(name="prod_fichatecnica_item_qtde")
 * @ORM\Entity
 *
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaItemQtde implements EntityId
{

    use EntityIdTrait;


    /**
     * @var null|string
     *
     * @ORM\Column(name="qtde", type="decimal", precision=15, scale=3, nullable=true)
     * @Groups("entity")
     */
    private $qtde;

    /**
     * @var null|int
     *
     * @ORM\Column(name="grade_tamanho_id", type="bigint", nullable=false)
     * @Groups("entity")
     */
    private $gradeTamanhoId;

    /**
     * @var null|FichaTecnicaItem
     *
     * @ORM\ManyToOne(targetEntity="FichaTecnicaItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fichatecnica_item_id", referencedColumnName="id")
     * })
     * @Groups("entity")
     */
    private $fichaTecnicaItem;


    /**
     * @return null|string
     */
    public function getQtde(): ?string
    {
        return $this->qtde;
    }

    /**
     * @param null|string $qtde
     * @return FichaTecnicaItemQtde
     */
    public function setQtde(?string $qtde): FichaTecnicaItemQtde
    {
        $this->qtde = $qtde;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getGradeTamanhoId(): ?int
    {
        return $this->gradeTamanhoId;
    }

    /**
     * @param int|null $gradeTamanhoId
     * @return FichaTecnicaItemQtde
     */
    public function setGradeTamanhoId(?int $gradeTamanhoId): FichaTecnicaItemQtde
    {
        $this->gradeTamanhoId = $gradeTamanhoId;
        return $this;
    }

    /**
     * @return FichaTecnicaItem|null
     */
    public function getFichaTecnicaItem(): ?FichaTecnicaItem
    {
        return $this->fichaTecnicaItem;
    }

    /**
     * @param FichaTecnicaItem|null $fichaTecnicaItem
     * @return FichaTecnicaItemQtde
     */
    public function setFichaTecnicaItem(?FichaTecnicaItem $fichaTecnicaItem): FichaTecnicaItemQtde
    {
        $this->fichaTecnicaItem = $fichaTecnicaItem;
        return $this;
    }


}
