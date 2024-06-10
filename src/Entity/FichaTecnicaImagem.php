<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"fichaTecnicaImagem","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"fichaTecnicaImagem"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/est/fichaTecnicaImagem/{id}", "security"="is_granted('ROLE_ESTOQUE')"},
 *          "put"={"path"="/est/fichaTecnicaImagem/{id}", "security"="is_granted('ROLE_ESTOQUE')"},
 *          "delete"={"path"="/est/fichaTecnicaImagem/{id}", "security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *          "get"={"path"="/est/fichaTecnicaImagem", "security"="is_granted('ROLE_ESTOQUE')"},
 *          "post"={"path"="/est/fichaTecnicaImagem", "security"="is_granted('ROLE_ESTOQUE')"}
 *     },
 *
 *     attributes={
 *          "pagination_items_per_page"=10,
 *          "formats"={"jsonld", "csv"={"text/csv"}}
 *     }
 * )
 * @ApiFilter(PropertyFilter::class)
 *
 * @ApiFilter(SearchFilter::class, properties={
 *     "nome": "partial",
 *     "documento": "exact",
 *     "id": "exact",
 *     "fichaTecnica.id": "exact",
 *     "fichaTecnica.codigo": "exact"
 * })
 * @ApiFilter(OrderFilter::class, properties={"id", "documento", "nome", "updated"}, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="CrosierSource\CrosierLibRadxBundle\EntityHandler\Estoque\FichaTecnicaImagemEntityHandler")
 *
 * @ORM\Entity(repositoryClass="App\Repository\FichaTecnicaImagemRepository")
 * @ORM\Table(name="prod_fichatecnica_imagem")
 * @Vich\Uploadable
 *
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaImagem implements EntityId
{

    use EntityIdTrait;

    /**
     *
     * @ORM\ManyToOne(targetEntity="FichaTecnica")
     * @ORM\JoinColumn(name="fichaTecnica_id", nullable=false)
     *
     * @var null|FichaTecnica
     */
    public ?FichaTecnica $fichaTecnica = null;

    /**
     * @Vich\UploadableField(mapping="fichaTecnica_imagem", fileNameProperty="imageName")
     * @var null|File
     */
    public ?File $imageFile = null;

    /**
     * @ORM\Column(name="image_name", type="string")
     * @Groups("fichaTecnicaImagem")
     * @NotUppercase()
     * @var null|string
     */
    public ?string $imageName = null;

    /**
     *
     * @ORM\Column(name="ordem", type="integer", nullable=true)
     * @Groups("fichaTecnicaImagem")
     * @var null|integer
     */
    public ?int $ordem = null;

    /**
     *
     * @ORM\Column(name="descricao", type="string", nullable=false)
     * @NotUppercase()
     * @Groups("fichaTecnicaImagem")
     * @var null|string
     */
    public ?string $descricao = null;

    /**
     * @return FichaTecnica|null
     */
    public function getFichaTecnica(): ?FichaTecnica
    {
        return $this->fichaTecnica;
    }

    /**
     * @param FichaTecnica|null $fichaTecnica
     * @return FichaTecnicaImagem
     */
    public function setFichaTecnica(?FichaTecnica $fichaTecnica): FichaTecnicaImagem
    {
        $this->fichaTecnica = $fichaTecnica;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile|null $imageFile
     * @return FichaTecnicaImagem
     * @throws Exception
     */
    public function setImageFile(?File $imageFile = null): FichaTecnicaImagem
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated = new DateTime();
        }
        return $this;
    }

    /**
     * @return null|string
     */
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    /**
     * @param null|string $imageName
     * @return FichaTecnicaImagem
     */
    public function setImageName(?string $imageName): FichaTecnicaImagem
    {
        $this->imageName = $imageName;
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
     * @return FichaTecnicaImagem
     */
    public function setOrdem(?int $ordem): FichaTecnicaImagem
    {
        $this->ordem = $ordem;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * @param string|null $descricao
     * @return FichaTecnicaImagem
     */
    public function setDescricao(?string $descricao): FichaTecnicaImagem
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * @Groups("fichaTecnicaImagem")
     */
    public function getUrl(): ?string
    {
        try {
            return ($_SERVER['CROSIERAPPRADX_URL'] ?? 'radx_url_not_found') .
                '/images/fichaTecnicas/' .
                $this->fichaTecnica->depto->getId() . '/' .
                $this->fichaTecnica->grupo->getId() . '/' .
                $this->fichaTecnica->subgrupo->getId() . '/' .
                $this->imageName;
        } catch (\Exception $e) {
            return null;
        }
    }


}
