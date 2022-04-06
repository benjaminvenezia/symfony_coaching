<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\CreateEventType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NavigationController extends AbstractController
{
  


   

    #[Route('/', name: 'homepage')]
    public function index(Request $request): Response
    {
        $event = new Event();
    
        $form = $this->createForm(CreateEventType::class, $event);
    
        $form->handleRequest($request);
        $formView = $form->createView();

        return $this->render('navigation/home.html.twig', [
            'formView' => $formView
        ]);
    }
}
