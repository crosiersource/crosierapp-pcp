<?php

namespace App\Business;


use CrosierSource\CrosierLibBaseBundle\Entity\Base\Prop;
use CrosierSource\CrosierLibBaseBundle\Repository\Base\PropRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

/**
 *
 * @author Carlos Eduardo Pauluk
 */
class PropBusiness
{

    /** @var ManagerRegistry */
    private $doctrine;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return array
     */
    public function findGrades(): array
    {

        $cache = new FilesystemAdapter();

        $rGrades = $cache->get('grades', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            /** @var Prop $prop */
            $prop = $this->findByNome('GRADES');
            $grades = json_decode($prop->getValores(), true);

            $rGrades = [];

            foreach ($grades as $grade) {
                $gradeId = $grade['gradeId'];
                $tamanhosArr = [];
                $tamanhos = $this->findTamanhosByGradeId($gradeId);
                foreach ($tamanhos as $tamanho) {
                    $tamanhosArr[] = $tamanho['tamanho'];
                }
                $tamanhosStr = str_pad($gradeId, 3, '0', STR_PAD_LEFT) . ' (' . implode('-', $tamanhosArr) . ')';
                $rGrades[$gradeId] = $tamanhosStr;
            }

            return $rGrades;
        });


        return $rGrades;
    }

    /**
     * @param string $nome
     * @return Prop
     */
    public function findByNome(string $nome): Prop
    {
        /** @var PropRepository $repoProp */
        $repoProp = $this->doctrine->getRepository(Prop::class);
        /** @var Prop $prop */
        $prop = $repoProp->findOneBy(['nome' => $nome]);
        return $prop;
    }

    /**
     * @param int $gradeId
     * @return array|null
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function findTamanhosByGradeId(int $gradeId): ?array
    {
        $cache = new FilesystemAdapter();

        $grades = $cache->get('findTamanhosByGradeId_' . $gradeId, function (ItemInterface $item) use ($gradeId) {
            $item->expiresAfter(3600);

            /** @var Prop $prop */
            $prop = $this->findByNome('GRADES');
            $grades = json_decode($prop->getValores(), true);

            foreach ($grades as $grade) {
                if ($grade['gradeId'] === $gradeId) {
                    return $grade['tamanhos'];
                }
            }
            return $grades;
        });
        return $grades;
    }

    /**
     * @param int $gradeId
     * @return array|null
     */
    public function findGradeTamanhoById(int $id): ?array
    {
        $cache = new FilesystemAdapter();

        $tamanho = $cache->get('findGradeTamanhoById_' . $id, function (ItemInterface $item) use ($id) {
            $item->expiresAfter(3600);

            /** @var Prop $prop */
            $prop = $this->findByNome('GRADES');
            $grades = json_decode($prop->getValores(), true);

            foreach ($grades as $grade) {
                foreach ($grade['tamanhos'] as $tamanho) {
                    if ($tamanho['id'] === $id) {
                        return $tamanho;
                    }
                }
            }

            return null;
        });
        return $tamanho;
    }


    /**
     * @param int $gradeId
     * @param int $posicao
     * @return array|null
     */
    public function findTamanhoByGradeIdAndPosicao(int $gradeId, int $posicao): ?array
    {

        $cache = new FilesystemAdapter();

        $tamanho = $cache->get('findTamanhoByGradeIdAndPosicao_' . $gradeId . '-' . $posicao, function (ItemInterface $item) use ($gradeId, $posicao) {
            $item->expiresAfter(3600);

            $tamanhos = $this->findTamanhosByGradeId($gradeId);
            foreach ($tamanhos as $tamanho) {
                if ($tamanho['posicao'] === $posicao) {
                    return $tamanho;
                }
            }

            return null;
        });

        return $tamanho;
    }


    /**
     *
     * @param int $gradeId
     * @return array
     */
    public function buildGradesTamanhosByPosicaoArray(int $gradeId): array
    {
        $cache = new FilesystemAdapter();

        $gradesTamanhosByPosicaoArray = $cache->get('buildGradesTamanhosByPosicaoArray_' . $gradeId, function (ItemInterface $item) use ($gradeId) {
            $item->expiresAfter(3600);

            $tamanhos = $this->findTamanhosByGradeId($gradeId);
            $gradesTamanhosByPosicaoArray = [];

            for ($i = 1; $i <= 15; $i++) {
                foreach ($tamanhos as $tamanho) {
                    $gradesTamanhosByPosicaoArray[$i] = '-';
                    if ($i === $tamanho['posicao']) {
                        $gradesTamanhosByPosicaoArray[$tamanho['posicao']] = $tamanho['tamanho'];
                        break;
                    }
                }
            }
            return $gradesTamanhosByPosicaoArray;
        });

        return $gradesTamanhosByPosicaoArray;

    }

    /**
     *
     * @param int $gradeTamanhoId
     * @return int
     */
    public function findPosicaoByGradeTamanhoId(int $gradeTamanhoId): int
    {

        $cache = new FilesystemAdapter();

        $posicao = $cache->get('findPosicaoByGradeTamanhoId' . $gradeTamanhoId, function (ItemInterface $item) use ($gradeTamanhoId) {
            $item->expiresAfter(3600);

            /** @var Prop $prop */
            $prop = $this->findByNome('GRADES');
            $grades = json_decode($prop->getValores(), true);

            foreach ($grades as $grade) {
                $gradeId = $grade['gradeId'];
                $tamanhos = $this->findTamanhosByGradeId($gradeId);
                foreach ($tamanhos as $tamanho) {
                    if ($tamanho['id'] === $gradeTamanhoId) {
                        return $tamanho['posicao'];
                    }
                }
            }

            return -1;
        });
        return $posicao;
    }


    /**
     * @return array
     */
    public function findUnidades(): array
    {

        $cache = new FilesystemAdapter();

        $rUnidades = $cache->get('unidades', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            /** @var Prop $prop */
            $prop = $this->findByNome('UNIDADES');
            return json_decode($prop->getValores(), true);
        });


        return $rUnidades;
    }


    /**
     * Encontra uma unidade por seu id no json UNIDADES.
     *
     * @param int $unidadeId
     * @return array|null
     */
    public function findUnidadeById(int $unidadeId): ?array
    {
        $cache = new FilesystemAdapter();

        $unidade = $cache->get('findUnidadeById' . $unidadeId, function (ItemInterface $item) use ($unidadeId) {
            $item->expiresAfter(3600);

            /** @var Prop $prop */
            $prop = $this->findByNome('UNIDADES');
            $unidades = json_decode($prop->getValores(), true);

            foreach ($unidades as $unidade) {
                if ($unidadeId === $unidade['id']) {
                    return $unidade;
                }

            }

            return null;
        });
        return $unidade;
    }


}