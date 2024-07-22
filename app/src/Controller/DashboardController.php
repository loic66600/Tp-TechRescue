<?php

namespace App\Controller;

use App\Entity\ContactInformation;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        // Fetch data from the database
        $openTickets = $this->entityManager->getRepository(Ticket::class)->count(['status' => 'ouvert']);
        $inProgressTickets = $this->entityManager->getRepository(Ticket::class)->count(['status' => 'en cours']);
        $resolvedToday = $this->entityManager->getRepository(Ticket::class)->count(['status' => 'resolus']);
        $avgResolutionTime = $this->entityManager->getRepository(Ticket::class)->calculateAverageResolutionTime();
        $recentTickets = $this->entityManager->getRepository(Ticket::class)->findRecentTickets();
 

        // Fetch all tickets
        $tickets = $this->entityManager->getRepository(Ticket::class)->findinfos();

        // Fetch technicians
        $technicians = $this->entityManager->getRepository(User::class)->findByRole('ROLE_TECHNICIEN');
      
 
        // Combine data into an array
        $data = [
            'openTickets' => $openTickets,
            'inProgressTickets' => $inProgressTickets,
            'resolvedToday' => $resolvedToday,
            'avgResolutionTime' => $avgResolutionTime,
            'recentTickets' => $recentTickets,
            'tickets' => $tickets,
            'technicians' => $technicians,
           
        ];
        //  render view;
        return $this->render('home/dashboard.html.twig', $data);
    }
}





