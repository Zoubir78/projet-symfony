<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Event;
use App\Entity\Contact;
use App\Form\InviteFormType;
use App\Form\ContactFormType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(Request $request, EventRepository $eventRepository)
    {
        $page = $request->query->get('page', 1);
        $events = $eventRepository->findPaginated($page);

        return $this->render('home/index.html.twig', [
            'events' => $events,
            'pagination_current' => $page,
            'pagination_offset'  => EventRepository::PAGINATION_OFFSET,
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

    /**
     * @Route("/event/{id}/participate", name="event_participate")
     * @IsGranted("ROLE_USER")
     */
    public function participate(Event $event, Request $request, EntityManagerInterface $entityManager)
    {
        // $token = $request->query->get('token');
        // if (!$this->isCsrfTokenValid('event-participate', $token)) {
        //     $this->addFlash('success', 'Une erreur est survenue.');
        //     return $this->redirectToRoute('event_page', ['id' => $event->getId()]);
        // }

        /** @var User $user */
        $user = $this->getUser();
        $user->addParticipation($event);
        // dd($user);

        $entityManager->flush();

        $this->addFlash('success', sprintf('Vous participez désormais à "%s" !', $event->getName()));
        // return $this->redirectToRoute('event_participate', ['id' => $event->getId()]);

        return $this->render('participe/index.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * @Route("/event/{id}/cancel-participation", name="event_cancel_participation")
     * @IsGranted("ROLE_USER")
     */
    public function cancelParticipation(Event $event, Request $request, EntityManagerInterface $entityManager)
    {
        $token = $request->query->get('token');
        if (!$this->isCsrfTokenValid('event-cancel-participation', $token)) {
            $this->addFlash('success', 'Une erreur est survenue.');
            return $this->redirectToRoute('event_page', ['id' => $event->getId()]);
        }

        /** @var User $user */
        $user = $this->getUser();
        $user->removeParticipation($event);

        $entityManager->flush();
        $this->addFlash('info', sprintf('Vous ne participez plus à "%s".', $event->getName()));
        return $this->redirectToRoute('event_page', ['id' => $event->getId()]);
    }

    /**
     * Email d'invitation
     * @Route("/event/{id}/participate", name="event_participate")
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
        return $this->render('participe/index.html.twig', [
            'event' => $event,
            'invite_form' => $inviteForm->createView(),
        ]);
    }
}
