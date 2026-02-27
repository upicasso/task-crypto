<?php

namespace App\Controller\Api;

use App\Repository\PortfolioValueRepository;
use App\Service\PortfolioValuationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class PortfolioController extends AbstractController
{
    public function __construct(
        private readonly PortfolioValuationService $portfolioValuationService,
    ) {
    }

    #[Route('/portfolio/history', name: 'app_portfolio')]
    public function index(Request $request): JsonResponse
    {
        $hours = $request->query->get('hours');
        $fromParam = $request->query->get('from');
        $toParam = $request->query->get('to');

        if ($fromParam && $toParam) {
            $from = new \DateTimeImmutable($fromParam);
            $to = new \DateTimeImmutable($toParam);

            $responseData = $this->portfolioValuationService->getPortfolioHistoryByDateRange($from, $to);
        } elseif ($hours) {
            $responseData = $this->portfolioValuationService->getPortfolioHistoryByHours($hours);
        } else {
            $responseData = $this->portfolioValuationService->getPortfolioHistory();
        }



        return $this->json($responseData);
    }
}
