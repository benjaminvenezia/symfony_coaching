<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\GroupRepository;
use App\Repository\TicketRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TicketController extends AbstractController
{
    #[Route('/{adminLinkToken}/tickets/show', name: 'tickets_show')]
    public function show($adminLinkToken, GroupRepository $groupRepository, EventRepository $eventRepository, TicketRepository $ticketRepository): Response
    {
        //find event 
        $event = $eventRepository->findOneBy([
            'adminLinkToken' => $adminLinkToken
        ]);
        //find groups by event
        $groups = $groupRepository->findBy([
            'event' => $event->getId(), 
        ], ['last_helped' => 'ASC']);
        //grasp tickets
        $tickets = [];
        foreach ($groups as $g){
            $ticketsForThisGroup = $ticketRepository->findBy(['group_ticket_id' => $g->getId()]);
            array_push($tickets, $ticketsForThisGroup);
        }

        return $this->render('navigation/eventtickets.html.twig', [
            'tickets' => $tickets,
            'event' => $event,
            'adminToken' => $adminLinkToken
        ]);
    }

    #[Route('/{linkToken}/ticket/{id}/delete', name: 'tickets_delete')]
    public function delete($adminLinkToken, GroupRepository $groupRepository, EventRepository $eventRepository, TicketRepository $ticketRepository): Response
    {
        //find event 
        $event = $eventRepository->findOneBy([
            'adminLinkToken' => $adminLinkToken
        ]);
        //find groups by event
        $groups = $groupRepository->findBy([
            'event' => $event->getId(), 
        ], ['last_helped' => 'ASC']);
        //grasp tickets
        $tickets = [];
        foreach ($groups as $g){
            $ticketsForThisGroup = $ticketRepository->findBy(['group_ticket_id' => $g->getId()]);
            array_push($tickets, $ticketsForThisGroup);
        }

        return $this->render('navigation/eventtickets.html.twig', [
            'tickets' => $tickets,
            'event' => $event,
            'adminToken' => $adminLinkToken
        ]);
    }
}
