<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * LoteProducaoItemQtde
 *
 * @ORM\Table(name="prod_lote_confeccao_item_qtde")
 * @ORM\Entity
 *
 * @author Carlos Eduardo Pauluk
 */
class LoteProducaoItemQtde implements EntityId
{

    use EntityIdTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="qtde", type="integer", nullable=false)
     * @Groups("entity")
     */
    private $qtde;

    /**
     * @var int
     *
     * @ORM\Column(name="grade_tamanho_id", type="bigint", nullable=false)
     * @Groups("entity")
     */
    private $gradeTamanhoId;

    /**
     * @var LoteProducaoItem
     *
     * @ORM\ManyToOne(targetEntity="LoteProducaoItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lote_confeccao_item_id", referencedColumnName="id")
     * })
     * @Groups("entity")
     */
    private $loteProducaoItem;


    /**
     * @return int
     */
    public function getQtde(): int
    {
        return $this->qtde;
    }

    /**
     * @param int $qtde
     * @return LoteProducaoItemQtde
     */
    public function setQtde(int $qtde): LoteProducaoItemQtde
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
     * @return LoteProducaoItemQtde
     */
    public function setGradeTamanhoId(int $gradeTamanhoId): LoteProducaoItemQtde
    {
        $this->gradeTamanhoId = $gradeTamanhoId;
        return $this;
    }

    /**
     * @return LoteProducaoItem
     */
    public function getLoteProducaoItem(): LoteProducaoItem
    {
        return $this->loteProducaoItem;
    }

    /**
     * @param LoteProducaoItem $loteProducaoItem
     * @return LoteProducaoItemQtde
     */
    public function setLoteProducaoItem(LoteProducaoItem $loteProducaoItem): LoteProducaoItemQtde
    {
        $this->loteProducaoItem = $loteProducaoItem;
        return $this;
    }


}
