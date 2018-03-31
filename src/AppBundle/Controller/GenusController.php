<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Genus;
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
     * Should be above the showAction controller so that it takes precedence when they visit /genus/new
     * @Route("/genus/new")
     */
    public function newAction() : Response
    {
        $genus = New Genus();
        $genus->setName('Octopus'.rand(1,100));

        // Entity Manager
        $em = $this->getDoctrine()->getManager();

        // Save in the DB!
        $em->persist($genus);
        $em->flush();

        return new Response('<html><body>Genus created!</body></html>');
    }

    /**
     * @Route("/genus/{genusName}")
     */
    public function showAction(string $genusName) : Response
    {
        $funFact = "Octopuses can change the color of their body in just *three-tenths* of a second!";

        $cache = $this->get('doctrine_cache.providers.my_markdown_cache');
        $key = md5($funFact);
        if ($cache->contains($key)) {
           $funFact = $cache->fetch($key);
        } else {
            sleep(1);
            // $this->get() is the equivalent of $this->container->get()
            $funFact = $this->get('markdown.parser')
                ->transform($funFact);
            $cache->save($key, $funFact);
        }

        return $this->render('genus/show.html.twig', [
            'name' => $genusName,
            'funFact' => $funFact
        ]);
    }

    /**
     * This powers our React frontend
     *
     * @Route("/genus/{genusName}/notes", name="genus_show_notes")
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