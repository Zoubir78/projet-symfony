<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ParticipeController extends AbstractController
{
   /**
     * page liste des participations
     * @Route("/participe", name="participe")
     * @IsGranted("ROLE_USER")
     */
    public function participePage(Event $event, EventRepository $repository, Request $request, EntityManagerInterface $entityManager)
    {
        if ($this->getUser()) {
            // $participe = $repository->findOneBy([
            //     'author' => $this->getUser(),
            //     'events' => $event,
            // ]);

            // Si la participation n'existe pas, on en crée une nouvelle
            // $event = $event ?? (new Event())
            //     ->setAuthor($this->getUser())
            //     ->setEvent($event)
            //     ->setDate(new \DateTime());

            // $this->handleRequest($request);

            $entityManager->persist($event);
            $entityManager->flush();

            //Message flash
            $this->addFlash('success', 'Votre participation a été enregistrée !');
            // return $this->redirectToRoute('', ['id' => $event->getId()]);
        }    

        return $this->render('participe/index.html.twig', [
            'events' => $event,
            'liste_participations' => $repository->findAll(),
        ]);
    }

     /**
     * @Route("/{id}/annuler", name="annuler")
     * @IsGranted("ROLE_USER")
     */
    public function annuler(Event $event, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($event);
        $entityManager->flush();

        // Message & redirection
        $this->addFlash('info', 'L\'événement a été annulé.');
        return $this->redirectToRoute('participe');
    }
}
