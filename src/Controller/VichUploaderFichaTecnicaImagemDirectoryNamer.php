<?php


namespace App\Controller;


use App\Entity\FichaTecnicaImagem;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

/**
 * Class VichUploaderFichaTecnicaDirectoryNamer
 * @package App\Utils\Estoque
 * @author Carlos Eduardo Pauluk
 */
class VichUploaderFichaTecnicaImagemDirectoryNamer implements DirectoryNamerInterface
{

    /**
     *
     *
     * @param $fichaTecnicaImagem
     * @param PropertyMapping $mapping The mapping to use to manipulate the given object
     *
     * @return string The directory name
     */
    public function directoryName(/** @var FichaTecnicaImagem $fichaTecnicaImagem */ $fichaTecnicaImagem, PropertyMapping $mapping): string
    {
        return $fichaTecnicaImagem->getFichaTecnica()->getId();
    }
}