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
        $ticket = $this->entityManager->getRepository(Ticket::class)->find($ticketId);

        if (!$ticket) {
            throw $this->createNotFoundException('No ticket found for id ' . $ticketId);
        }

        Stripe::setApiKey('sk_test_51PY4LwDzMg6ykNYFOM27Sh0Y1KylOwPYqlSiOIf0az66NgCq00E1l2npAApETaO4K4ZNZYaAq3zdaxWqiXZkCpy500JPL5qTMv');

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

        // Update the ticket status to 'paid'
        $ticket->setStatus('paid');
        $this->entityManager->flush();

        return $this->redirectToRoute('app_tickets');
    }

    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        $this->addFlash('error', 'Payment was cancelled.');
        return $this->redirectToRoute('app_tickets');
    }

    private function calculateAmount(Ticket $ticket): int
    {
        // Calculate the amount based on your pricing logic
        return 1000; // For example, 10 USD
    }
}

