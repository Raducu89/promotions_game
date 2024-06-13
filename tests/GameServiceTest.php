<?php

namespace App\Tests\Service;

use App\Entity\Prizes;
use App\Entity\UserPrizes;
use App\Entity\Users;
use App\Entity\PrizeDistribution;
use App\Entity\Partners;
use App\Repository\PrizesRepository;
use App\Repository\UserPrizesRepository;
use App\Service\GameService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;

class GameServiceTest extends TestCase
{
    private $prizesRepository;
    private $userPrizesRepository;
    private $entityManager;
    private $security;
    private $gameService;

    protected function setUp(): void
    {
        $this->prizesRepository = $this->createMock(PrizesRepository::class);
        $this->userPrizesRepository = $this->createMock(UserPrizesRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->security = $this->createMock(Security::class);

        $this->gameService = new GameService(
            $this->prizesRepository,
            $this->userPrizesRepository,
            $this->entityManager,
            $this->security
        );
    }

    public function testPlayGameSuccess()
    {
        $user = new Users();
        $user->setId(1);
        $user->setLanguage('en');

        $prize = new Prizes();
        $prize->setId(1);
        $prize->setName('Prize 1');
        $prize->setAvailable(true);

        $partner = new Partners();
        $partner->setName('Partner 1');
        $partner->setPartnerCode('P1');
        $partner->setUrl('http://partner1.com');

        $prize->setPartner($partner);

        $this->security->method('getUser')->willReturn($user);

        $this->userPrizesRepository->method('findBy')->willReturn([]);
        $this->prizesRepository->method('findRandomAvailablePrize')->willReturn($prize);
        $this->userPrizesRepository->method('countDistributedPrizes')->willReturn(0);
        $this->prizesRepository->method('count')->willReturn(10);

        $this->entityManager->expects($this->once())->method('beginTransaction');
        $this->entityManager->expects($this->once())->method('commit');
        $this->entityManager->expects($this->never())->method('rollback');

        $result = $this->gameService->playGame();

        $this->assertEquals('Prize 1', $result->getPrizeName());
        $this->assertEquals('Partner 1', $result->getPartnerName());
        $this->assertEquals('P1', $result->getPartnerCode());
        $this->assertEquals('http://partner1.com', $result->getPartnerUrl());
    }

    public function testPlayGameAlreadyPlayedToday()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You have already played today');

        $user = new Users();
        $user->setId(1);
        $user->setLanguage('en');

        $this->security->method('getUser')->willReturn($user);

        $userPrize = new UserPrizes();
        $userPrize->setDatePlayed(new \DateTime());

        $this->userPrizesRepository->method('findBy')->willReturn([$userPrize]);

        $this->gameService->playGame();
    }

    public function testPlayGameNoPrizesAvailableForToday()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No more prizes available for today');

        $user = new Users();
        $user->setId(1);
        $user->setLanguage('en');

        $this->security->method('getUser')->willReturn($user);

        $this->userPrizesRepository->method('findBy')->willReturn([]);
        $this->userPrizesRepository->method('countDistributedPrizes')->willReturn(5);
        $this->prizesRepository->method('count')->willReturn(10); // 10 / 2 = 5 max per day

        $this->gameService->playGame();
    }

    public function testPlayGameNoPrizesAvailable()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No prizes available');

        $user = new Users();
        $user->setId(1);
        $user->setLanguage('en');

        $this->security->method('getUser')->willReturn($user);

        $this->userPrizesRepository->method('findBy')->willReturn([]);
        $this->userPrizesRepository->method('countDistributedPrizes')->willReturn(0);
        $this->prizesRepository->method('count')->willReturn(10);
        $this->prizesRepository->method('findRandomAvailablePrize')->willReturn(null);

        $this->gameService->playGame();
    }

    public function testPlayGameNotAllowedDuringThisTime()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Playing is not allowed during this time');

        $user = new Users();
        $user->setId(1);
        $user->setLanguage('en');

        $this->security->method('getUser')->willReturn($user);

        // Set current time to be outside of allowed playing hours
        $currentTime = (new \DateTime())->setTime(8, 0, 0);
        $this->gameService->checkAllowedTime = function() use ($currentTime) {
            $this->assertTrue($currentTime < (new \DateTime())->setTime(9, 0, 0));
        };

        $this->gameService->playGame();
    }
}
