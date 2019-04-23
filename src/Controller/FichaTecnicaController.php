<?php

namespace App\Controller;


use App\Entity\FichaTecnica;
use App\EntityHandler\FichaTecnicaEntityHandler;
use App\Form\FichaTecnicaType;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * CRUD Controller para FichaTecnica.
 *
 * @package App\Controller
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaController extends FormListController
{

    protected $crudParams =
        [
            'typeClass' => FichaTecnicaType::class,

            'formView' => '@CrosierLibBase/form.html.twig',
            'formRoute' => 'fichaTecnica_form',
            'formPageTitle' => 'Ficha Técnica',
            'form_PROGRAM_UUID' => null,

            'listView' => '@CrosierLibBase/list.html.twig',
            'listRoute' => 'fichaTecnica_list',
            'listRouteAjax' => 'fichaTecnica_datatablesJsList',
            'listPageTitle' => 'Fichas Técnicas',
            'listId' => 'fichaTecnicaList',
            'list_PROGRAM_UUID' => null,
            'listJS' => 'fichaTecnicaList.js',

        ];

    /**
     * @required
     * @param FichaTecnicaEntityHandler $entityHandler
     */
    public function setEntityHandler(FichaTecnicaEntityHandler $entityHandler): void
    {
        $this->entityHandler = $entityHandler;
    }

    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['descricao'], 'LIKE', 'str', $params)
        ];
    }

    /**
     *
     * @Route("/fichaTecnica/form/{id}", name="fichaTecnica_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param FichaTecnica|null $fichaTecnica
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function form(Request $request, FichaTecnica $fichaTecnica = null)
    {
        return $this->doForm($request, $fichaTecnica);
    }

    /**
     *
     * @Route("/fichaTecnica/list/", name="fichaTecnica_list")
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
     * @Route("/fichaTecnica/datatablesJsList/", name="fichaTecnica_datatablesJsList")
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
     * @Route("/fichaTecnica/delete/{id}/", name="fichaTecnica_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param FichaTecnica $fichaTecnica
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, FichaTecnica $fichaTecnica): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        return $this->doDelete($request, $fichaTecnica);
    }


}