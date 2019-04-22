<?php

namespace App\Controller;


use App\Entity\TipoInsumo;
use App\EntityHandler\TipoInsumoEntityHandler;
use App\Form\TipoInsumoType;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * CRUD Controller para TipoInsumo.
 *
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class TipoInsumoController extends FormListController
{

    protected $crudParams =
        [
            'typeClass' => TipoInsumoType::class,

            'formView' => '@CrosierLibBase/form.html.twig',
            'formRoute' => 'tipoInsumo_form',
            'formPageTitle' => 'TipoInsumo',
            'form_PROGRAM_UUID' => null,

            'listView' => '@CrosierLibBase/list.html.twig',
            'listRoute' => 'tipoInsumo_list',
            'listRouteAjax' => 'tipoInsumo_datatablesJsList',
            'listPageTitle' => 'TipoInsumos',
            'listId' => 'tipoInsumoList',
            'list_PROGRAM_UUID' => null,
            'listJS' => 'tipoInsumoList.js',

        ];

    /**
     * @required
     * @param TipoInsumoEntityHandler $entityHandler
     */
    public function setEntityHandler(TipoInsumoEntityHandler $entityHandler): void
    {
        $this->entityHandler = $entityHandler;
    }

    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['descricao'], 'LIKE', 'descricao', $params)
        ];
    }

    /**
     *
     * @Route("/tipoInsumo/form/{id}", name="tipoInsumo_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param TipoInsumo|null $tipoInsumo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function form(Request $request, TipoInsumo $tipoInsumo = null)
    {
        return $this->doForm($request, $tipoInsumo);
    }

    /**
     *
     * @Route("/tipoInsumo/list/", name="tipoInsumo_list")
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
     * @Route("/tipoInsumo/datatablesJsList/", name="tipoInsumo_datatablesJsList")
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
     * @Route("/tipoInsumo/delete/{id}/", name="tipoInsumo_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param TipoInsumo $tipoInsumo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, TipoInsumo $tipoInsumo): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        return $this->doDelete($request, $tipoInsumo);
    }


}