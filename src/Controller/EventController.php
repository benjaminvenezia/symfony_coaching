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
    public function show(Request $request, string $adminToken, ClassService $classService, GroupRepository $groupRepository,TicketRepository $ticketRepository, EventRepository $eventRepository, EntityManagerInterface $em): Response
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

        /**
         * @var int $counterTicketsForThisEvent number of tickets for all the groups of the event.
         */
        $counterTicketsForThisEvent = 0;

        foreach ($groups as $g){
            $n = count($ticketRepository->findBy(['group_ticket_id' => $g->getId()]));
            $counterTicketsForThisEvent += $n;
        }
        
        return $this->render('navigation/eventpage.html.twig', [
            'nbArticles' => $counterTicketsForThisEvent,
            'adminToken' => $adminToken,
            'formView' => $formView, 
            'groups' => $groups,
            'event' => $event
        ]);
    }
}
