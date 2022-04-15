<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Group;
use App\Form\CreateEventType;
use App\Form\CreateGroupType;
use App\Form\TokenLoginType;
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
    
        $formCreateEvent = $this->createForm(CreateEventType::class, $event);
    
        $formCreateEvent->handleRequest($request);
       

         if($formCreateEvent->isSubmitted() && $formCreateEvent->isValid()) {
            $em->persist($event);
            $em->flush();
            
            $token = $event->getAdminLinkToken();

            return $this->redirectToRoute('event_show', [
                "adminToken" => $token,
            ]);
        } 
        

        $formView = $formCreateEvent->createView();

        $events = $eventRepository->findAll();

        return $this->render('navigation/homepage.html.twig', [
            'formView' => $formView,
            'events' => $events
        ]);
    }

    #[Route('/login', name: 'navigation_login')]
    public function login(EntityManagerInterface $em,Request $request, EventRepository $eventRepository): Response
    {
        $formLogin = $this->createForm(TokenLoginType::class);

        $formLogin->handleRequest($request);

        if($formLogin->isSubmitted() && $formLogin->isValid()) {
            $data = $formLogin->getData();
            $tokenName = $data['adminLinkToken'];

            $eventAssociated = $eventRepository->findOneBy(['adminLinkToken' => $tokenName]);

            if(!$eventAssociated) {
                throw $this->createNotFoundException("Aucun groupe avec cet identifiant.");
            }

            return $this->redirectToRoute('event_show', [
                "adminToken" => $tokenName,
            ]);            
        }

        $formViewLogin = $formLogin->createView();

        return $this->render('navigation/login.html.twig', [
            'formViewLogin' => $formViewLogin,
        ]);

    }

    //page B
    


}
