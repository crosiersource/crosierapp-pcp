<?php

namespace App\Controller;


use App\Business\LoteProducaoBusiness;
use App\Entity\LoteProducao;
use App\Entity\LoteProducaoItem;
use App\EntityHandler\LoteProducaoEntityHandler;
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

        ];

    /** @var LoteProducaoBusiness */
    private $loteProducaoBusiness;

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
                    $this->getEntityHandler()->save($entity);
                    $this->addFlash('success', 'Registro salvo com sucesso!');
                    // return $this->redirectTo($request, $entity, $parameters);
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