<?php

// src/Controller/StripeController.php

namespace App\Controller;

use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/create-checkout-session/{ticketId}', name: 'create_checkout_session')]
    public function createCheckoutSession($ticketId)
    {
        // Récupérer le ticket à partir de l'ID
        // Retrieve the ticket from the ID
        $ticket = $this->entityManager->getRepository(Ticket::class)->find($ticketId);

        if (!$ticket) {
            throw $this->createNotFoundException('No ticket found for id ' . $ticketId);
        }

        // Configurer la clé API Stripe
        // Set up the Stripe API key
        Stripe::setApiKey('sk_test_51PY4LwDzMg6ykNYFOM27Sh0Y1KylOwPYqlSiOIf0az66NgCq00E1l2npAApETaO4K4ZNZYaAq3zdaxWqiXZkCpy500JPL5qTMv');

        // Créer une session de paiement Stripe
        // Create a Stripe checkout session
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Ticket #' . $ticket->getId(),
                    ],
                    'unit_amount' => $this->calculateAmount($ticket),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('payment_success', ['ticketId' => $ticket->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        // Rediriger vers l'URL de paiement Stripe
        // Redirect to the Stripe payment URL
        return $this->redirect($session->url, 303);
    }

    #[Route('/payment/success', name: 'payment_success')]
    public function paymentSuccess(Request $request): Response
    {
        $ticketId = $request->get('ticketId');
        $ticket = $this->entityManager->getRepository(Ticket::class)->find($ticketId);

        if (!$ticket) {
            throw $this->createNotFoundException('No ticket found for id ' . $ticketId);
        }

        // Mettre à jour le statut du ticket à 'payé'
        // Update the ticket status to 'paid'
        $ticket->setStatus('paid');
        $this->entityManager->flush();

        return $this->redirectToRoute('app_tickets');
    }

    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        // Ajouter un message flash pour indiquer l'annulation du paiement
        // Add a flash message to indicate payment cancellation
        $this->addFlash('error', 'Payment was cancelled.');
        return $this->redirectToRoute('app_tickets');
    }

    private function calculateAmount(Ticket $ticket): int
    {
        // Calculer le montant en fonction de votre logique de tarification
        // Calculate the amount based on your pricing logic
        return 1000; // Par exemple, 10 USD / For example, 10 USD
    }
}