<?php

namespace App\EntityHandler;

use App\Entity\FichaTecnica;
use App\Entity\FichaTecnicaImagem;
use App\Repository\FichaTecnicaImagemRepository;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;

/**
 *
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaImagemEntityHandler extends EntityHandler
{

    public function getEntityClass(): string
    {
        return FichaTecnicaImagem::class;
    }

    public function beforeSave(/** @var FichaTecnicaImagem $fichaTecnicaImagem */ $fichaTecnicaImagem)
    {
        if (!$fichaTecnicaImagem->getOrdem()) {
            /** @var FichaTecnicaImagem $ultima */
            $o = $this->getDoctrine()->getConnection()->fetchAssociative(
                'SELECT max(ordem) + 1 as ordem FROM prod_fichatecnica_imagem WHERE fichatecnica_id = :fichaTecnicaId',
                [
                    'fichaTecnicaId' => $fichaTecnicaImagem->getFichaTecnica()->getId()
                ]
            );
            $fichaTecnicaImagem->setOrdem($o['ordem'] ?? 1);
        }
    }

    /**
     * @param array $ids
     * @return array
     * @throws ViewException
     */
    public function salvarOrdens(array $ids): array
    {
        /** @var FichaTecnicaImagemRepository $repoImagem */
        $repoFichaTecnicaImagem = $this->getDoctrine()->getRepository(FichaTecnicaImagem::class);
        $i = 1;
        $ordens = [];
        $imagens = $repoFichaTecnicaImagem->find($ids[0])->getFichaTecnica()->getImagens();
        /** @var FichaTecnicaImagem $imagem */
        $varia = random_int(1, 1000000);
        foreach ($imagens as $imagem) {
            $imagem->setOrdem($imagem->getOrdem() + $varia + 1);
            $this->save($imagem);
        }
        foreach ($ids as $id) {
            if (!$id) continue;
            /** @var FichaTecnicaImagem $fichaTecnicaImagem */
            $fichaTecnicaImagem = $repoFichaTecnicaImagem->find($id);
            $ordens[$id] = $i;
            $fichaTecnicaImagem->setOrdem($i++);
            $this->save($fichaTecnicaImagem);
        }
        return $ordens;
    }

    /**
     * @param FichaTecnica $fichaTecnica
     * @throws ViewException
     */
    public function reordenar(FichaTecnica $fichaTecnica)
    {
        $i = 1;
        foreach ($fichaTecnica->getImagens() as $fichaTecnicaImagem) {
            $fichaTecnicaImagem->setOrdem($i++);
            $this->save($fichaTecnicaImagem);
        }
    }

}