<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\CoisaRepository")
 * @ORM\Table(name="hw_coisa")
 */
class Coisa implements EntityId
{

    use EntityIdTrait;

    /**
     * @var string|null
     * @ORM\Column(name="nome", type="string", nullable=false, length=200)
     */
    private $nome;

    /**
     * @var string|null
     * @ORM\Column(name="obs", type="string", nullable=true, length=3000)
     * @NotUppercase()
     */
    private $obs;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="dt_coisa", type="datetime", nullable=false)
     */
    private $dtCoisa;

    /**
     * @var int|null
     * @ORM\Column(name="ordem", type="integer", nullable=false)
     */
    private $ordem;

    /**
     * @var bool|null
     * @ORM\Column(name="importante", type="boolean", nullable=false)
     */
    private $importante = false;

    /**
     * @return null|string
     */
    public function getNome(): ?string
    {
        return $this->nome;
    }

    /**
     * @param null|string $nome
     */
    public function setNome(?string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * @return null|string
     */
    public function getObs(): ?string
    {
        return $this->obs;
    }

    /**
     * @param null|string $obs
     */
    public function setObs(?string $obs): void
    {
        $this->obs = $obs;
    }

    /**
     * @return \DateTime|null
     */
    public function getDtCoisa(): ?\DateTime
    {
        return $this->dtCoisa;
    }

    /**
     * @param \DateTime|null $dtCoisa
     */
    public function setDtCoisa(?\DateTime $dtCoisa): void
    {
        $this->dtCoisa = $dtCoisa;
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
     */
    public function setOrdem(?int $ordem): void
    {
        $this->ordem = $ordem;
    }

    /**
     * @return bool|null
     */
    public function getImportante(): ?bool
    {
        return $this->importante;
    }

    /**
     * @param bool|null $importante
     */
    public function setImportante(?bool $importante): void
    {
        $this->importante = $importante;
    }


}
    