<?php

namespace App\Controller;


use App\Entity\Insumo;
use App\EntityHandler\InsumoEntityHandler;
use App\Form\InsumoType;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
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

    protected $crudParams =
        [
            'typeClass' => InsumoType::class,

            'formView' => '@CrosierLibBase/form.html.twig',
            'formRoute' => 'insumo_form',
            'formPageTitle' => 'Insumo',
            'form_PROGRAM_UUID' => null,

            'listView' => '@CrosierLibBase/list.html.twig',
            'listRoute' => 'insumo_list',
            'listRouteAjax' => 'insumo_datatablesJsList',
            'listPageTitle' => 'Insumos',
            'listId' => 'insumoList',
            'list_PROGRAM_UUID' => null,
            'listJS' => 'insumoList.js',

            'role_access' => 'ROLE_PCP_ADMIN',
            'role_delete' => 'ROLE_PCP_ADMIN',

        ];

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
     */
    public function form(Request $request, Insumo $insumo = null)
    {
        return $this->doForm($request, $insumo);
    }

    /**
     *
     * @Route("/insumo/list/", name="insumo_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function list(Request $request): Response
    {
        return $this->doList($request);
    }

    /**
     *
     * @Route("/insumo/datatablesJsList/", name="insumo_datatablesJsList")
     * @param Request $request
     * @return Response
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
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
     */
    public function delete(Request $request, Insumo $insumo): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        return $this->doDelete($request, $insumo);
    }


}