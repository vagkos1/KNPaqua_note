<?php

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Form\UserRegistrationForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     */
    public function registerAction(Request $request)
    {
        // uses the form.factory service in the background
        $form = $this->createForm(UserRegistrationForm::class);

        // only handles data on POST.
        // If the user just navigates to the form (GET), the handleRequest does nothing.
        $form->handleRequest($request);

        // Technically, $form->isSubmitted() is not needed
        if ($form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // gets the session service and adds a flashBag
            $this->addFlash(
                'success',
                'Welcome ' . $user->getEmail()
            );

            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}