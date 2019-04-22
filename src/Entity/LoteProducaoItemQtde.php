<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;

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
     */
    private $qtde;

    /**
     * @var int
     *
     * @ORM\Column(name="grade_tamanho_id", type="bigint", nullable=false)
     */
    private $gradeTamanhoId;

    /**
     * @var LoteProducaoItem
     *
     * @ORM\ManyToOne(targetEntity="LoteProducaoItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lote_confeccao_item_id", referencedColumnName="id")
     * })
     */
    private $loteConfeccaoItem;

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
    public function getLoteConfeccaoItem(): LoteProducaoItem
    {
        return $this->loteConfeccaoItem;
    }

    /**
     * @param LoteProducaoItem $loteConfeccaoItem
     * @return LoteProducaoItemQtde
     */
    public function setLoteConfeccaoItem(LoteProducaoItem $loteConfeccaoItem): LoteProducaoItemQtde
    {
        $this->loteConfeccaoItem = $loteConfeccaoItem;
        return $this;
    }


}
