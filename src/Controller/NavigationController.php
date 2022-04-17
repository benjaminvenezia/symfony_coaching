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
use App\Services\ClassService;

class NavigationController extends AbstractController
{
    //page A
    #[Route('/', name: 'homepage')]
    public function homepage(EntityManagerInterface $em,Request $request, EventRepository $eventRepository, ClassService $classService): Response
    {
        $event = new Event();
        $formCreateEvent = $this->createForm(CreateEventType::class, $event);
        $formCreateEvent->handleRequest($request);
       
         if($formCreateEvent->isSubmitted() && $formCreateEvent->isValid()) {
            //on genère un token.
            $generatedToken = $classService->generateToken();
            $event->setAdminLinkToken($generatedToken);

            $em->persist($event);
            $em->flush();
            
            // $token = $event->getAdminLinkToken();

            return $this->redirectToRoute('event_show', [
                "adminToken" => $generatedToken,
            ]);
        } 
        

        $formView = $formCreateEvent->createView();

        $events = $eventRepository->findAll();

        return $this->render('navigation/homepage.html.twig', [
            'formView' => $formView,
            'events' => $events
        ]);
    }

    #[Route('/logincoach', name: 'navigation_logincoach')]
    public function loginCoach(Request $request, EventRepository $eventRepository): Response
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

        return $this->render('navigation/logincoach.html.twig', [
            'formViewLogin' => $formViewLogin,
        ]);

    }

    #[Route('/loginuser', name: 'navigation_loginuser')]
    public function loginUser(Request $request, GroupRepository $groupRepository, EventRepository $eventRepository): Response
    {
        $formLogin = $this->createForm(TokenLoginType::class);

        $formLogin->handleRequest($request);
       
        if($formLogin->isSubmitted() && $formLogin->isValid()) {
            $data = $formLogin->getData();
            $tokenName = $data['adminLinkToken']; //user token
            
            return $this->redirectToRoute('group_show', [
                "linkTokenParam" => $tokenName,
            ]);            
        }

        $formViewLogin = $formLogin->createView();

        return $this->render('navigation/loginuser.html.twig', [
            'formViewLogin' => $formViewLogin,
        ]);

    }

    //page B
    


}
