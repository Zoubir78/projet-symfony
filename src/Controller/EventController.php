<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/event", name="event_")
 */

class EventController extends AbstractController
{
    /**
     * @Route("/", name="list")
     */
    public function index(EventRepository $repository)
    {
        return $this->render('event/index.html.twig', [
            'liste_evenements' => $repository->findAll(),
        ]);
    }

    /**
     * page d'ajout des événements
     * @Route("/ajout", name="ajout")
     */
    public function ajout(Request $request, EventRepository $repository, EntityManagerInterface $entityManager)
    {
        // Afficher le formulaire uniquement si l'utilisateur est connecté
        if ($this->getUser()) {
            // Rechercher une note déjà existante pour la modifier
            $event = $repository->findOneBy([
                'author' => $this->getUser()
            ]);

            // Si l'événement n'existe pas, on en crée un
            $event = $event ?? (new Event())
                ->setAuthor($this->getUser())
                ->setCreatedAt(new \DateTime());

            $form = $this->createForm(EventFormType::class, $event);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){

                // 4) Récupèrer les données du formulaire
                $event = $form->getData();
                // dd($event);

                $entityManager->persist($event);
                $entityManager->flush();

                //Message flash & redirection
                $this->addFlash('success', 'L\'événement a été enregistré !');
                return $this->redirectToRoute('event_list', ['id' => $event->getId()]);
            }
            // 5) Pour afficher le formulaire, passer le résultat au formulaire
            return $this->render('event/ajout.html.twig', [
                'event_form' => $form->createView()
            ]);
        }
    }

    /**
     * Modification d'un événement
     * @Route("/{id}", name="modif")
     * le composant ParamConverter va convertir le paramètre id en l'entité associée
     * @IsGranted("EVENT_EDIT", subject="event")
     */
    public function modification(Event $event, Request $request, EntityManagerInterface $entityManager)
    {
        // On passe l'entité à modifier en 2ème argument (arg. "data")
        // Pour que l'objet soit directement modifier et pour pré-remplir le formulaire
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // Il n'est pas nécessaire de récupèrer les données du formulaire: l'entité a été modifiée par celui-ci
            // On appelle pas non plus $entityManager->persist() car Doctrine connait déjà l'existance de l'entité 
            $entityManager->flush();
            $this->addFlash('success', 'L\'événement a été mis à jour.');
            return $this->redirectToRoute('event_list');
        }

        return $this->render('event/modif.html.twig', [
            'event' => $event,
            'event_form' => $form->createView(),
        ]);
    }

    /**
     * Suppression d'un événement
     * @Route("/{id}/supprimer", name="supprimer")
     * @IsGranted("EVENT_DELETE", subject="event")
     */
    public function supprimer(Event $event, EntityManagerInterface $entityManager)
    {
        // Suppression
        $entityManager->remove($event);
        $entityManager->flush();

        // Message & redirection
        $this->addFlash('info', 'L\'événement a été supprimé.');
        return $this->redirectToRoute('event_list');
    }
}

