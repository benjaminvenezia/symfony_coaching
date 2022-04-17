<?php

namespace App\Controller;

use Doctrine\Common\EventManager;
use App\Repository\EventRepository;
use App\Repository\GroupRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TicketController extends AbstractController
{
    #[Route('/{adminLinkToken}/tickets/show', name: 'tickets_show')]
    public function show(string $adminLinkToken, GroupRepository $groupRepository, EventRepository $eventRepository, TicketRepository $ticketRepository): Response
    {
        //find event 
        $event = $eventRepository->findOneBy([
            'adminLinkToken' => $adminLinkToken
        ]);
        
        //find groups by event
        $groups = $groupRepository->findBy([
            'event' => $event->getId(), 
        ], ['last_helped' => 'ASC']);
        

        /**
         * @var Ticket[] $tickets
         */
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

    #[Route('/{linkToken}/ticket/{ticketId}/delete', name: 'ticket_delete')]
    public function delete(string $linkToken, $ticketId,EntityManagerInterface $em, GroupRepository $groupRepository, EventRepository $eventRepository, TicketRepository $ticketRepository): Response
    {
        //find ticket 
        $ticket = $ticketRepository->find($ticketId);

        if(!$ticket) {
            throw new NotFoundHttpException("Le ticket que vous souhaitez supprimer n'existe pas.");
        }

        $em->remove($ticket);
        $em->flush();

        return $this->redirectToRoute('group_show', ['linkTokenParam' => $linkToken]);

    }
}
