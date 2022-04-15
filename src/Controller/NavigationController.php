<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Group;
use App\Form\CreateEventType;
use App\Form\CreateGroupType;
use App\Repository\EventRepository;
use App\Repository\GroupRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NavigationController extends AbstractController
{
    //page A
    #[Route('/', name: 'homepage')]
    public function homepage(EntityManagerInterface $em,Request $request, EventRepository $eventRepository): Response
    {
        $event = new Event();
    
        $form = $this->createForm(CreateEventType::class, $event);
    
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($event);
            $em->flush();
            
            $token = $event->getAdminLinkToken();

            return $this->redirectToRoute('groups', [
                "adminToken" => $token,
            ]);
        }

        $formView = $form->createView();

        $events = $eventRepository->findAll();

        return $this->render('navigation/homepage.html.twig', [
            'formView' => $formView,
            'events' => $events
        ]);
    }

    //page B
    


}
