<?php


namespace AppBundle\Controller;


use AppBundle\AppBundle;
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
        $genus = new Genus();
        $genus->setName('Octopus'.rand(1,100));
        $genus->setSubFamily('Octopodinae');
        $genus->setSpeciesCount(rand(100, 99999));

        // Entity Manager
        $em = $this->getDoctrine()->getManager();

        // Save in the DB!
        $em->persist($genus);
        $em->flush();

        return new Response('<html><body>Genus created!</body></html>');
    }

    /**
     * @Route("/genus")
     *
     * @return Response
     */
    public function listAction() : Response
    {
        // whenever we want to interact with the DB, we grab the doctrine's Entity Manager
        $em = $this->getDoctrine()->getManager();

        // Get stuff out of the DB via Doctrine: use a repository
        // can also use "AppBundle\Entity\Genus" but the shorthand provided is more common
        $genuses = $em->getRepository('AppBundle:Genus')
            ->findAll();
        
        return $this->render('genus/list.html.twig', [
           'genuses' => $genuses
        ]);
    }

    /**
     * @Route("/genus/{genusName}", name="genus_show")
     */
    public function showAction(string $genusName) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $genus = $em->getRepository('AppBundle:Genus')
            ->findOneBy(['name' => $genusName]);

        if (!$genus) {
            throw $this->createNotFoundException('No genus found');
        }

        /*
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
        */

        return $this->render('genus/show.html.twig', [
            'genus' => $genus
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