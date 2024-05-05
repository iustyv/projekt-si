<?php
/**
 * Report Controller.
 */

namespace App\Controller;

use App\Entity\Report;
use App\Repository\ReportRepository;
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
     * Index action.
     *
     * @param ReportRepository   $reportRepository Report repository
     * @param PaginatorInterface $paginator        Paginator
     * @param int                $page             Page
     *
     * @return Response HTTP Response
     */
    #[Route(name: 'report_index', methods: 'GET')]
    public function index(ReportRepository $reportRepository, PaginatorInterface $paginator, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $paginator->paginate(
            $reportRepository->queryAll(),
            $page,
            ReportRepository::PAGINATOR_ITEMS_PER_PAGE
        );

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
    public function show(Report $report): Response
    {
        return $this->render('report/show.html.twig', ['report' => $report]);
    }
}
