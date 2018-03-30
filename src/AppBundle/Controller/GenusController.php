<?php


namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            'name' => $genusName,
        ]);
    }

    /**
     * @Route("/genus/{genusName}/notes")
     * @Method("GET")
     */
    public function getNotesAction() : Response
    {
        $notes = [
            ['id' => 1, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Octopus asked me a riddle, outsmarted me!', 'date' => 'Dec. 10, 2016'],
            ['id' => 2, 'username' => 'AquaWeaver', 'avatarUri' => '/images/ryan.jpeg', 'note' => 'I counted 8 legs... as they wrapped around me', 'date' => 'Dec. 1, 2016'],
            ['id' => 3, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Inked', 'date' => 'Aug. 15, 2016'],
        ];

        $data = [
          'notes' => $notes
        ];

        return new JsonResponse($data);
    }
}