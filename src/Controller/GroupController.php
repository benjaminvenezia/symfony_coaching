<?php

namespace App\Controller;

use DateTime;
use App\Entity\Group;
use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\EventRepository;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GroupController extends AbstractController
{
    protected $em;
    protected $groupRepository;
    protected $eventRepository;

    public function __construct(EntityManagerInterface $em, GroupRepository $groupRepository, EventRepository $eventRepository)
    {
        $this->em = $em;
        $this->groupRepository = $groupRepository;
        $this->eventRepository = $eventRepository;
    }

    
    #[Route('/group/delete/{id}', name: 'group_delete')]
    public function delete($id): Response
    {
        $group = $this->groupRepository->find($id);
        $adminToken = $group->getLinkToken();

        if(!$group) {
            throw $this->createNotFoundException("Le groupe n'existe pas et ne peut pas être supprimé");
        }

        $this->em->remove($group);
        $this->em->flush();

        return $this->redirectToRoute('event_show', ['adminToken' => $adminToken]);
    }

    #[Route('/group/help/{id}', name: 'group_help')]
    public function help($id): Response
    {
        $group = $this->groupRepository->find($id);
        $adminToken = $group->getLinkToken();

        if(!$group) {
            throw $this->createNotFoundException("Le groupe n'existe pas et ne peut pas être supprimé");
        }
        
        $group->incrementHelpedCounter();
        $this->em->persist($group);
        $this->em->flush();

        return $this->redirectToRoute('event_show', ['adminToken' => $adminToken]);
    }

    #[Route('/group/{linkTokenParam}/{id}', name: 'group_show')]
    public function show($id, $linkTokenParam, Request $request): Response
    {
        $group = $this->groupRepository->find($id);
       
        if(!$group) {
            throw $this->createNotFoundException("Le groupe n'existe pas.");
        }

        if($linkTokenParam !== $group->getLinkToken()){
            throw $this->createNotFoundException("Le groupe n'existe pas.");
        }
           
        $ticket = new Ticket();
        $ticket->setGroupTicketId($group);
        $formTicket= $this->createForm(TicketType::class, $ticket);
        $formTicket->handleRequest($request);
        
        if($formTicket->isSubmitted() && $formTicket->isValid()) {
            $this->em->persist($ticket);
            $this->em->flush();
            return $this->redirectToRoute('group_show', [
                "id" => $id,
                "linkTokenParam" => $linkTokenParam
            ]);            
        }

        $form = $formTicket->createView();

        return $this->render('navigation/grouppage.html.twig', [
            "linktoken" => $linkTokenParam,
            "formView" => $form,
            "id" => $id,
            "group" => $group
        ]);   
    }
}
