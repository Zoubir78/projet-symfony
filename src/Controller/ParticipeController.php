<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ParticipeController extends AbstractController
{
   /**
     * @Route("/participe", name="participe")
     * @IsGranted("ROLE_USER")
     */
    public function participePage(EventRepository $repository)
    {
        return $this->render('participe/index.html.twig', [
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
