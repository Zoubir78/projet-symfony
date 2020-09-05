<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

     /**
     * @Route("/", name="homepage")
     */
    public function contact(Request $request, EntityManagerInterface $manager)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactFormType::class, $contact);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $contact = $form->getData();
            // dd($contact);
            
            $manager->persist($contact);
            $manager->flush();

            $this->addFlash('success', 'Votre message a été envoyé !');
            // return $this->redirectToRoute('homepage');

        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
