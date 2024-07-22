<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Stock;
use App\Entity\Ticket;
use App\Form\AddStockType;
use App\Form\EditPieceType;
use App\Entity\Intervention;
use App\Repository\UserRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InterventionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin', name: 'admin_dashboard')]
    public function index(UserRepository $userRepository, TicketRepository $ticketRepository): Response
    {
        // Retrieve all users and tickets from the database
        $users = $userRepository->findAll();
        $tickets = $ticketRepository->findAll();
        $technicians = $userRepository->findByRole('ROLE_TECHNICIEN');

        $this->denyAccessUnlessGranted('ROLE_ADMIN'); //seul l'utilisateur connecté en tant qu'admin peut accéder à cette page
        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'tickets' => $tickets,
            'technicians' => $technicians,
        ]);
    }

    #[Route('/admin/catalogue', name: 'admin_catalogue')]
    public function catalogue(): Response
    { //ici on affiche le catalogue des pièces
        $stock = $this->entityManager->getRepository(Stock::class)->findAll();

        return $this->render('admin/catalogue.html.twig', [
            'stock' => $stock,
            
        ]);
    }

    #[Route('/admin/history', name: 'admin_history')]
    public function history(): Response
    {
        return $this->render('admin/history.html.twig');
    }

    #[Route('/admin/addpiece', name: 'app_addpiece')]
    public function addpiece(Request $request,ValidatorInterface $validator): Response
    { 
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
            // Implement stock addition logic here
            $stock = new Stock();
            $form = $this->createForm(AddStockType::class, $stock);
            $form->handleRequest($request);

            //ici on vas vérifie que les donénessont correctes
            
            if ($form->isSubmitted() && $form->isValid()) { // Set the default status
                $stock->setActive('True');
                $this->entityManager->persist($stock);
                $this->entityManager->flush();
                
    
                return $this->redirectToRoute('admin_catalogue');
            }

    
        return $this->render('admin/form/addpiece.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $form->createView(),
        ]);


    }

    /**
     * méthode qui va supprimer une pièce
     * @Route("/admin/deletepiece/{id}", name="app_deletepiece")
     * @param Stock $stock
     * @return Response
     */
    #[Route('/admin/deletepiece/{id}', name: 'app_deletepiece')]
    public function deletepiece(Stock $stock): Response
    {
        $this->entityManager->remove($stock);
        $this->entityManager->flush();

        $this->addFlash('success', 'Ticket deleted successfully.');
        return $this->redirectToRoute('admin_catalogue');

    }





    #[Route('/admin/editpiece/{id}', name: 'app_editpiece')]
    public function updateticket($id,Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $piece= $this->entityManager->getRepository(Stock::class)->findOneBy(
            ['id' => $id]
        );
        

        $form = $this->createForm(EditPieceType::class, $piece);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { // Set the default status
            $piece->setActive('True');
            
            $this->entityManager->persist($piece);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_catalogue');
        }

    
        return $this->render('admin/form/editpiece.html.twig', [
            'controller_name' => 'HomeController',
            'piece' => $piece,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/assign-ticket/{id}', name: 'admin_assign_ticket', methods: ['POST'])]
    public function assignTicket(Request $request, $id, TicketRepository $ticketRepository, UserRepository $userRepository, InterventionRepository $interventionRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $ticket = $ticketRepository->find($id);
        if (!$ticket) {
            throw $this->createNotFoundException('Ticket not found');
        }

        $technicianId = $request->request->get('technician_id');
        if ($technicianId) {
            $technician = $userRepository->find($technicianId);
            if ($technician) {
                $ticket->setTechnicien($technician);

                // Create or update the intervention
                $intervention = $ticket->getIntervention();
                if (!$intervention) {
                    $intervention = new Intervention();
                    $ticket->setIntervention($intervention);
                }
                $intervention->setLabel('Intervention for ticket ' . $ticket->getId());

                $this->entityManager->persist($intervention);
                $this->entityManager->flush();
            }
        }

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/admin/deleteUser/{id}', name: 'app_deleteuser')]
    public function deleteUser(User $user): Response
    {
        try {
            $this->entityManager->beginTransaction();
 
            if (in_array('ROLE_SUPPLIER', $user->getRoles())) {
                $this->handleSupplierDeletion($user);
            } elseif (in_array('ROLE_TECHNICIAN', $user->getRoles())) {
                $this->handleTechnicianDeletion($user);
            }
 
            // Supprimer les informations de contact
            $contactInfo = $user->getContactInformation();
            if ($contactInfo) {
                $this->entityManager->remove($contactInfo);
            }
 
            // Supprimer l'utilisateur
            $this->entityManager->remove($user);
            $this->entityManager->flush();
 
            $this->entityManager->commit();
 
            $this->addFlash('success', 'Utilisateur et ses informations de contact supprimés avec succès.');
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $this->addFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
 
        return $this->redirectToRoute('admin_dashboard');
    }
 
    private function handleSupplierDeletion(User $supplier)
    {
        $stocks = $this->entityManager->getRepository(Stock::class)->findBy(['supplier' => $supplier]);
        foreach ($stocks as $stock) {
            $stock->setSupplier(null);
            $this->entityManager->persist($stock);
        }
        // Si la relation est bidirectionnelle, ajoutez ceci :
    $supplier->getStocks()->clear();
    }
 
    private function handleTechnicianDeletion(User $technician)
    {
        $tickets = $this->entityManager->getRepository(Ticket::class)->findBy(['technician' => $technician]);
        foreach ($tickets as $ticket) {
            $ticket->setTechnician(null);
            $this->entityManager->persist($ticket);
        }

    }
}














