<?php

namespace App\Services;

use App\Repository\TicketRepository;

class EventService {

    private $ticketRepository;

    public function __construct(TicketRepository $ticketRepository) {
        $this->ticketRepository = $ticketRepository;
    }

    public function getNbTicketsForThisEvent($groups): int
    {
        $counter = 0;

        foreach ($groups as $g){
                $n = count($this->ticketRepository->findBy(['group_ticket_id' => $g->getId()]));
                $counter += $n;
            }

        return  $counter;
    }
}
