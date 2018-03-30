<?php


namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GenusController
 * By extending Symfony's Controller, we gain access to the container since that Controller uses the ContainerAwareTrait
 *
 * @package AppBundle\Controller
 */
class GenusController extends Controller
{
    /**
     * @Route("/genus/{genusName}")
     */
    public function showAction(string $genusName) : Response
    {
        return $this->render('genus/show.html.twig', [
            'name' => $genusName
        ]);
    }
}