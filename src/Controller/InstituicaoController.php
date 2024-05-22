<?php

namespace App\Controller;


use App\Form\clienteType;
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
class ClienteController extends FormListController
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
        ];
    }

    /**
     *
     * @Route("/cliente/form/{id}", name="cliente_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Cliente|null $cliente
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     *
     * @IsGranted("ROLE_PCP_ADMIN", statusCode=403)
     */
    public function form(Request $request, Cliente $cliente = null)
    {
        $params = [
            'typeClass' => ClienteType::class,
            'formView' => '@CrosierLibBase/form.html.twig',
            'formRoute' => 'cliente_form',
            'formPageTitle' => 'Cliente',
        ];

        $fnHandleRequestOnValid = function (Request $request, /** @var Cliente $cliente */ $cliente): void {
            $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);
            $cache->clear();
        };


        return $this->doForm($request, $cliente, $params, false, $fnHandleRequestOnValid);
    }

    /**
     *
     * @Route("/cliente/list/", name="cliente_list")
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @IsGranted("ROLE_PCP_ADMIN", statusCode=403)
     */
    public function list(Request $request): Response
    {
        $params = [
            'formRoute' => 'cliente_form',
            'listView' => '@CrosierLibBase/list.html.twig',
            'listRoute' => 'cliente_list',
            'listRouteAjax' => 'cliente_datatablesJsList',
            'listPageTitle' => 'Clientes',
            'listId' => 'clienteList',
            'list_PROGRAM_UUID' => null,
            'listJS' => 'clienteList.js',
        ];
        return $this->doList($request, $params);
    }

    /**
     *
     * @Route("/cliente/datatablesJsList/", name="cliente_datatablesJsList")
     * @param Request $request
     * @return Response
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     *
     * @IsGranted("ROLE_PCP_ADMIN", statusCode=403)
     */
    public function datatablesJsList(Request $request): Response
    {
        return $this->doDatatablesJsList($request, []);
    }


}
