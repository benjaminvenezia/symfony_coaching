<?php

namespace App\Services;

use App\Repository\TicketRepository;

class EventService {

    private $ticketRepository;

    public function __construct(TicketRepository $ticketRepository) {
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Return number of ticket by Event.
     * @param Group[] $groups All groups of the event.
     * @return integer
     */
    public function getNbTicketsForThisEvent($groups): int
    {
        /**
         * @var int $counter;
         */
        $counter = 0;

        foreach ($groups as $g){
                $n = count($this->ticketRepository->findBy(['group_ticket_id' => $g->getId()]));
                $counter += $n;
            }

        return  $counter;
    }
}
