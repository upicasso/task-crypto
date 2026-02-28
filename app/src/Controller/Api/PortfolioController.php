<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\PortfolioValuationService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class PortfolioController extends AbstractController
{
    public function __construct(
        private readonly PortfolioValuationService $portfolioValuationService,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route('/portfolio/history', name: 'app_portfolio')]
    public function index(Request $request): JsonResponse
    {
        $hours = $request->query->get('hours');
        $fromParam = $request->query->get('from');
        $toParam = $request->query->get('to');

        try {
            if ($fromParam && $toParam) {
                $from = new \DateTimeImmutable($fromParam);
                $to = new \DateTimeImmutable($toParam);

                $responseData = $this->portfolioValuationService->getPortfolioHistoryByDateRange($from, $to);
            } elseif ($hours) {
                $responseData = $this->portfolioValuationService->getPortfolioHistoryByHours($hours);
            } else {
                $responseData = $this->portfolioValuationService->getPortfolioHistory();
            }
        } catch (\Exception $e) {
            $this->logger->error('Portfolio history request failed', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
            $responseData = ['error' => $e->getMessage()];
        }

        return $this->json($responseData);
    }
}
