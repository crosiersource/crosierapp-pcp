<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * LoteProducaoItem
 *
 * @ORM\Table(name="prod_lote_producao_item")
 * @ORM\Entity
 *
 * @author Carlos Eduardo Pauluk
 */
class LoteProducaoItem implements EntityId
{

    use EntityIdTrait;


    /**
     * @var null|string
     *
     * @ORM\Column(name="obs", type="string", length=5000, nullable=false)
     * @Groups("entity")
     */
    private $obs;

    /**
     * @var null|int
     *
     * @ORM\Column(name="ordem", type="integer", nullable=false)
     * @Groups("entity")
     */
    private $ordem;

    /**
     * @var null|FichaTecnica
     *
     * @ORM\ManyToOne(targetEntity="FichaTecnica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="confeccao_id", referencedColumnName="id")
     * })
     * @Groups("entity")
     */
    private $fichaTecnica;

    /**
     * @var null|LoteProducao
     *
     * @ORM\ManyToOne(targetEntity="LoteProducao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lote_producao_id", referencedColumnName="id")
     * })
     * @Groups("entity")
     */
    private $loteConfeccao;

    /**
     * @return null|string
     */
    public function getObs(): ?string
    {
        return $this->obs;
    }

    /**
     * @param null|string $obs
     * @return LoteProducaoItem
     */
    public function setObs(?string $obs): LoteProducaoItem
    {
        $this->obs = $obs;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getOrdem(): ?int
    {
        return $this->ordem;
    }

    /**
     * @param int|null $ordem
     * @return LoteProducaoItem
     */
    public function setOrdem(?int $ordem): LoteProducaoItem
    {
        $this->ordem = $ordem;
        return $this;
    }

    /**
     * @return FichaTecnica|null
     */
    public function getFichaTecnica(): ?FichaTecnica
    {
        return $this->fichaTecnica;
    }

    /**
     * @param FichaTecnica|null $fichaTecnica
     * @return LoteProducaoItem
     */
    public function setFichaTecnica(?FichaTecnica $fichaTecnica): LoteProducaoItem
    {
        $this->fichaTecnica = $fichaTecnica;
        return $this;
    }

    /**
     * @return LoteProducao|null
     */
    public function getLoteConfeccao(): ?LoteProducao
    {
        return $this->loteConfeccao;
    }

    /**
     * @param LoteProducao|null $loteConfeccao
     * @return LoteProducaoItem
     */
    public function setLoteConfeccao(?LoteProducao $loteConfeccao): LoteProducaoItem
    {
        $this->loteConfeccao = $loteConfeccao;
        return $this;
    }


}
