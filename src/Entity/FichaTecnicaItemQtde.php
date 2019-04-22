<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * FichaTecnicaItemQtde
 *
 * @ORM\Table(name="prod_confeccao_item_qtde")
 * @ORM\Entity
 *
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaItemQtde implements EntityId
{

    use EntityIdTrait;


    /**
     * @var string|null
     *
     * @ORM\Column(name="qtde", type="decimal", precision=15, scale=3, nullable=true)
     */
    private $qtde;

    /**
     * @var int
     *
     * @ORM\Column(name="grade_tamanho_id", type="bigint", nullable=false)
     */
    private $gradeTamanhoId;

    /**
     * @var FichaTecnicaItem
     *
     * @ORM\ManyToOne(targetEntity="FichaTecnicaItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="confeccao_item_id", referencedColumnName="id")
     * })
     */
    private $confeccaoItem;

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
     * @return int
     */
    public function getGradeTamanhoId(): int
    {
        return $this->gradeTamanhoId;
    }

    /**
     * @param int $gradeTamanhoId
     * @return FichaTecnicaItemQtde
     */
    public function setGradeTamanhoId(int $gradeTamanhoId): FichaTecnicaItemQtde
    {
        $this->gradeTamanhoId = $gradeTamanhoId;
        return $this;
    }

    /**
     * @return FichaTecnicaItem
     */
    public function getConfeccaoItem(): FichaTecnicaItem
    {
        return $this->confeccaoItem;
    }

    /**
     * @param FichaTecnicaItem $confeccaoItem
     * @return FichaTecnicaItemQtde
     */
    public function setConfeccaoItem(FichaTecnicaItem $confeccaoItem): FichaTecnicaItemQtde
    {
        $this->confeccaoItem = $confeccaoItem;
        return $this;
    }


}
