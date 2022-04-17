<?php

namespace App\Controller;

use App\Entity\Event;
use DateTime;
use App\Entity\Group;
use App\Form\CreateGroupType;
use App\Repository\EventRepository;
use App\Repository\GroupRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Services\ClassService;

class EventController extends AbstractController
{
    #[Route('/event/{adminToken}/groups', name: 'event_show')]
    public function show(Request $request, string $adminToken, ClassService $classService, GroupRepository $groupRepository, EventRepository $eventRepository, EntityManagerInterface $em): Response
    {
        /**
         * @var Group $group 
         */
        $group = new Group();
    
        $form = $this->createForm(CreateGroupType::class, $group);
    
        $form->handleRequest($request);

         /**
          * @var Event $event
          */
         $event = $eventRepository->findOneBy([
            'adminLinkToken' => $adminToken
        ]);

        if($form->isSubmitted() && $form->isValid()) {
            //on genère un nouveau token qu'on associe au groupe

            /**
             * @var String $groupToken random key associated to event.
             */
            $grouptoken = $classService->generateToken();
            $group->setLinkToken($grouptoken);
            $group->setLastArchived(new DateTime());
            $group->setEvent($event);

            $em->persist($group);
            $em->flush();

            return $this->redirectToRoute('event_show', [
                "adminToken" => $adminToken,
            ]);
        }
        $formView = $form->createView();
        
        //returns groups of this event
        $groups = $groupRepository->findBy([
            'event' => $event->getId(), 
        ], ['last_helped' => 'ASC']);

        // $tickets = [];
        // foreach ($groups as $g){
        //     $ticketsForThisGroup = $ticketRepository->findBy(['group_ticket_id' => $g->getId()]);
        //     array_push($tickets, $ticketsForThisGroup);
        // }
      
        //returns ticket for this event
        // $tickets = $ticketRepository->createQueryBuilder('u')
        // ->select('count(u.id)')
        // ->getQuery()
        // ->getSingleScalarResult();
        

        
        return $this->render('navigation/eventpage.html.twig', [
            'adminToken' => $adminToken,
            'formView' => $formView, 
            'groups' => $groups,
            'event' => $event
        ]);
    }
}
