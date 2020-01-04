<?php

namespace App\Controller;


use App\Business\FichaTecnicaBusiness;
use App\Business\PropBusiness;
use App\Entity\FichaTecnica;
use App\Entity\FichaTecnicaItem;
use App\Entity\Insumo;
use App\EntityHandler\FichaTecnicaEntityHandler;
use App\EntityHandler\FichaTecnicaItemEntityHandler;
use App\Form\FichaTecnicaType;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * CRUD Controller para FichaTecnica.
 *
 * @package App\Controller
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaController extends FormListController
{

    /** @var TipoArtigoController */
    private $tipoArtigoController;

    /** @var FichaTecnicaBusiness */
    private $fichaTecnicaBusiness;

    /** @var FichaTecnicaItemEntityHandler */
    private $fichaTecnicaItemEntityHandler;

    /** @var PropBusiness */
    private $propBusiness;


    /**
     * @required
     * @param FichaTecnicaEntityHandler $entityHandler
     */
    public function setEntityHandler(FichaTecnicaEntityHandler $entityHandler): void
    {
        $this->entityHandler = $entityHandler;
    }

    /**
     * @required
     * @param TipoArtigoController $tipoArtigoController
     */
    public function setTipoArtigoController(TipoArtigoController $tipoArtigoController): void
    {
        $this->tipoArtigoController = $tipoArtigoController;
    }

    /**
     * @required
     * @param FichaTecnicaBusiness $fichaTecnicaBusiness
     */
    public function setFichaTecnicaBusiness(FichaTecnicaBusiness $fichaTecnicaBusiness): void
    {
        $this->fichaTecnicaBusiness = $fichaTecnicaBusiness;
    }

    /**
     * @required
     * @param FichaTecnicaItemEntityHandler $fichaTecnicaItemEntityHandler
     */
    public function setFichaTecnicaItemEntityHandler(FichaTecnicaItemEntityHandler $fichaTecnicaItemEntityHandler): void
    {
        $this->fichaTecnicaItemEntityHandler = $fichaTecnicaItemEntityHandler;
    }

    /**
     * @required
     * @param PropBusiness $propBusiness
     */
    public function setPropBusiness(PropBusiness $propBusiness): void
    {
        $this->propBusiness = $propBusiness;
    }



    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['descricao', 'id'], 'LIKE', 'str', $params)
        ];
    }

    /**
     *
     * @Route("/fichaTecnica/form/{id}", name="fichaTecnica_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param FichaTecnica|null $fichaTecnica
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @IsGranted("ROLE_PCP", statusCode=403)
     */
    public function form(Request $request, FichaTecnica $fichaTecnica = null)
    {
        $params = [
            'typeClass' => FichaTecnicaType::class,
            'formView' => 'fichaTecnicaForm.html.twig',
            'formRoute' => 'fichaTecnica_form',
            'formPageTitle' => 'Ficha Técnica',
            'listRoute' => 'fichaTecnica_list',
        ];
        if (!$fichaTecnica) {
            $fichaTecnica = new FichaTecnica();
            $fichaTecnica->setBloqueada(false);
            $fichaTecnica->setOculta(false);
            $fichaTecnica->setCustoOperacionalPadrao(0.35);
            $fichaTecnica->setCustoFinanceiroPadrao(0.15);
            $fichaTecnica->setMargemPadrao(0.12);
            $fichaTecnica->setPrazoPadrao(30);
        }
        return $this->doForm($request, $fichaTecnica, $params);
    }

    /**
     *
     * @Route("/fichaTecnica/delete/{fichaTecnica}/", name="fichaTecnica_delete", requirements={"fichaTecnica"="\d+"})
     * @param Request $request
     * @param FichaTecnica $fichaTecnica
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @IsGranted("ROLE_PCP", statusCode=403)
     */
    public function delete(Request $request, FichaTecnica $fichaTecnica): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        return $this->doDelete($request, $fichaTecnica);
    }

    /**
     *
     * @Route("/fichaTecnica/list/", name="fichaTecnica_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @IsGranted("ROLE_PCP", statusCode=403)
     */
    public function list(Request $request): Response
    {
        $params = [
            'formRoute' => 'fichaTecnica_form',
            'listView' => '@CrosierLibBase/list.html.twig',
            'listRoute' => 'fichaTecnica_list',
            'listRouteAjax' => 'fichaTecnica_datatablesJsList',
            'listPageTitle' => 'Fichas Técnicas',
            'listId' => 'fichaTecnicaList',
            'list_PROGRAM_UUID' => null,
            'listJS' => 'fichaTecnicaList.js',
        ];
        return $this->doList($request, $params);
    }

    /**
     *
     * @Route("/fichaTecnica/datatablesJsList/", name="fichaTecnica_datatablesJsList")
     * @param Request $request
     * @return Response
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     *
     * @IsGranted("ROLE_PCP", statusCode=403)
     */
    public function datatablesJsList(Request $request): Response
    {
        return $this->doDatatablesJsList($request);
    }


    /**
     *
     * @Route("/fichaTecnica/builder/{id}", name="fichaTecnica_builder", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param FichaTecnica $fichaTecnica
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     *
     * @IsGranted("ROLE_PCP", statusCode=403)
     */
    public function builder(Request $request, FichaTecnica $fichaTecnica = null): Response
    {
        if (!$fichaTecnica) {
            return $this->redirectToRoute('fichaTecnica_list');
        }
        // Valores para o select de instituição
        $parameters = [];
        $parameters['instituicoes'] = $this->fichaTecnicaBusiness->buildInstituicoesSelect2();
        $parameters['insumos'] = $this->buildInsumosSelect2();

        if ($fichaTecnica) {
            $parameters['instituicaoId'] = $fichaTecnica->getInstituicao()->getId();

            $parameters['tiposArtigos'] = $this->tipoArtigoController->findByInstituicao($parameters['instituicaoId'])->getContent();
            $parameters['tipoArtigo'] = $fichaTecnica->getTipoArtigo()->getId();

            $parameters['fichasTecnicas'] = $this->doFindByInstituicaoIdAndTipoArtigo($parameters['instituicaoId'], $parameters['tipoArtigo'])->getContent();
            $parameters['fichaTecnica'] = $fichaTecnica;

            $parameters['insumosArray'] = $this->fichaTecnicaBusiness->buildInsumosArray($fichaTecnica);
        }

        $parameters['formRoute'] = 'fichaTecnica_form';
        $parameters['listRoute'] = 'fichaTecnica_list';


        return $this->doRender('fichaTecnica.html.twig', $parameters);
    }

    /**
     * Valores para o select2 de Insumo.
     *
     * @return false|string
     */
    private function buildInsumosSelect2()
    {
        $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache');

        $arrInsumos = $cache->get('buildInsumosSelect2', function (ItemInterface $item) {
            $insumos = $this->getDoctrine()->getRepository(Insumo::class)->findBy([], ['descricao' => 'ASC']);

            $arrInsumos = [];
            $arrInsumos[] = ['id' => '', 'text' => '...'];
            /** @var Insumo $insumo */
            foreach ($insumos as $insumo) {
                $arrInsumos[] = ['id' => $insumo->getId(), 'text' => $insumo->getDescricao() . ' (' . number_format($insumo->getPrecoAtual()->getPrecoCusto(), 2, ',', '.') . ')'];
            }

            return $arrInsumos;
        });

        return json_encode($arrInsumos);

    }

    /**
     * @param int $instituicaoId
     * @param int $tipoArtigoId
     * @return JsonResponse
     *
     * @IsGranted("ROLE_PCP", statusCode=403)
     */
    private function doFindByInstituicaoIdAndTipoArtigo(int $instituicaoId, int $tipoArtigoId): JsonResponse
    {
        $fichasTecnicas = $this->getDoctrine()->getRepository(FichaTecnica::class)->findBy(['instituicao' => $instituicaoId, 'tipoArtigo' => $tipoArtigoId]);
        $rs = [];
        /** @var FichaTecnica $fichaTecnica */
        foreach ($fichasTecnicas as $fichaTecnica) {
            $r['id'] = $fichaTecnica->getId();
            $r['text'] = $fichaTecnica->getDescricao();
            $rs[] = $r;
        }
        return new JsonResponse($rs);
    }

    /**
     *
     * @Route("/fichaTecnica/salvarObs/{fichaTecnica}", name="fichaTecnica_salvarObs", defaults={"fichaTecnica"=null}, requirements={"fichaTecnica"="\d+"})
     * @param Request $request
     * @param FichaTecnica|null $fichaTecnica
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @IsGranted("ROLE_PCP", statusCode=403)
     */
    public function salvarObs(Request $request, FichaTecnica $fichaTecnica)
    {
        $fichaTecnica->setObs($request->get('obs'));
        $this->getEntityHandler()->save($fichaTecnica);
        return $this->redirectToRoute('fichaTecnica_builder', ['id' => $fichaTecnica->getId()]);
    }

    /**
     *
     * @Route("/fichaTecnica/findByInstituicaoIdAndTipoArtigo", name="fichaTecnica_findByInstituicaoIdAndTipoArtigo")
     * @param Request $request
     * @return JsonResponse
     *
     * @IsGranted("ROLE_PCP", statusCode=403)
     */
    public function findByInstituicaoIdAndTipoArtigo(Request $request): JsonResponse
    {
        $instituicaoId = $request->get('instituicaoId');
        $tipoArtigoId = $request->get('tipoArtigo');
        return $this->doFindByInstituicaoIdAndTipoArtigo($instituicaoId, $tipoArtigoId);
    }

    /**
     *
     * @Route("/fichaTecnicaItem/form/{fichaTecnicaItem}", name="fichaTecnicaItem_form", defaults={"fichaTecnicaItem"=null}, requirements={"fichaTecnicaItem"="\d+"})
     * @param Request $request
     * @param FichaTecnicaItem|null $fichaTecnicaItem
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @IsGranted("ROLE_PCP", statusCode=403)
     */
    public function itemForm(Request $request, FichaTecnicaItem $fichaTecnicaItem)
    {

        if ($fichaTecnicaItem) {
            $this->fichaTecnicaBusiness->buildQtdesTamanhosArray($fichaTecnicaItem->getFichaTecnica());
        }


        if ($request->get('btnSalvarItemForm')) {
            if ($request->get('ficha_tecnica_item_qtde')) {
                /** @var Insumo $insumo */
                $insumo = $this->getDoctrine()->getRepository(Insumo::class)->find($request->get('insumo'));
                $fichaTecnicaItem->setInsumo($insumo);
                $this->fichaTecnicaItemEntityHandler->handleSaveArrayQtdes($fichaTecnicaItem, $request->get('ficha_tecnica_item_qtde'));
            }
            $this->fichaTecnicaItemEntityHandler->save($fichaTecnicaItem);
            $this->addFlash('success', 'Registro salvo com sucesso!');
            return $this->redirectToRoute('fichaTecnica_builder', ['id' => $fichaTecnicaItem->getFichaTecnica()->getId()]);
        }


        $parameters = [];
        $parameters['insumos'] = $this->buildInsumosSelect2();
        $parameters['fichaTecnicaItem'] = $fichaTecnicaItem;
        $parameters['unidade'] = $this->propBusiness->findUnidadeById($fichaTecnicaItem->getInsumo()->getUnidadeProdutoId());

        return $this->doRender('fichaTecnicaItemForm.html.twig', $parameters);
    }

    /**
     *
     * @Route("/fichaTecnica/addItem/{fichaTecnica}", name="fichaTecnica_addItem", requirements={"fichaTecnica"="\d+"})
     * @param Request $request
     * @param FichaTecnica $fichaTecnica
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @IsGranted("ROLE_PCP", statusCode=403)
     */
    public function addItem(Request $request, FichaTecnica $fichaTecnica)
    {
        try {
            $insumoId = $request->get('insumo');
            if (!$insumoId) {
                $this->addFlash('info', 'Selecione um insumo para inserir');
            } else {
                /** @var Insumo $insumo */
                $insumo = $this->getDoctrine()->getRepository(Insumo::class)->find($insumoId);
                $this->fichaTecnicaBusiness->addInsumo($fichaTecnica, $insumo);
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erro ao inserir insumo');
        }
        return $this->redirectToRoute('fichaTecnica_builder', ['id' => $fichaTecnica->getId()]);
    }

    /**
     * @Route("/fichaTecnica/deleteItem/{fichaTecnicaItem}/", name="fichaTecnica_deleteItem", requirements={"fichaTecnicaItem"="\d+"})
     * @param Request $request
     * @param FichaTecnicaItem $fichaTecnicaItem
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @IsGranted("ROLE_PCP", statusCode=403)
     */
    public function deleteItem(Request $request, FichaTecnicaItem $fichaTecnicaItem): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $fichaTecnica = $fichaTecnicaItem->getFichaTecnica();
        if (!$this->isCsrfTokenValid('fichaTecnica_deleteItem', $request->request->get('token'))) {
            $this->addFlash('error', 'Erro interno do sistema.');
        } else {
            try {
                $this->fichaTecnicaItemEntityHandler->delete($fichaTecnicaItem);
                $this->addFlash('success', 'Registro deletado com sucesso.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erro ao deletar registro.');
            }
        }

        return $this->redirectToRoute('fichaTecnica_builder', ['id' => $fichaTecnica->getId(), '_fragment' => 'itens']);
    }

    /**
     * @Route("/fichaTecnica/calcularPrecos/{fichaTecnica}/", name="fichaTecnica_calcularPrecos", requirements={"fichaTecnica"="\d+"})
     * @param FichaTecnica $fichaTecnica
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws ViewException
     *
     * @IsGranted("ROLE_PCP", statusCode=403)
     */
    public function calcularPrecos(FichaTecnica $fichaTecnica): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $this->fichaTecnicaBusiness->calcularPrecos($fichaTecnica);
        return $this->redirectToRoute('fichaTecnica_builder', ['id' => $fichaTecnica->getId(), '_fragment' => 'precos']);
    }

    /**
     *
     * @Route("/fichaTecnica/clonar/{fichaTecnica}", name="fichaTecnica_clonar",requirements={"fichaTecnica"="\d+"})
     * @param Request $request
     * @param FichaTecnica $fichaTecnica
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     *
     * @IsGranted("ROLE_PCP", statusCode=403)
     */
    public function clonar(Request $request, FichaTecnica $fichaTecnica): Response
    {
        $parameters = [];


        if ($request->get('instituicao')) {
            $novaFichaTecnica = $this->fichaTecnicaBusiness->clonar($fichaTecnica, (int)$request->get('instituicao'), $request->get('descricao'));
            return $this->redirectToRoute('fichaTecnica_builder', ['id' => $novaFichaTecnica->getId()]);
        }

        $parameters['instituicoes'] = FichaTecnicaBusiness::buildInstituicoesSelect2();
        $parameters['instituicaoId'] = $fichaTecnica->getInstituicao()->getId();
        $parameters['fichaTecnicaOrigem'] = $fichaTecnica;
        $parameters['descricaoSugerida'] = $fichaTecnica->getDescricao() . ' (2)';

        return $this->doRender('fichaTecnica_clonar.html.twig', $parameters);

    }


}