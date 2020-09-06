<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\InviteFormType;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
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
     * @IsGranted("ROLE_USER")
     */
    public function ajout(Request $request, EntityManagerInterface $entityManager)
    {

        $form = $this->createForm(EventFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Event $event */
            $event = $form->getData();
            $event->setAuthor($this->getUser());
            // dd($event);

            $entityManager->persist($event);
            $entityManager->flush();

            //Message flash & redirection
            $this->addFlash('success', 'L\'événement a été enregistré !');
            return $this->redirectToRoute('event_list', ['id' => $event->getId()]);
        }
        return $this->render('event/ajout.html.twig', [
            'event_form' => $form->createView()
        ]);
    }

    /**
     * Modification d'un événement
     * @Route("/{id}", name="modif")
     */
    public function modification(Event $event, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
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

    /**
     * Page d'un événement
     * @Route("/{id}/page", name="page")
     */
    public function eventPage(Event $event)
    {
        return $this->render('event/event_page.html.twig', [
            'event' => $event
        ]);
    }

     /**
     * Email d'invitation
     * @Route("/{id}/page", name="page")
     * @IsGranted("ROLE_USER")
     */
    public function inviter(Event $event, Request $request, MailerInterface $mailer)
    {
        $inviteForm = $this->createForm(InviteFormType::class);
        $inviteForm->handleRequest($request);

        if ($inviteForm->isSubmitted() && $inviteForm->isValid()) {
            $email = (new TemplatedEmail())
                ->from(new Address('noreply@evently.com', 'My-Event'))
                ->to(new Address($inviteForm['email']->getData()))
                ->subject(sprintf('%s My-Event | Invitation à "%s"', "\u{1F5D3}", $event->getName()))
                ->htmlTemplate('emails/invitation.html.twig')
                ->context([
                    'event' => $event,
            ]);
            
            $mailer->send($email);
            $this->addFlash('success', 'Votre invitation a été envoyée.');
            return $this->redirectToRoute('event_page', ['id' => $event->getId()]);
        }
        return $this->render('event/event_page.html.twig', [
            'event' => $event,
            'invite_form' => $inviteForm->createView(),
        ]);
    }
}

