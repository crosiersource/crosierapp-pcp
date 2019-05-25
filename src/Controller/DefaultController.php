<?php

namespace App\Controller;


use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
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
        $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache');
        $cache->clear();
        return $this->redirectToRoute('index');
    }


}