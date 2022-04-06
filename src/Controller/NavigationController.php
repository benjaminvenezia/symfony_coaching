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
    #[Route('/event/{adminToken}/groups', name: 'groups')]
    public function groupsPage(EntityManagerInterface $em ,Request $request, EventRepository $eventRepository, GroupRepository $groupRepository, $adminToken): Response
    {
        //créer un nouveau groupe
        $group = new Group();
    
        $form = $this->createForm(CreateGroupType::class, $group);
    
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            //on set le token de l'événement au groupe
            $group->setLinkToken($adminToken);
            $group->setLastArchived(new DateTime());

            $em->persist($group);

            $em->flush();

            return $this->redirectToRoute('groups', [
                "adminToken" => $adminToken,
            ]);
        }

        $formView = $form->createView();

        $event = $eventRepository->findOneBy([
            'adminLinkToken' => $adminToken
        ]);

        $groups = $groupRepository->findBy([
            'linkToken' => $adminToken
        ]);
        
        return $this->render('navigation/groupspage.html.twig', [
            'formView' => $formView, 
            'groups' => $groups,
            'event' => $event
        ]);
    }


}
