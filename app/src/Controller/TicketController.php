<?php

// src/Controller/TicketController.php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\Stock;
use App\Entity\InterventionStock;
use App\Entity\Facturation;
use App\Form\TicketType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class TicketController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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

        // Fetch available stock
        $stocks = $this->entityManager->getRepository(Stock::class)->findAll();

        return $this->render('ticket/edit.html.twig', [
            'form' => $form->createView(),
            'ticket' => $ticket,
            'stocks' => $stocks,
        ]);
    }

    #[Route('/ticket/use_stock/{ticketId}/{stockId}', name: 'ticket_use_stock')]
    public function useStock(int $ticketId, int $stockId, Request $request): JsonResponse
    {
        $ticket = $this->entityManager->getRepository(Ticket::class)->find($ticketId);
        $stock = $this->entityManager->getRepository(Stock::class)->find($stockId);

        if ($request->isMethod('POST')) {
            $quantityUsed = (int)$request->request->get('quantity');
            $description = $request->request->get('description');

            if ($quantityUsed > 0 && $quantityUsed <= $stock->getQuantity()) {
                $stock->setQuantity($stock->getQuantity() - $quantityUsed);

                $interventionStock = new InterventionStock();
                $interventionStock->setIntervention($ticket->getIntervention());
                $interventionStock->setStock($stock);
                $interventionStock->setQuantityUsed($quantityUsed);
                $interventionStock->setDescription($description);
                $interventionStock->setUsedAt(new \DateTime());

                $this->entityManager->persist($interventionStock);
                $this->entityManager->flush();

                return new JsonResponse(['status' => 'success']);
            } else {
                return new JsonResponse(['status' => 'error', 'message' => 'Invalid quantity.']);
            }
        }

        return new JsonResponse(['status' => 'error', 'message' => 'Invalid request.']);
    }


    #[Route('/ticket/in_progress/{id}', name: 'ticket_in_progress')]
    public function in_progress(Ticket $ticket): Response
    {
        $ticket->setStatus('en cours');

        $this->entityManager->flush();

        $this->addFlash('success', 'Ticket in progress.');
        return $this->redirectToRoute('app_ticket');
    }



    #[Route('/ticket/close/{id}', name: 'ticket_close')]
    public function close(Ticket $ticket, Request $request): Response
    {
        $ticket->setStatus('resolus');
        $ticket->setDateEnd(new \DateTime());

        $finalReport = $request->request->get('finalReport');
        $interventionStocks = $ticket->getIntervention()->getInterventionStocks();

        $facturation = new Facturation();
        $facturationData = [
            'ticket_id' => $ticket->getId(),
            'technician_id' => $ticket->getTechnicien()->getId(),
            'creation_date' => $ticket->getDateStart()->format('d/m/Y H:i'),
            'closing_date' => $ticket->getDateEnd()->format('d/m/Y H:i'),
            'stocks_used' => []
        ];

        foreach ($interventionStocks as $interventionStock) {
            $facturationData['stocks_used'][] = [
                'stock_label' => $interventionStock->getStock()->getLabel(),
                'quantity_used' => $interventionStock->getQuantityUsed(),
                'description' => $interventionStock->getDescription(),
                'used_at' => $interventionStock->getUsedAt()->format('d/m/Y H:i')
            ];
        }

        $facturation->setValue(json_encode($facturationData));
        $facturation->setDescription($finalReport);

        $this->entityManager->persist($facturation);
        $this->entityManager->flush();

        $this->addFlash('success', 'Ticket closed successfully.');

        return $this->redirectToRoute('app_ticket');
    }

    #[Route('/ticket/delete/{id}', name: 'ticket_delete')]
    public function delete(Ticket $ticket): Response
    {
        $this->entityManager->remove($ticket);
        $this->entityManager->flush();

        $this->addFlash('success', 'Ticket deleted successfully.');
        return $this->redirectToRoute('app_dashboard');
    }
}











