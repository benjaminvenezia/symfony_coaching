<?php

namespace App\Controller;

use DateTime;
use App\Entity\Group;
use App\Entity\Status;
use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\EventRepository;
use App\Repository\GroupRepository;
use App\Repository\StatusRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GroupController extends AbstractController
{
    
    #[Route('/group/delete/{id}', name: 'group_delete')]
    public function delete(
        $id, 
        GroupRepository $groupRepository, 
        TicketRepository $ticketRepository, 
        EntityManagerInterface $em
    ): Response
    {
        /**
         * @var Group $group
         */
        $group = $groupRepository->find($id);

        /**
         * @var String $adminToken
         */
        $adminToken = $group->getEvent()->getAdminLinkToken();

        if(null === $group) {
          throw $this->createNotFoundException("Le groupe n'existe pas et ne peut pas être supprimé");
        }
        
        if($ticketRepository->findBy(['group_ticket_id' => $group->getId()]) != null) {
            throw new Exception("Le groupe possède des tickets.");
        }
      
        $em->remove($group);
        $em->flush();
       
        return $this->redirectToRoute('event_show', ['adminToken' => $adminToken]);
    }

    #[Route('/group/help/{id}', name: 'group_help', requirements: ['id' => '\d+'])]
    public function help(
        int $id,
        GroupRepository $groupRepository, 
        EntityManagerInterface $em 
    ): Response
    {
        /**
         * @var Group $group
         */
        $group = $groupRepository->find($id);
        
        /**
         * @var String $adminToken
         */
        $adminToken = $group->getEvent()->getAdminLinkToken();

        if(!$group) {
          throw $this->createNotFoundException("Le groupe n'existe pas et ne peut pas être supprimé");
        }
        
        $group->incrementHelpedCounter();

        $em->persist($group);
        $em->flush();

        return $this->redirectToRoute('event_show', ['adminToken' => $adminToken]);
    }

    #[Route('/group/{linkTokenParam}', name: 'group_show')]
    public function show(
        string $linkTokenParam, 
        GroupRepository $groupRepository, 
        TicketRepository $ticketRepository, 
        StatusRepository $statusRepository, 
        EntityManagerInterface $em, 
        Request $request, 
        PaginatorInterface $paginator
    ): Response
    {
        /**
         * @var Group $group
         */
        $group = $groupRepository->findOneBy(['linkToken' => $linkTokenParam]);

        if(!$group) {
            throw $this->createNotFoundException("Le groupe n'existe pas.");
        }

        /**
         * @var Ticket[] $ticketsGroup 
         */
        $ticketsGroup = $ticketRepository->findBy(['group_ticket_id' => $group->getId()]);

        $ticketsGroup = $paginator->paginate(
            $ticketsGroup,
            $request->query->getInt('page', 1),
            2
        );

        if($linkTokenParam !== $group->getLinkToken()){
            throw $this->createNotFoundException("Le groupe n'existe pas.");
        }
        /**
         * @var Ticket $ticket
         */
        $ticket = new Ticket();

        /**
         * @var Status $status
         */
        $status = new Status();

        $status->setName('status de test');
        $status->setIsArchived(false);
        $status->setTicketStatus($ticket);

        $ticket->setGroupTicketId($group);

        $formTicket= $this->createForm(TicketType::class, $ticket);
        $formTicket->handleRequest($request);
        
        if($formTicket->isSubmitted() && $formTicket->isValid()) {
            $em->persist($ticket);
            $em->persist($status);

            $em->flush();

            return $this->redirectToRoute('group_show', [
                "linkTokenParam" => $linkTokenParam
            ]);            
        }
        //on s'assure à chaque re-rendu que l'attribut isArchived est bien mis à jour pour absolument tous les tickets.
        $allTickets = $ticketRepository->findAll();

        foreach($allTickets as $ticket){
            $status = $statusRepository->findOneBy(['ticket_status' => $ticket->getId()]);
            $ticket->setIsArchived($status->getIsArchived());
        }

        $form = $formTicket->createView();

        return $this->render('navigation/grouppage.html.twig', [
            "linktoken" => $linkTokenParam,
            "formView" => $form,
            "group" => $group,
            "tickets" => $ticketsGroup
        ]);   
    }
}
