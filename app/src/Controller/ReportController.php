<?php
/**
 * Report Controller.
 */

namespace App\Controller;

use App\Entity\Report;
use App\Form\Type\ReportType;
use App\Service\CommentServiceInterface;
use App\Service\ReportServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ReportController.
 */
#[Route('/report')]
class ReportController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param ReportServiceInterface  $reportService  Report service interface
     * @param CommentServiceInterface $commentService Comment service interface
     * @param TranslatorInterface $translator Translator interface
     */
    public function __construct(private readonly ReportServiceInterface $reportService, private readonly CommentServiceInterface $commentService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param int $page Page
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
     * @param int    $page   Page number
     *
     * @return Response HTTP Response
     */
    #[Route('/{id}', name: 'report_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    public function show(?Report $report = null, #[MapQueryParameter] int $page = 1): Response
    {
        $comments = $this->commentService->getPaginatedList($report, $page);

        return $this->render('report/show.html.twig', ['report' => $report, 'comments' => $comments]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'report_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->reportService->save($report);

            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('report_index');
        }

        return $this->render('report/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit action.
     *
     * @param Request  $request  HTTP request
     * @param Report $report Report entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'report_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function edit(Request $request, Report $report): Response
    {
        $form = $this->createForm(
            ReportType::class,
            $report,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('report_edit', ['id' => $report->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->reportService->save($report);

            $this->addFlash('success', $this->translator->trans('message.edited_successfully'));

            return $this->redirectToRoute('report_index');
        }

        return $this->render('report/edit.html.twig', ['form' => $form->createView(), 'report' => $report]);
    }
}
