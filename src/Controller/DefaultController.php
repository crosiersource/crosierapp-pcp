<?php

namespace App\Controller;


use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
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
    public function index()
    {
        $params['PROGRAM_UUID'] = '69a3bd02-c887-4319-bae0-3f1cd20c5608';
        return $this->doRender('dashboard.html.twig', $params);
    }

}