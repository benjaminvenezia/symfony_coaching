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
use App\Services\EventService;
use Knp\Component\Pager\PaginatorInterface;

class EventController extends AbstractController
{
    #[Route('/event/{adminToken}/groups', name: 'event_show')]
    public function show(
        string $adminToken,
        Request $request, 
        EventService $eventService, 
        ClassService $classService, 
        GroupRepository $groupRepository, 
        EventRepository $eventRepository, 
        EntityManagerInterface $em, 
        PaginatorInterface $paginator
        ): Response
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
         $event = $eventRepository->findOneBy(['adminLinkToken' => $adminToken]);

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
         * @var Group[] $groups The paginated groups
         */
        $groups = $paginator->paginate(
            $groups,
            $request->query->getInt('page', 1),
            4
        );

        /**
         * @var int $counterTicketsForThisEvent number of tickets for all the groups of the event.
         */
        $counterTicketsForThisEvent = $eventService->getNbTicketsForThisEvent($groups);
        
        return $this->render('navigation/eventpage.html.twig', [
            'nbTickets' => $counterTicketsForThisEvent,
            'adminToken' => $adminToken,
            'formView' => $formView, 
            'groups' => $groups,
            'event' => $event
        ]);
    }
}
