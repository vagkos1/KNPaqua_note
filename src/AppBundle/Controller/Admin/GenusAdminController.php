<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Form\GenusFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class GenusAdminController extends Controller
{
    /**
     * @Route("/genus", name="admin_genus_list")
     */
    public function indexAction()
    {
        $genuses = $this->getDoctrine()
            ->getRepository('AppBundle:Genus')
            ->findAll();

        return $this->render('admin/genus/list.html.twig', [
            'genuses' => $genuses
        ]);
    }

    /**
     * @Route("/genus/new", name="admin_genus_new")
     */
    public function newAction(Request $request)
    {
        // uses the form.factory service in the background
        $form = $this->createForm(GenusFormType::class);

        // only handles data on POST.
        // If the user just navigates to the form (GET), the handleRequest does nothing.
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $genus = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($genus);
            $em->flush();

            // gets the session service and adds a flashBag
            $this->addFlash('success', 'Genus created - you are amazing!');

            return $this->redirectToRoute('admin_genus_list');
        }

        return $this->render('admin/genus/new.html.twig', [
            'genusForm' => $form->createView()
        ]);
    }
}