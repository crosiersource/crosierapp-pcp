<?php

namespace App\Controller;

use App\Entity\Coisa;
use App\EntityHandler\CoisaEntityHandler;
use App\Form\CoisaType;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller CRUD para a entidade Coisa.
 * @package App\Controller\Coisa
 * @author Carlos Eduardo Pauluk
 */
class CoisaController extends FormListController
{

    protected $crudParams =
        [
            'typeClass' => CoisaType::class,

            'formView' => '@CrosierLibBase/form.html.twig',
            'formRoute' => 'coisa_form',
            'formPageTitle' => 'Coisa',
            'form_PROGRAM_UUID' => '',

            'listView' => 'coisaList.html.twig',
            'listRoute' => 'coisa_list',
            'listRouteAjax' => 'coisa_datatablesJsList',
            'listPageTitle' => 'Coisas',
            'listId' => 'coisaList',
            'list_PROGRAM_UUID' => '235a672b-1385-4886-a413-8127a38f4c7b',

            'normalizedAttrib' => [
                'id',
                'nome',
                'obs',
                'dtCoisa',
                'ordem',
                'importante',
            ],

        ];

    /**
     * @required
     * @param CoisaEntityHandler $entityHandler
     */
    public function setEntityHandler(CoisaEntityHandler $entityHandler): void
    {
        $this->entityHandler = $entityHandler;
    }

    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['nome', 'obs'], 'LIKE', 'str', $params)
        ];
    }

    /**
     *
     * @Route("/coisa/form/{id}", name="coisa_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Coisa|null $coisa
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function form(Request $request, Coisa $coisa = null)
    {
        return $this->doForm($request, $coisa);
    }

    /**
     *
     * @Route("/coisa/list/", name="coisa_list")
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
     * @Route("/coisa/datatablesJsList/", name="coisa_datatablesJsList")
     * @param Request $request
     * @return Response
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function datatablesJsList(Request $request): Response
    {
        return $this->doDatatablesJsList($request);
    }

    /**
     *
     * @Route("/coisa/delete/{id}/", name="coisa_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param Coisa $coisa
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Coisa $coisa): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        return $this->doDelete($request, $coisa);
    }


}