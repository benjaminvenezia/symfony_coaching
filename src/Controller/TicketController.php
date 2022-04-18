<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\GroupRepository;
use App\Repository\StatusRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function delete(string $linkToken, $ticketId,EntityManagerInterface $em, TicketRepository $ticketRepository, StatusRepository $statusRepository): Response
    {
        $ticket = $ticketRepository->find($ticketId);

        if(!$ticket) {
            throw new NotFoundHttpException("Le ticket que vous souhaitez supprimer n'existe pas.");
        }
        
        //find status binded to ticket and delete it before delete ticket.
        $status = $statusRepository->findOneBy(['ticket_status' => $ticket->getId()]);

        $em->remove($status);
        $em->remove($ticket);
        $em->flush();

        return $this->redirectToRoute('group_show', ['linkTokenParam' => $linkToken]);

    }

    #[Route('/ticket/{ticketId}/changestatus', name: 'ticket_changestatus')]
    public function changeStatus( $ticketId,EntityManagerInterface $em,EventRepository $eventRepository, GroupRepository $groupRepository, TicketRepository $ticketRepository, StatusRepository $statusRepository): Response
    {
        $ticket = $ticketRepository->find($ticketId);
        $groupId = $ticket->getGroupTicketId();
        $group = $groupRepository->findOneBy(['id' => $groupId]);
        $eventId = $group->getEvent();
        $event = $eventRepository->findOneBy(['id' => $eventId]);
        $adminLinkToken = $event->getAdminLinkToken();
        // dd($adminToken);
        if(!$ticket){
            throw new NotFoundHttpException("Pas de ticket avec l'identifant " . $ticketId);
        }

        $status = $statusRepository->findOneBy(['ticket_status' => $ticket->getId()]);

        if(!$status){
            throw new NotFoundHttpException("Pas de status avec l'identifant ");
        }

        $status->setIsArchived(!$status->getIsArchived());
       
        // $status = ->getTicketStatus();
        // dd($status);
        //permet de retrouver le ticket lié au status.
        // dd($status[0]->getTicketStatus());

        $em->persist($status);
        $em->flush();

        return $this->redirectToRoute('tickets_show', ['adminLinkToken' => $adminLinkToken]);

    }
}
