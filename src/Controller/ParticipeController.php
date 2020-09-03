<?php

namespace App\Controller;

use App\Repository\EventRepository;
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
}
