<?php

namespace App\Controller;

use App\Service\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class GameController extends AbstractController
{
    private $gameService;
    private $security;

    public function __construct(GameService $gameService, Security $security)
    {
        $this->gameService = $gameService;
        $this->security = $security;
    }

    #[Route('/api/play', name: 'play_game', methods: ['GET'])]
    public function play(): JsonResponse
    {
        try {
            $result = $this->gameService->playGame();
            return new JsonResponse(['status' => 'success', 'data' => $result]);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    #[Route('/api/status', name: 'game_status', methods: ['GET'])]
    public function status(): JsonResponse
    {
        $user = $this->security->getUser();

        try {
            $played = $this->gameService->hasUserPlayedToday($user);
            $response = ['played' => $played];

            if ($played) {
                $userPrize = $this->gameService->getUserPrize($user);
                $response['prize'] = $userPrize->getPrizeDistribution()->getPrize()->getName();
            }

            return new JsonResponse(['status' => 'success', 'data' => $response]);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
