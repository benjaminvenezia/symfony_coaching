<?php

namespace App\Services;

use App\Repository\StatusRepository;
use App\Repository\TicketRepository;
use Exception;

class TicketService {

    private $ticketRepository;
    private $statusRepository;

    public function __construct(TicketRepository $ticketRepository, StatusRepository $statusRepository) {
        $this->ticketRepository = $ticketRepository;
        $this->statusRepository = $statusRepository;
    }

    /**
     * Update all ticket status when coach change the isArchived Value
     * @return void
     */
    public function updateAllIsArchivedTicketsStatus(): void
    {   
        $allTickets = $this->ticketRepository->findAll();

        if(!$allTickets) {
            throw new Exception("Erreur : Les tickets n'ont pas pu êtres récupérés. ");
        }

        foreach($allTickets as $ticket){
            $status = $this->statusRepository->findOneBy(['ticket_status' => $ticket->getId()]);
            $ticket->setIsArchived($status->getIsArchived());
        }
    }
}
