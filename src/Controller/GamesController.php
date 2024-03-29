<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Sport;
use App\Entity\User;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

class GamesController extends AbstractController
{

    #[Route('/api/games_user', methods: ['POST'])]
    public function getGamesByUser(Request $request, GameRepository $gameRepository):JsonResponse{
        $userid = $request->get('userid');
        $sportid = $request->get('sportid');
        $response = $gameRepository->getGamesByUser($userid, $sportid);

        if(!empty($response)){
            return new JsonResponse($response, 200);

        }else{
            return new JsonResponse(['success' => false], 202);
        }
    }

    #[Route('/api/create_game', methods: ['POST'])]
    public function createNewGame(Request $request, EntityManagerInterface $entityManager):JsonResponse{
        $userid = $entityManager->getRepository(User::class)->find($request->get('userid'));
        $sportid = $entityManager->getRepository(Sport::class)->find($request->get('sportid'));

        $game = new Game();
        $game->setSport($sportid);
        $game->setUser($userid);
        $game->setPlayerOne($request->get('player_one'));
        $game->setPlayerTwo($request->get('player_two'));
        $game->setIndividual($request->get('individual'));
        $game->setSaque(1);
        $game->setCreatedAt(date_create_immutable());
        $entityManager->persist($game);
        $entityManager->flush();

        return new JsonResponse(['id' => $game->getId()], 200);
    }

    #[Route('/api/reset_game', methods: ['POST'])]
    public function resetGame(Request $request, EntityManagerInterface $entityManager):JsonResponse{
        $game = $entityManager->getRepository(Game::class)->find($request->get('gameid'));

        $game->setSaque(1);
        $game->setP1ps(null);
        $game->setP11s(null);
        $game->setP12s(null);
        $game->setP13s(null);
        $game->setP2ps(null);
        $game->setP21s(null);
        $game->setP22s(null);
        $game->setP23s(null);
        $entityManager->persist($game);
        $entityManager->flush();

        return new JsonResponse(['success' => true], 200);
    }
}