<?php

namespace App\Controller;


use App\Entity\Insumo;
use App\Entity\InsumoPreco;
use App\EntityHandler\InsumoEntityHandler;
use App\Form\InsumoType;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * CRUD Controller para Insumo.
 *
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class InsumoController extends FormListController
{

    /**
     * @required
     * @param InsumoEntityHandler $entityHandler
     */
    public function setEntityHandler(InsumoEntityHandler $entityHandler): void
    {
        $this->entityHandler = $entityHandler;
    }

    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['descricao', 'ti.descricao'], 'LIKE', 'str', $params)
        ];
    }

    /**
     *
     * @Route("/insumo/form/{id}", name="insumo_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Insumo|null $insumo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @IsGranted({"ROLE_PCP_ADMIN"}, statusCode=403)
     */
    public function form(Request $request, Insumo $insumo = null)
    {
        $params = [
            'typeClass' => InsumoType::class,
            'formView' => 'insumoForm.html.twig',
            'formRoute' => 'insumo_form',
            'formPageTitle' => 'Insumo',
        ];
        return $this->doForm($request, $insumo, $params);
    }

    /**
     *
     * @Route("/insumo/list/", name="insumo_list")
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @IsGranted({"ROLE_PCP_ADMIN"}, statusCode=403)
     */
    public function list(Request $request): Response
    {
        $params = [
            'formRoute' => 'insumo_form',
            'listView' => '@CrosierLibBase/list.html.twig',
            'listRoute' => 'insumo_list',
            'listRouteAjax' => 'insumo_datatablesJsList',
            'listPageTitle' => 'Insumos',
            'listId' => 'insumoList',
            'list_PROGRAM_UUID' => null,
            'listJS' => 'insumoList.js',
            'deleteRoute' => 'insumo_delete',
        ];
        return $this->doList($request, $params);
    }

    /**
     *
     * @Route("/insumo/datatablesJsList/", name="insumo_datatablesJsList")
     * @param Request $request
     * @return Response
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     *
     * @IsGranted({"ROLE_PCP_ADMIN"}, statusCode=403)
     */
    public function datatablesJsList(Request $request): Response
    {
        return $this->doDatatablesJsList($request);
    }

    /**
     *
     * @Route("/insumo/delete/{id}/", name="insumo_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param Insumo $insumo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @IsGranted({"ROLE_PCP_ADMIN"}, statusCode=403)
     */
    public function delete(Request $request, Insumo $insumo): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        return $this->doDelete($request, $insumo);
    }

    /**
     *
     * @Route("/insumo/precos/ajustarAtual", name="insumo_precos_ajustarAtual")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @IsGranted({"ROLE_PCP_ADMIN"}, statusCode=403)
     */
    public function ajustarAtual(): Response
    {
        $insumos = $this->getDoctrine()->getRepository(Insumo::class)->findAll();

        /** @var Insumo $insumo */
        foreach ($insumos as $insumo) {
            $precos = $insumo->getPrecos()->toArray();

            uasort($precos, function ($a, $b) {
                /** @var InsumoPreco $a */
                /** @var InsumoPreco $b */
                return $a->getDtCusto() > $b->getDtCusto();
            });

            /** @var InsumoPreco $precoAtual */
            $precoAtual = $this->getDoctrine()->getRepository(InsumoPreco::class)->find($precos[0]->getId());
            $precoAtual->setAtual(true);
            $this->getDoctrine()->getManager()->persist($precoAtual);

        }
        $this->getDoctrine()->getManager()->flush();

    }


}