<?php

namespace App\Controller;


use App\Form\InstituicaoType;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use CrosierSource\CrosierLibRadxBundle\Entity\CRM\Cliente;
use CrosierSource\CrosierLibRadxBundle\EntityHandler\CRM\ClienteEntityHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Carlos Eduardo Pauluk
 */
class InstituicaoController extends FormListController
{

    /**
     * @required
     * @param ClienteEntityHandler $entityHandler
     */
    public function setEntityHandler(ClienteEntityHandler $entityHandler): void
    {
        $this->entityHandler = $entityHandler;
    }

    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['nome'], 'LIKE', 'str', $params),
            new FilterData(['cliente_pcp'], 'EQ', 'cliente_pcp', $params, null, true)
        ];
    }

    /**
     *
     * @Route("/instituicao/form/{id}", name="instituicao_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Cliente|null $cliente
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @IsGranted("ROLE_PCP_ADMIN", statusCode=403)
     */
    public function form(Request $request, Cliente $cliente = null)
    {
        $params = [
            'typeClass' => InstituicaoType::class,
            'formView' => '@CrosierLibBase/form.html.twig',
            'formRoute' => 'instituicao_form',
            'formPageTitle' => 'Instituição',
        ];

        $fnHandleRequestOnValid = function (Request $request, /** @var Cliente $cliente */ $cliente): void {
            $cliente->jsonData['cliente_pcp'] = 'S';
            $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);
            $cache->clear();
        };


        return $this->doForm($request, $cliente, $params, false, $fnHandleRequestOnValid);
    }

    /**
     *
     * @Route("/instituicao/list/", name="instituicao_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @IsGranted("ROLE_PCP_ADMIN", statusCode=403)
     */
    public function list(Request $request): Response
    {
        $params = [
            'formRoute' => 'instituicao_form',
            'listView' => '@CrosierLibBase/list.html.twig',
            'listRoute' => 'instituicao_list',
            'listRouteAjax' => 'instituicao_datatablesJsList',
            'listPageTitle' => 'Instituições',
            'listId' => 'instituicaoList',
            'list_PROGRAM_UUID' => null,
            'listJS' => 'instituicaoList.js',
        ];
        return $this->doList($request, $params);
    }

    /**
     *
     * @Route("/instituicao/datatablesJsList/", name="instituicao_datatablesJsList")
     * @param Request $request
     * @return Response
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     *
     * @IsGranted("ROLE_PCP_ADMIN", statusCode=403)
     */
    public function datatablesJsList(Request $request): Response
    {
        $defaultFilters['filter']['cliente_pcp'] = 'S';
        return $this->doDatatablesJsList($request, $defaultFilters);
    }


}
