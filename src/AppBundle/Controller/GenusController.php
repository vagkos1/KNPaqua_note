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
        // for some reason, the templating service is not currently available in the container so we'll use the twig one.
        $templating = $this->container->get('twig');

        $html = $templating->render('genus/show.html.twig', [
            'name' => $genusName
        ]);

        return new Response($html);
    }
}