<?php

namespace App\Controller;


use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
use Fpdf\Fpdf;
use setasign\Fpdi\Tcpdf\Fpdi;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package App\Controller
 * @author Carlos Eduardo Pauluk
 */
class DefaultController extends BaseController
{

    /**
     *
     * @Route("/", name="index")
     */
    public function index(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->doRender('dashboard.html.twig');
    }

    /**
     *
     * @Route("/limparCaches", name="limparCaches")
     */
    public function limparCaches(): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);
        $cache->delete("buildClientesSelect2");
        $cache->delete("buildInsumosSelect2");
        return $this->redirectToRoute('index');
    }


    /**
     *
     * @Route("/pdf", name="pd")
     */
    public function pdf(): Response
    {

        $pdf = new Fpdi('P', 'mm', 'A4'); //FPDI extends TCPDF

        $pages = $pdf->setSourceFile('/home/carlos/Downloads/cotacao/scan3745_com_ocr.pdfa.pdf');

        $certificate = 'file://home/carlos/Dropbox/Ipê/Certs/2021/chave.pfx';

// set additional information


        for ($i = 1; $i <= $pages; $i++) {
            $pdf->AddPage();
            $page = $pdf->importPage($i);
            $pdf->useTemplate($page, 0, 0);


            // set document signature
            $pdf->setSignature($certificate, $certificate, 'tcpdfdemo', '', 2, $info);

        }
        return new Response("OK");
    }


    /**
     *
     * @Route("/nosec", name="nosec", methods={"GET"})
     * @return Response
     */
    public function nosec(): Response
    {
        return new Response('nosec OK');
    }


    /**
     * @Route("/v/{vuePage}", name="v_vuaPage", requirements={"vuePage"=".+"})
     */
    public function vuePage($vuePage): Response
    {
        $rURL = $this->doctrine->getConnection()->fetchAssociative('SELECT valor FROM cfg_app_config WHERE app_uuid = :appUUID AND chave = :chave', [
            'appUUID' => $_SERVER['CROSIERAPP_UUID'],
            'chave' => 'URL_' . $_SERVER['CROSIER_ENV']
        ]);

        $params = [
            'jsEntry' => $vuePage,
            'serverParams' => json_encode(
                ['radxURL' => $rURL['valor']]
            )
        ];

        return $this->doRender('@CrosierLibBase/vue-app-page.html.twig', $params);
    }


}