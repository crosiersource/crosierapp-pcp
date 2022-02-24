<?php

namespace App\Controller;


use App\Business\FichaTecnicaBusiness;
use App\Entity\FichaTecnica;
use App\Entity\FichaTecnicaItem;
use App\Entity\Insumo;
use App\EntityHandler\FichaTecnicaEntityHandler;
use App\EntityHandler\FichaTecnicaItemEntityHandler;
use App\Form\FichaTecnicaType;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use CrosierSource\CrosierLibRadxBundle\Entity\CRM\Cliente;
use CrosierSource\CrosierLibRadxBundle\Entity\Estoque\Unidade;
use CrosierSource\CrosierLibRadxBundle\EntityHandler\CRM\ClienteEntityHandler;
use CrosierSource\CrosierLibRadxBundle\Repository\Estoque\UnidadeRepository;
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

    private TipoArtigoController $tipoArtigoController;

    private FichaTecnicaBusiness $fichaTecnicaBusiness;

    private FichaTecnicaItemEntityHandler $fichaTecnicaItemEntityHandler;


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


    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['descricao', 'id', 'instituicao.nome'], 'LIKE', 'str', $params),
        ];
    }

    /**
     *
     * @Route("/fichaTecnica/form/{id}", name="fichaTecnica_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param FichaTecnica|null $fichaTecnica
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
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
            $fichaTecnica->bloqueada = (false);
            $fichaTecnica->oculta = (false);
            $fichaTecnica->custoOperacionalPadrao = (0.35);
            $fichaTecnica->custoFinanceiroPadrao = (0.15);
            $fichaTecnica->margemPadrao = (0.12);
            $fichaTecnica->prazoPadrao = (30);
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
        return $this->doDelete($request, $fichaTecnica, []);
    }

    /**
     *
     * @Route("/fichaTecnica/list/", name="fichaTecnica_list")
     * @param Request $request
     * @return Response
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
     * @throws ViewException
     *
     * @IsGranted("ROLE_PCP", statusCode=403)
     */
    public function datatablesJsList(Request $request): Response
    {
        return $this->doDatatablesJsList($request,
            null, null, null, 
            ['outrosGruposSerializ' => ['cliente', 'tipoArtigo']]);
    }


    /**
     *
     * @Route("/fichaTecnica/builder/{id}", name="fichaTecnica_builder", defaults={"id"=null}, requirements={"id"="\d+"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Psr\Cache\InvalidArgumentException
     * @IsGranted("ROLE_PCP", statusCode=403)
     */
    public function builder(FichaTecnica $fichaTecnica = null): Response
    {
        if (!$fichaTecnica) {
            return $this->redirectToRoute('fichaTecnica_list');
        }
        // Valores para o select de instituição
        $parameters = [];
        $parameters['instituicoes'] = $this->fichaTecnicaBusiness->buildInstituicoesSelect2();
        $parameters['insumos'] = $this->buildInsumosSelect2();

        $parameters['instituicaoId'] = $fichaTecnica->instituicao->getId();

        $parameters['tiposArtigos'] = $this->tipoArtigoController->findByInstituicao($parameters['instituicaoId'])->getContent();
        $parameters['tipoArtigo'] = $fichaTecnica->tipoArtigo->getId();

        $parameters['fichasTecnicas'] = $this->doFindByInstituicaoIdAndTipoArtigo($parameters['instituicaoId'], $parameters['tipoArtigo'])->getContent();
        $parameters['fichaTecnica'] = $fichaTecnica;

        $parameters['insumosArray'] = $this->fichaTecnicaBusiness->buildInsumosArray($fichaTecnica);

        $parameters['formRoute'] = 'fichaTecnica_form';
        $parameters['listRoute'] = 'fichaTecnica_list';


        return $this->doRender('fichaTecnica.html.twig', $parameters);
    }

    /**
     * Valores para o select2 de Insumo.
     *
     * @return false|string
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function buildInsumosSelect2()
    {
        $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);

        $arrInsumos = $cache->get('buildInsumosSelect2', function (ItemInterface $item) {
            $fd = new FilterData('visivel', 'EQ', 'visivel', ['filter' => ['visivel' => true]]);
            $insumos = $this->getDoctrine()->getRepository(Insumo::class)
                ->findByFiltersSimpl([$fd], ['descricao' => 'ASC'], 0, 9999999);

            $arrInsumos = [];
            $arrInsumos[] = ['id' => '', 'text' => '...'];
            /** @var Insumo $insumo */
            foreach ($insumos as $insumo) {
                $arrInsumos[] = ['id' => $insumo->getId(), 'text' => $insumo->descricao . ' - ' . $insumo->marca . ' (' . number_format($insumo->getPrecoAtual()->precoCusto, 2, ',', '.') . ')'];
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
            $r['text'] = $fichaTecnica->descricao;
            $rs[] = $r;
        }
        return new JsonResponse($rs);
    }

    /**
     *
     * @Route("/fichaTecnica/salvarObs/{fichaTecnica}", name="fichaTecnica_salvarObs", defaults={"fichaTecnica"=null}, requirements={"fichaTecnica"="\d+"})
     * @param Request $request
     * @param FichaTecnica|null $fichaTecnica
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     *
     * @IsGranted("ROLE_PCP", statusCode=403)
     */
    public function salvarObs(Request $request, FichaTecnica $fichaTecnica)
    {
        $fichaTecnica->obs = ($request->get('obs'));
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception|\Psr\Cache\InvalidArgumentException
     *
     * @IsGranted("ROLE_PCP", statusCode=403)
     */
    public function itemForm(Request $request, ?FichaTecnicaItem $fichaTecnicaItem = null)
    {

        /** @var UnidadeRepository $repoUnidade */
        $repoUnidade = $this->getDoctrine()->getRepository(Unidade::class);

        if ($fichaTecnicaItem) {
            $this->fichaTecnicaBusiness->buildQtdesTamanhosArray($fichaTecnicaItem->fichaTecnica);
        }


        if ($request->get('btnSalvarItemForm')) {
            if ($request->get('ficha_tecnica_item_qtde')) {
                /** @var Insumo $insumo */
                $insumo = $this->getDoctrine()->getRepository(Insumo::class)->find($request->get('insumo'));
                $fichaTecnicaItem->insumo = ($insumo);
                $this->fichaTecnicaItemEntityHandler->handleSaveArrayQtdes($fichaTecnicaItem, $request->get('ficha_tecnica_item_qtde'));
            }
            $this->fichaTecnicaItemEntityHandler->save($fichaTecnicaItem);
            $this->addFlash('success', 'Registro salvo com sucesso!');
            return $this->redirectToRoute('fichaTecnica_builder', ['id' => $fichaTecnicaItem->fichaTecnica->getId()]);
        }


        $parameters = [];
        $parameters['insumos'] = $this->buildInsumosSelect2();
        $parameters['fichaTecnicaItem'] = $fichaTecnicaItem;
        $parameters['unidade'] = $repoUnidade->find($fichaTecnicaItem->insumo->unidadeProdutoId);

        return $this->doRender('fichaTecnicaItemForm.html.twig', $parameters);
    }

    /**
     *
     * @Route("/fichaTecnica/addItem/{fichaTecnica}", name="fichaTecnica_addItem", requirements={"fichaTecnica"="\d+"})
     * @param Request $request
     * @param FichaTecnica $fichaTecnica
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
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
        $fichaTecnica = $fichaTecnicaItem->fichaTecnica;
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

        $parameters['instituicoes'] = $this->fichaTecnicaBusiness->buildInstituicoesSelect2();
        $parameters['instituicaoId'] = $fichaTecnica->instituicao->getId();
        $parameters['fichaTecnicaOrigem'] = $fichaTecnica;
        $parameters['descricaoSugerida'] = $fichaTecnica->descricao . ' (2)';

        return $this->doRender('fichaTecnica_clonar.html.twig', $parameters);

    }


    /**
     *
     * @Route("/fichaTecnica/corrigirClientes", name="fichaTecnica_corrigirClientes")
     * @param ClienteEntityHandler $clienteEntityHandler
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws ViewException
     * @throws \Doctrine\DBAL\Exception
     */
    public function corrigirClientes(ClienteEntityHandler $clienteEntityHandler): Response
    {
        $conn = $this->fichaTecnicaItemEntityHandler->getDoctrine()->getConnection();

        $fichas = $conn->fetchAllAssociative('SELECT * FROM prod_fichatecnica');

        $repoFichaTecnica = $this->fichaTecnicaItemEntityHandler->getDoctrine()->getRepository(FichaTecnica::class);
        $repoCliente = $this->fichaTecnicaItemEntityHandler->getDoctrine()->getRepository(Cliente::class);


        foreach ($fichas as $ficha) {
            $cliente = $conn->fetchAllAssociative('SELECT * FROM crm_cliente WHERE pessoa_id = ' . $ficha['pessoa_id']);
            $pessoa = $conn->fetchAssociative('SELECT * FROM bse_pessoa WHERE id = ' . $ficha['pessoa_id']);
            if (!$cliente) {
                $cliente = new Cliente();
            } else {
                $cliente = $repoCliente->find($cliente[0]['id']);
            }
            $cliente->nome = $pessoa['nome'] ?? '';
            $cliente->jsonData['cliente_pcp'] = 'S';
            $clienteEntityHandler->save($cliente);
            /** @var FichaTecnica $fichaTecnica */
            $fichaTecnica = $repoFichaTecnica->find($ficha['id']);
            $fichaTecnica->instituicao = ($cliente);
        }
        return new Response('ok');

    }

    /**
     *
     * @Route("/fichaTecnica/corrigirClientesNasFichas", name="fichaTecnica_corrigirClientesNasFichas")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\DBAL\Exception
     */
    public function corrigirClientesNasFichas(): Response
    {
        $conn = $this->fichaTecnicaItemEntityHandler->getDoctrine()->getConnection();

        $clientes = $conn->fetchAllAssociative('select count(id) as qt, documento, nome from crm_cliente where nome IS NOT NULL AND json_data->>"$.cliente_pcp" = \'S\' GROUP BY documento, nome HAVING qt > 1');


        foreach ($clientes as $cliente) {
            $rsClientesIds = $conn->fetchAllAssociative('SELECT id FROM crm_cliente WHERE json_data->>"$.cliente_pcp" = \'S\' AND nome = :nome', ['nome' => $cliente['nome']]);
            $primeiroId = $rsClientesIds[0]['id'];
            $ids = '(';

            foreach ($rsClientesIds as $clienteId) {
                $ids .= $clienteId['id'] . ',';
            }
            $ids = substr($ids, 0, -1) . ')';

            $conn->executeQuery('UPDATE prod_fichatecnica SET cliente_id = :primeiroId WHERE cliente_id IN ' . $ids, ['primeiroId' => $primeiroId]);

            $conn->executeQuery('DELETE FROM crm_cliente WHERE id != :primeiroId AND id IN ' . $ids, ['primeiroId' => $primeiroId]);
// break;
        }


        return new Response('ok');

    }


}
