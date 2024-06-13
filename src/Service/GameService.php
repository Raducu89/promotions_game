<?php

namespace App\Service;

use App\Entity\UserPrizes;
use App\Entity\PrizeDistribution;
use App\Repository\PrizesRepository;
use App\Repository\UserPrizesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class GameService
{
    private $prizesRepository;
    private $userPrizesRepository;
    private $entityManager;
    private $security;

    public function __construct(
        PrizesRepository $prizesRepository,
        UserPrizesRepository $userPrizesRepository,
        EntityManagerInterface $entityManager,
        Security $security
    ) {
        $this->prizesRepository = $prizesRepository;
        $this->userPrizesRepository = $userPrizesRepository;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function playGame(): array
    {
        $user = $this->security->getUser();
        $language = $user->getLanguage();

        if ($this->hasUserPlayedToday($user)) {
            throw new \Exception('You have already played today');
        }

        $currentTime = new \DateTime();
        $start = (new \DateTime())->setTime(9, 0, 0);
        $end = (new \DateTime())->setTime(20, 0, 0);

        if ($currentTime < $start || $currentTime > $end) {
            //throw new \Exception('Playing is not allowed during this time');
        }


        $this->entityManager->beginTransaction();

        try {
            // Check how many prizes have been distributed today
            $today = $currentTime->format('Y-m-d');
            $distributedToday = $this->userPrizesRepository->countDistributedPrizes(new \DateTime($today));

            // Total number of prizes available
            $totalPrizes = $this->prizesRepository->count([]);

            // Calculating how many prizes should be distributed today (half of the total)
            $maxPrizesToday = $totalPrizes / 2;

            if ($distributedToday >= $maxPrizesToday) {
                throw new \Exception('No more prizes available for today');
            }


            $prize = $this->prizesRepository->findRandomAvailablePrize($language);

            if (!$prize) {
                throw new \Exception('No prizes available');
            }

            $prizeDistribution = new PrizeDistribution();
            $prizeDistribution->setPrize($prize);
            $prizeDistribution->setDate(new \DateTime());
            $prizeDistribution->setDistributed(true);

            $userPrize = new UserPrizes();
            $userPrize->setUser($user);
            $userPrize->setPrizeDistribution($prizeDistribution);
            $userPrize->setDatePlayed(new \DateTime());

            $prize->setAvailable(false);

            $this->entityManager->persist($prizeDistribution);
            $this->entityManager->persist($userPrize);
            $this->entityManager->persist($prize);
            $this->entityManager->flush();

            return [
                'prize' => $prize->getName(),
                'partner' => [
                    'name' => $prize->getPartner()->getName(),
                    'code' => $prize->getPartner()->getPartnerCode(),
                    'url' => $prize->getPartner()->getUrl()
                ]
            ];
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    public function hasUserPlayedToday($user): bool
    {
        $today = (new \DateTime())->format('Y-m-d');
        $played = $this->userPrizesRepository->findBy(['user' => $user, 'date_played' => new \DateTime($today)]);

        return !empty($played);
    }

    public function getUserPrize($user): ?UserPrizes
    {
        $today = (new \DateTime())->format('Y-m-d');
        return $this->userPrizesRepository->findOneBy(['user' => $user, 'date_played' => new \DateTime($today)]);
    }
}
