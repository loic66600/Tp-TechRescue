<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Stock;
use App\Entity\Ticket;
use App\Form\UserType;
use App\Form\LoginType;
use App\Form\TicketType;
use App\Entity\Facturation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;
 

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
       
    }

    #[Route('/connexion', name: 'app_connexion')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $formData */
            $formData = $form->getData();
            $email = $formData->getEmail();
            $client = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$client) {
                $this->addFlash('error', 'Email non trouvé.');
                return $this->redirectToRoute('app_connexion');
            }

            if ($passwordHasher->isPasswordValid($client, $formData->getPassword())) {
                return $this->redirectToRoute('app_dashboard');
            } else {
                $this->addFlash('error', 'Mot de passe incorrect.');
                return $this->redirectToRoute('app_connexion');
            }
        }

        return $this->render('home/connexion/connexion.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
     


    #[Route('/inscription', name: 'app_inscription')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the plain password before storing
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $message = 'Registration successful. Please check your email for confirmation.';
            $this->addFlash('success', $message);

            // Redirect to login page after successful registration
            return $this->redirectToRoute('app_login');
        }

        return $this->render('home/connexion/inscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mytickets', name: 'app_tickets')]
    public function tickets(Request $request): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setUser($this->getUser());


            $ticket->setStatus('ouvert'); // Set the default status

            $this->entityManager->persist($ticket);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_tickets');
        }

        $tickets = $this->entityManager->getRepository(Ticket::class)->findBy([
            'user' => $this->getUser(),

            'status' => 'ouvert'
        ]);
      
        $tickets_progress = $this->entityManager->getRepository(Ticket::class)->findBy([
            'user' => $this->getUser(),
            'status' => 'en cours'

        ]);
        $closedTickets = $this->entityManager->getRepository(Ticket::class)->findBy([
            'user' => $this->getUser(),
            'status' => 'resolus'
        ]);

        return $this->render('user/ticket.html.twig', [
            'tickets' => $tickets,
            'tickets_progress' => $tickets_progress,
            'closed_tickets' => $closedTickets,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/technicien', name: 'app_ticket')]
    public function technicien(): Response
    {
        if (!$this->isGranted('ROLE_TECHNICIEN')) {
            throw new AccessDeniedException('Access Denied. You do not have permission to access this page.');
        }

        $technicien = $this->getUser();
        $tickets = $this->entityManager->getRepository(Ticket::class)->findBy([
            'technicien' => $technicien,
            'status' => 'ouvert'
        ]);
        $tickets_progress = $this->entityManager->getRepository(Ticket::class)->findBy([
            'technicien' => $technicien,
            'status' => 'en cours'
        ]);
        $closedTickets = $this->entityManager->getRepository(Ticket::class)->findBy([
            'technicien' => $technicien,
            'status' => 'resolus'
        ]);

        //ici on va chercher les stocks et afficher un messahge d'erreur si le stock est insuffisant
         $stocks = $this->entityManager->getRepository(Stock::class)->findAll();

        return $this->render('user/technicien.html.twig', [
            'tickets' => $tickets,
            'tickets_progress' => $tickets_progress,
            'closed_tickets' => $closedTickets,
            'stocks' => $stocks,
        ]);
    }

    #[Route('/ticket/edit/{id}', name: 'ticket_edit')]
    public function edit(Ticket $ticket, Request $request): Response
    {
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('app_ticket');
        }

        return $this->render('ticket/edit.html.twig', [
            'form' => $form->createView(),
            'ticket' => $ticket,
        ]);
    }

    #[Route('/ticket/close/{id}', name: 'ticket_close')]
    public function close(Ticket $ticket): Response
    {
        $ticket->setStatus('resolus');
        $ticket->setDateEnd(new \DateTime());

        $description = $this->generateFinalReport($ticket);

        $facturation = new Facturation();
        $facturation->setValue($description);

        $this->entityManager->persist($facturation);
        $this->entityManager->flush();

        $this->addFlash('success', 'Ticket closed successfully.');
        return $this->redirectToRoute('app_ticket');
    }

    private function generateFinalReport(Ticket $ticket): string
    {
        $technicianId = $ticket->getTechnicien() ? $ticket->getTechnicien()->getId() : 'N/A';
        $clientId = $ticket->getUser() ? $ticket->getUser()->getId() : 'N/A';
        $creationDate = $ticket->getDateStart() ? $ticket->getDateStart()->format('d/m/Y H:i') : 'N/A';
        $closingDate = $ticket->getDateEnd() ? $ticket->getDateEnd()->format('d/m/Y H:i') : 'N/A';

        $stockUsages = $ticket->getIntervention()->getInterventionStocks();
        $stockDetails = '';

        foreach ($stockUsages as $stockUsage) {
            $stockDetails .= sprintf(
                "Date d'utilisation: %s, Stock: %s, Quantité Utilisée: %d, Description: %s\n",
                $stockUsage->getUsedAt()->format('d/m/Y H:i'),
                $stockUsage->getStock()->getLabel(),
                $stockUsage->getQuantityUsed(),
                $stockUsage->getDescription()
            );
        }

        return sprintf(
            "Ticket ID: %d\nTechnician ID: %s\nClient ID: %s\nDate de Création: %s\nDate de Clôture: %s\n\nStock Utilisé:\n%s",
            $ticket->getId(),
            $technicianId,
            $clientId,
            $creationDate,
            $closingDate,
            $stockDetails
        );
    }

    #[Route('/adduser', name: 'app_adduser', methods: ['POST'])]
    public function addUser(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $roles = [$request->request->get('roles')];

        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setPassword($passwordHasher->hashPassword($user, $password));
        $user->setActive(true);

        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('admin_dashboard'); // Adjust the route name accordingly
    }
}




























