<?php
/**
 * Report Controller.
 */

namespace App\Controller;

use App\Repository\ReportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 *Class ReportController.
 */
#[Route('/report')]
class ReportController extends AbstractController
{
    /**
     * Index action.
     *
     * @param ReportRepository $repository Report repository
     *
     * @return Response HTTP Response
     */
    #[Route(name: 'report_index', methods: 'GET')]
    public function index(ReportRepository $repository): Response
    {
        $reports = $repository->findAll();

        return $this->render('report/index.html.twig', ['reports' => $reports]);
    }

    /**
     * Show action.
     *
     * @param ReportRepository $repository Report repository
     * @param int              $id         Id
     *
     * @return Response HTTP Response
     */
    #[Route('/{id}', name: 'report_show', requirements: ['id' => '[1-9][0-9]*'], methods: 'GET')]
    public function show(ReportRepository $repository, int $id): Response
    {
        $report = $repository->findOneById($id);

        return $this->render('report/show.html.twig', ['report' => $report, 'id' => $id]);
    }
}
