<?php
/**
 * Report Controller.
 */

namespace App\Controller;

use App\Entity\Report;
use App\Repository\ReportRepository;
use App\Service\CommentServiceInterface;
use App\Service\ReportServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class ReportController.
 */
#[Route('/report')]
class ReportController extends AbstractController
{
    /**
     * Constructor.
     */
    public function __construct(private readonly ReportServiceInterface $reportService, private readonly CommentServiceInterface $commentService)
    {
    }

    /**
     * Index action.
     *
     * @param ReportRepository   $reportRepository Report repository
     * @param PaginatorInterface $paginator        Paginator
     * @param int                $page             Page
     *
     * @return Response HTTP Response
     */
    #[Route(name: 'report_index', methods: 'GET')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->reportService->getPaginatedList($page);

        return $this->render('report/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Report $report Report entity
     *
     * @return Response HTTP Response
     */
    #[Route('/{id}', name: 'report_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    public function show(Report $report = null, #[MapQueryParameter] int $page = 1): Response
    {
        $comments = $this->commentService->getPaginatedList($report, $page);

        return $this->render('report/show.html.twig', ['report' => $report, 'comments' => $comments]);
    }
}
