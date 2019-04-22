<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * LoteProducaoItem
 *
 * @ORM\Table(name="prod_lote_confeccao_item")
 * @ORM\Entity
 *
 * @author Carlos Eduardo Pauluk
 */
class LoteProducaoItem implements EntityId
{

    use EntityIdTrait;


    /**
     * @var string
     *
     * @ORM\Column(name="obs", type="string", length=5000, nullable=false)
     */
    private $obs;

    /**
     * @var int
     *
     * @ORM\Column(name="ordem", type="integer", nullable=false)
     */
    private $ordem;

    /**
     * @var FichaTecnica
     *
     * @ORM\ManyToOne(targetEntity="FichaTecnica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="confeccao_id", referencedColumnName="id")
     * })
     */
    private $confeccao;

    /**
     * @var LoteProducao
     *
     * @ORM\ManyToOne(targetEntity="LoteProducao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lote_confeccao_id", referencedColumnName="id")
     * })
     */
    private $loteConfeccao;

    /**
     * @return string
     */
    public function getObs(): string
    {
        return $this->obs;
    }

    /**
     * @param string $obs
     * @return LoteProducaoItem
     */
    public function setObs(string $obs): LoteProducaoItem
    {
        $this->obs = $obs;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrdem(): int
    {
        return $this->ordem;
    }

    /**
     * @param int $ordem
     * @return LoteProducaoItem
     */
    public function setOrdem(int $ordem): LoteProducaoItem
    {
        $this->ordem = $ordem;
        return $this;
    }

    /**
     * @return FichaTecnica
     */
    public function getConfeccao(): FichaTecnica
    {
        return $this->confeccao;
    }

    /**
     * @param FichaTecnica $confeccao
     * @return LoteProducaoItem
     */
    public function setConfeccao(FichaTecnica $confeccao): LoteProducaoItem
    {
        $this->confeccao = $confeccao;
        return $this;
    }

    /**
     * @return LoteProducao
     */
    public function getLoteConfeccao(): LoteProducao
    {
        return $this->loteConfeccao;
    }

    /**
     * @param LoteProducao $loteConfeccao
     * @return LoteProducaoItem
     */
    public function setLoteConfeccao(LoteProducao $loteConfeccao): LoteProducaoItem
    {
        $this->loteConfeccao = $loteConfeccao;
        return $this;
    }


}
