<?php

namespace App\Repository;

use App\Entity\Wallet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wallet>
 *
 * @method Wallet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wallet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wallet[]    findAll()
 * @method Wallet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WalletRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wallet::class);
    }

    public function findOrCreateWallet(string $currency, $user): Wallet
    {
        $wallet = $this->findOneBy([
            'owner' => $user,
            'currency' => $currency,
        ]);

        if (!$wallet) {
            $wallet = new Wallet();
            $wallet->setOwner($user);
            $wallet->setCurrency($currency);
            $this->getEntityManager()->persist($wallet);
            $this->getEntityManager()->flush();
        }

        return $wallet;
    }
}
