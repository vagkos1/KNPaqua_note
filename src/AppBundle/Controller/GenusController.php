<?php


namespace AppBundle\Controller;


use AppBundle\AppBundle;
use AppBundle\Entity\Genus;
use AppBundle\Entity\GenusNote;
use AppBundle\Service\MarkdownTransformer;
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

        $genusNote = new GenusNote();
        $genusNote->setUsername('AquaWeaver');
        $genusNote->setUserAvatarFilename('ryan.jpeg');
        $genusNote->setNote('I counted 8 legs... as they wrapped around me');
        $genusNote->setCreatedAt(new \DateTime('-1 month'));
        $genusNote->setGenus($genus); // associate this $genusNote with $genus

        // Entity Manager
        $em = $this->getDoctrine()->getManager();

        // Save in the DB!
        $em->persist($genus);
        $em->persist($genusNote);

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
        // this is a Doctrine EntityRepository or a GenusRepository that extends the generic EntityRepository
        $genuses = $em->getRepository('AppBundle:Genus')
            ->findAllPublishedOrderedByRecentlyActive();

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

        /** @var Genus $genus */
        $genus = $em->getRepository('AppBundle:Genus')
            ->findOneBy(['name' => $genusName]);

        if (!$genus) {
            throw $this->createNotFoundException('No genus found');
        }

        $transformer = $this->get('app.markdown_transformer');
        $funFact = $transformer->parse($genus->getFunFact());

        $recentNotes = $em->getRepository('AppBundle:GenusNote')
            ->findAllRecentNotesForGenus($genus);

        return $this->render('genus/show.html.twig', [
            'genus' => $genus,
            'funFact' => $funFact,
            'recentNoteCount' => count($recentNotes)
        ]);
    }

    /**
     * This powers our React frontend
     * By Typehinting Genus, Symfony will find genus.name (param conversion)
     *
     * @Route("/genus/{name}/notes", name="genus_show_notes")
     * @Method("GET")
     */
    public function getNotesAction(Genus $genus) : Response
    {
        $notes = [];
        foreach ($genus->getNotes() as $note) {
            $notes[] = [
                'id' => $note->getId(),
                'userName' => $note->getUsername(),
                'avatarUri' => '/images/'.$note->getUserAvatarFilename(),
                'note' => $note->getNote(),
                'date' => $note->getCreatedAt()->format('M d, Y')
            ];
        }
        $data = [
          'notes' => $notes
        ];

        return new JsonResponse($data);
    }
}