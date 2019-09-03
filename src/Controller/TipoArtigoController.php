<?php

namespace App\Controller;


use App\Entity\TipoArtigo;
use App\EntityHandler\TipoArtigoEntityHandler;
use App\Form\TipoArtigoType;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * CRUD Controller para TipoArtigo.
 *
 * @package App\Controller
 * @author Carlos Eduardo Pauluk
 */
class TipoArtigoController extends FormListController
{

    /**
     * @required
     * @param TipoArtigoEntityHandler $entityHandler
     */
    public function setEntityHandler(TipoArtigoEntityHandler $entityHandler): void
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
     * @Route("/tipoArtigo/form/{id}", name="tipoArtigo_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param TipoArtigo|null $tipoArtigo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @IsGranted({"ROLE_PCP_ADMIN"}, statusCode=403)
     */
    public function form(Request $request, TipoArtigo $tipoArtigo = null)
    {
        $params = [
            'typeClass' => TipoArtigoType::class,
            'formView' => '@CrosierLibBase/form.html.twig',
            'formRoute' => 'tipoArtigo_form',
            'formPageTitle' => 'Tipo de Artigo',
        ];
        return $this->doForm($request, $tipoArtigo, $params);
    }

    /**
     *
     * @Route("/tipoArtigo/list/", name="tipoArtigo_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @IsGranted({"ROLE_PCP_ADMIN"}, statusCode=403)
     */
    public function list(Request $request): Response
    {
        $params = [
            'formRoute' => 'tipoArtigo_form',
            'listView' => '@CrosierLibBase/list.html.twig',
            'listRoute' => 'tipoArtigo_list',
            'listRouteAjax' => 'tipoArtigo_datatablesJsList',
            'listPageTitle' => 'Tipos de Artigos',
            'listId' => 'tipoArtigoList',
            'list_PROGRAM_UUID' => null,
            'listJS' => 'tipoArtigoList.js',
            'deleteRoute' => 'tipoArtigo_delete',
        ];
        return $this->doList($request, $params);
    }

    /**
     *
     * @Route("/tipoArtigo/datatablesJsList/", name="tipoArtigo_datatablesJsList")
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
     * @Route("/tipoArtigo/delete/{id}/", name="tipoArtigo_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param TipoArtigo $tipoArtigo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @IsGranted({"ROLE_PCP_ADMIN"}, statusCode=403)
     */
    public function delete(Request $request, TipoArtigo $tipoArtigo): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        return $this->doDelete($request, $tipoArtigo);
    }

    /**
     *
     * @Route("/tipoArtigo/findByInstituicaoId/{instituicaoId}", name="tipoArtigo_findByInstituicaoId", requirements={"instituicaoId"="\d+"})
     * @param int $instituicaoId
     * @return Response
     *
     * @IsGranted({"ROLE_PCP_ADMIN"}, statusCode=403)
     */
    public function findByInstituicao(int $instituicaoId): Response
    {
        $itens = $this->getDoctrine()->getRepository(TipoArtigo::class)->findByInstituicao($instituicaoId);
        $rs = [];
        /** @var TipoArtigo $tipoArtigo */
        foreach ($itens as $tipoArtigo) {
            $r['id'] = $tipoArtigo->getId();
            $r['text'] = $tipoArtigo->getDescricaoMontada();
            $rs[] = $r;
        }
        return new JsonResponse($rs);
    }


}