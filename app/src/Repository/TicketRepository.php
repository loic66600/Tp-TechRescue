<?php

namespace App\Repository;

use App\Entity\Ticket;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{

    private $userRepository;
    public function __construct(ManagerRegistry $registry, UserRepository $userRepository)
    {
        parent::__construct($registry, Ticket::class);
        $this->userRepository = $userRepository;
    }



    public function countResolvedToday(): int
    {
        $qb = $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->where('t.status = :status')
            ->andWhere('t.dateEnd >= :start')
            ->andWhere('t.dateEnd <= :end')
            ->setParameter('status', 'resolved')
            ->setParameter('start', new \DateTime('today midnight'))
            ->setParameter('end', new \DateTime('tomorrow midnight'))
            ->getQuery();

        return (int) $qb->getSingleScalarResult();
    }

    public function calculateAverageResolutionTime(): float
    {
        $qb = $this->createQueryBuilder('t')
            ->select('AVG(t.dateEnd - t.dateStart) as avg_time')
            ->where('t.status = :status')
            ->setParameter('status', 'resolved')
            ->getQuery();


        return (float) $qb->getSingleScalarResult();
    }

    public function findRecentTickets(int $limit = 5): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.dateEnd', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findInfos()
    {
        $q = $this->createQueryBuilder('t')
            ->select('t, u, tech, uci, tci')
            ->leftJoin('t.user', 'u')
            ->leftJoin('t.technicien', 'tech')
            ->leftJoin('u.contactInformation', 'uci')
            ->leftJoin('tech.contactInformation', 'tci')
            ->getQuery()
            ->getResult();

        foreach ($q as $ticket) {
            // Ensure the user, technicien, and their contactInformation are fully loaded
            if ($ticket->getUser()) {
                $ticket->getUser()->getId();
                if ($ticket->getUser()->getContactInformation()) {
                    $ticket->getUser()->getContactInformation()->getId();
                }
            }
            if ($ticket->getTechnicien()) {
                $ticket->getTechnicien()->getId();
                if ($ticket->getTechnicien()->getContactInformation()) {
                    $ticket->getTechnicien()->getContactInformation()->getId();
                }
            }
        }
        //dd($q);
        return $q;
    }
}
