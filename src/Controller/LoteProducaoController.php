<?php

namespace App\Controller;


use App\Business\LoteProducaoBusiness;
use App\Entity\LoteProducao;
use App\Entity\LoteProducaoItem;
use App\EntityHandler\LoteProducaoEntityHandler;
use App\EntityHandler\LoteProducaoItemEntityHandler;
use App\Form\LoteProducaoItemType;
use App\Form\LoteProducaoType;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\ExceptionUtils\ExceptionUtils;
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

            'formView' => 'loteProducaoForm.html.twig',
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

            'role_access' => 'ROLE_PCP',
            'role_delete' => 'ROLE_PCP',

        ];

    /** @var LoteProducaoBusiness */
    private $loteProducaoBusiness;

    /** @var LoteProducaoItemEntityHandler */
    private $loteProducaoItemEntityHandler;

    /**
     * @required
     * @param LoteProducaoEntityHandler $entityHandler
     */
    public function setEntityHandler(LoteProducaoEntityHandler $entityHandler): void
    {
        $this->entityHandler = $entityHandler;
    }

    /**
     * @required
     * @param LoteProducaoBusiness $loteProducaoBusiness
     */
    public function setLoteProducaoBusiness(LoteProducaoBusiness $loteProducaoBusiness): void
    {
        $this->loteProducaoBusiness = $loteProducaoBusiness;
    }

    /**
     * @required
     * @param LoteProducaoItemEntityHandler $loteProducaoItemEntityHandler
     */
    public function setLoteProducaoItemEntityHandler(LoteProducaoItemEntityHandler $loteProducaoItemEntityHandler): void
    {
        $this->loteProducaoItemEntityHandler = $loteProducaoItemEntityHandler;
    }

    /**
     * @param array $params
     * @return array
     */
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

        $loteProducaoItem = new LoteProducaoItem();
        $loteProducaoItem->setLoteProducao($loteProducao);

        if ($loteProducao) {
            $this->loteProducaoBusiness->buildLoteQtdesTamanhosArray($loteProducao);
        }

        $formItem = $this->createForm(LoteProducaoItemType::class, $loteProducaoItem);
        $formItem->handleRequest($request);

        if ($formItem->isSubmitted()) {
            if ($formItem->isValid()) {
                try {
                    $entity = $formItem->getData();
                    $this->loteProducaoItemEntityHandler->save($entity);
                    $this->addFlash('success', 'Registro salvo com sucesso!');
                    return $this->redirectToRoute('loteProducao_form', ['id' => $loteProducao->getId(), '_fragment' => 'itens']);
                } catch (ViewException $e) {
                    $this->addFlash('error', $e->getMessage());
                } catch (\Exception $e) {
                    $msg = ExceptionUtils::treatException($e);
                    $this->addFlash('error', $msg);
                    $this->addFlash('error', 'Erro ao salvar!');
                }
            } else {
                $errors = $formItem->getErrors(true, true);
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
        }

        $parameters = [];
        $parameters['formItem'] = $formItem->createView();

        return $this->doForm($request, $loteProducao, $parameters);
    }

    /**
     *
     * @Route("/loteProducaoItem/form/{loteProducaoItem}", name="loteProducaoItem_form", defaults={"loteProducaoItem"=null}, requirements={"loteProducaoItem"="\d+"})
     * @param Request $request
     * @param LoteProducaoItem|null $loteProducaoItem
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function itemForm(Request $request, LoteProducaoItem $loteProducaoItem)
    {

        if ($loteProducaoItem) {
            $this->loteProducaoBusiness->buildLoteQtdesTamanhosArray($loteProducaoItem->getLoteProducao());
        }

        $form = $this->createForm(LoteProducaoItemType::class, $loteProducaoItem);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $entity = $form->getData();
                    if ($request->get('lote_producao_item_qtde')) {
                        $this->loteProducaoItemEntityHandler->handleSaveArrayQtdes($entity, $request->get('lote_producao_item_qtde'));
                    }
                    $this->loteProducaoItemEntityHandler->save($entity);
                    $this->addFlash('success', 'Registro salvo com sucesso!');
                    return $this->redirectToRoute('loteProducao_form', ['id' => $loteProducaoItem->getLoteProducao()->getId(), '_fragment' => 'itens']);
                } catch (ViewException $e) {
                    $this->addFlash('error', $e->getMessage());
                } catch (\Exception $e) {
                    $msg = ExceptionUtils::treatException($e);
                    $this->addFlash('error', $msg);
                    $this->addFlash('error', 'Erro ao salvar!');
                }
            } else {
                $errors = $form->getErrors(true, true);
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
        }

        $parameters = [];
        $parameters['form'] = $form->createView();
        $parameters['loteProducaoItem'] = $loteProducaoItem;

        return $this->doRender('loteProducaoItemForm.html.twig', $parameters);
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

    /**
     * @Route("/loteProducao/deleteItem/{loteProducaoItem}/", name="loteProducao_deleteItem", requirements={"loteProducaoItem"="\d+"})
     * @param Request $request
     * @param LoteProducaoItem $loteProducaoItem
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteItem(Request $request, LoteProducaoItem $loteProducaoItem): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $loteProducao = $loteProducaoItem->getLoteProducao();
        $this->checkAccess('loteProducao_deleteItem');
        if (!$this->isCsrfTokenValid('loteProducao_deleteItem', $request->request->get('token'))) {
            $this->addFlash('error', 'Erro interno do sistema.');
        } else {
            try {
                $this->loteProducaoItemEntityHandler->delete($loteProducaoItem);
                $this->addFlash('success', 'Registro deletado com sucesso.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erro ao deletar registro.');
            }
        }

        return $this->redirectToRoute('loteProducao_form', ['id' => $loteProducao->getId(), '_fragment' => 'itens']);
    }


}