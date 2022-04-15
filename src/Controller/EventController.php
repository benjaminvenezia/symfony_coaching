<?php

namespace App\Controller;

use DateTime;
use App\Entity\Group;
use App\Form\CreateGroupType;
use App\Repository\EventRepository;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
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

    #[Route('/event/{adminToken}/groups', name: 'event_show')]
    public function show(Request $request, $adminToken): Response
    {
        //créer un nouveau groupe
        $group = new Group();
    
        $form = $this->createForm(CreateGroupType::class, $group);
    
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            //on set le token de l'événement au groupe
            $group->setLinkToken($adminToken);
            $group->setLastArchived(new DateTime());

            $this->em->persist($group);

            $this->em->flush();

            return $this->redirectToRoute('event_show', [
                "adminToken" => $adminToken,
            ]);
        }

        $formView = $form->createView();
        //return event
        $event = $this->eventRepository->findOneBy([
            'adminLinkToken' => $adminToken
        ]);
        //returns groups
        $groups = $this->groupRepository->findBy([
            'linkToken' => $adminToken, //link token is the same than adminToken in this implementation... 
        ], ['last_helped' => 'ASC']);
        
        return $this->render('navigation/groupspage.html.twig', [
            'formView' => $formView, 
            'groups' => $groups,
            'event' => $event
        ]);
    }
}
