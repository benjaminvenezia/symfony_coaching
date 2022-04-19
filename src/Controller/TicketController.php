<?php

namespace App\Controller;

use App\Entity\Status;
use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\EventRepository;
use App\Repository\GroupRepository;
use App\Repository\StatusRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Knp\Component\Pager\PaginatorInterface;

class TicketController extends AbstractController
{

    
    #[Route('/{adminLinkToken}/tickets/show', name: 'tickets_show')]
    public function show(string $adminLinkToken, Request $request, GroupRepository $groupRepository, EventRepository $eventRepository, TicketRepository $ticketRepository, StatusRepository $statusRepository, PaginatorInterface $paginator): Response
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

            //Pour chaque groupe de ticket, on lui associe son statut de la table Status.
            foreach($ticketsForThisGroup as $ticket){
                $status = $statusRepository->findOneBy(['ticket_status' => $ticket->getId()]);
                $ticket->setIsArchived($status->getIsArchived());
            }

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

    #[Route('/{linkToken}/ticket/{ticketId}/update', name: 'ticket_update')]
    public function update(string $linkToken, $ticketId, Request $request, EntityManagerInterface $em, TicketRepository $ticketRepository, StatusRepository $statusRepository): Response
    {
        $ticket = $ticketRepository->find($ticketId);

        if(!$ticket) {
            throw new NotFoundHttpException("Le ticket que vous souhaitez modifié n'existe pas.");
        }

        $form = $this->createForm(TicketType::class, $ticket);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
                   
            $em->flush();

            // $this->addFlash('info', "L'article a été modifié avec succès.");
            return $this->redirectToRoute('group_show', ['linkTokenParam' => $linkToken]);
        }

        $formView = $form->createView();
        
        return $this->render('navigation/ticketupdate.html.twig', [
            'formView' => $formView,
            'linkTokenParam' => $linkToken,
        ]);
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

        $status->setIsArchived(!$status->getIsArchived());
       
        $em->persist($status);
        $em->flush();

        return $this->redirectToRoute('tickets_show', ['adminLinkToken' => $adminLinkToken]);

    }
}
