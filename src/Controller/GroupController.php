<?php

namespace App\Controller;

use DateTime;
use App\Entity\Group;
use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\EventRepository;
use App\Repository\GroupRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GroupController extends AbstractController
{
    // protected $em;
    // protected $groupRepository;
    // protected $eventRepository;
    // protected $ticketRepository;

    // public function __construct(EntityManagerInterface $em, GroupRepository $groupRepository, EventRepository $eventRepository, TicketRepository $ticketRepository)
    // {
    //     $this->em = $em;
    //     $this->groupRepository = $groupRepository;
    //     $this->eventRepository = $eventRepository;
    //     $this->ticketRepository = $ticketRepository;
    // }

    
    #[Route('/group/delete/{id}', name: 'group_delete')]
    public function delete($id, GroupRepository $groupRepository, EntityManagerInterface $em): Response
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

        $em->remove($group);
        $em->flush();

        return $this->redirectToRoute('event_show', ['adminToken' => $adminToken]);
    }

    #[Route('/group/help/{id}', name: 'group_help')]
    public function help($id ,GroupRepository $groupRepository, EntityManagerInterface $em): Response
    {
        $group = $groupRepository->find($id);
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
    public function show($linkTokenParam, GroupRepository $groupRepository, TicketRepository $ticketRepository, EntityManagerInterface $em, Request $request): Response
    {
        //remove special chars breaking the search
        $linkTokenParam = preg_replace('/(\v|\s)+/', ' ', $linkTokenParam);
        $group = $groupRepository->findOneBy(['linkToken' => $linkTokenParam]);

        if(!$group) {
            throw $this->createNotFoundException("Le groupe n'existe pas.");
        }

        $ticketsGroup = $ticketRepository->findBY(['group_ticket_id' => $group->getId()]);

        if($linkTokenParam !== $group->getLinkToken()){
            throw $this->createNotFoundException("Le groupe n'existe pas.");
        }

        $ticket = new Ticket();
        $ticket->setGroupTicketId($group);
        $formTicket= $this->createForm(TicketType::class, $ticket);
        $formTicket->handleRequest($request);
        
        if($formTicket->isSubmitted() && $formTicket->isValid()) {
            $em->persist($ticket);
            $em->flush();
            return $this->redirectToRoute('group_show', [
                "linkTokenParam" => $linkTokenParam
            ]);            
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
