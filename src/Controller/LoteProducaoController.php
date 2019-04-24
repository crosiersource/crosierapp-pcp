<?php

namespace App\Controller;


use App\Entity\LoteProducao;
use App\EntityHandler\LoteProducaoEntityHandler;
use App\Form\LoteProducaoType;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * CRUD Controller para LoteProducao.
 *
 * @package App\Controller
 * @author Carlos Eduardo Pauluk
 */
class LoteProducaoController extends FormListController
{

    protected $crudParams =
        [
            'typeClass' => LoteProducaoType::class,

            'formView' => '@CrosierLibBase/form.html.twig',
            'formRoute' => 'loteProducao_form',
            'formPageTitle' => 'Lote de Produção',
            'form_PROGRAM_UUID' => null,

            'listView' => '@CrosierLibBase/list.html.twig',
            'listRoute' => 'loteProducao_list',
            'listRouteAjax' => 'loteProducao_datatablesJsList',
            'listPageTitle' => 'Lotes de Produção',
            'listId' => 'loteProducaoList',
            'list_PROGRAM_UUID' => null,
            'listJS' => 'loteProducaoList.js',

        ];

    /**
     * @required
     * @param LoteProducaoEntityHandler $entityHandler
     */
    public function setEntityHandler(LoteProducaoEntityHandler $entityHandler): void
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
     * @Route("/loteProducao/form/{id}", name="loteProducao_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param LoteProducao|null $loteProducao
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function form(Request $request, LoteProducao $loteProducao = null)
    {
        return $this->doForm($request, $loteProducao);
    }

    /**
     *
     * @Route("/loteProducao/list/", name="loteProducao_list")
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
     * @Route("/loteProducao/datatablesJsList/", name="loteProducao_datatablesJsList")
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
     * @Route("/loteProducao/delete/{id}/", name="loteProducao_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param LoteProducao $loteProducao
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, LoteProducao $loteProducao): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        return $this->doDelete($request, $loteProducao);
    }


}