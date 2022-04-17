<?php

namespace App\Controller;

use App\Entity\Event;
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
use App\Services\ClassService;

class EventController extends AbstractController
{

    protected $em;
    protected $eventRepository;

    public function __construct(EntityManagerInterface $em, EventRepository $eventRepository)
    {
        $this->em = $em;
        $this->eventRepository = $eventRepository;
    }

    #[Route('/event/{adminToken}/groups', name: 'event_show')]
    public function show(Request $request, $adminToken, ClassService $classService, GroupRepository $groupRepository): Response
    {
        //créer un nouveau groupe
        $group = new Group();
    
        $form = $this->createForm(CreateGroupType::class, $group);
    
        $form->handleRequest($request);

         //return event
         $event = $this->eventRepository->findOneBy([
            'adminLinkToken' => $adminToken
        ]);

        if($form->isSubmitted() && $form->isValid()) {
            //on genère un nouveau token qu'on associe au groupe
            $grouptoken = $classService->generateToken();
            $group->setLinkToken($grouptoken);
            $group->setLastArchived(new DateTime());
            $group->setEvent($event);

            $this->em->persist($group);
            $this->em->flush();

            return $this->redirectToRoute('event_show', [
                "adminToken" => $adminToken,
            ]);
        }
        $formView = $form->createView();
        //returns groups
        $groups = $groupRepository->findBy([
            'event' => $event->getId(), 
        ]/*, ['last_helped' => 'ASC']*/);
        
        return $this->render('navigation/eventpage.html.twig', [
            'formView' => $formView, 
            'groups' => $groups,
            'event' => $event
        ]);
    }
}
