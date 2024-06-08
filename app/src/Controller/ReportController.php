<?php
/**
 * Report Controller.
 */

namespace App\Controller;

use App\Dto\ReportListInputFiltersDto;
use App\Entity\Comment;
use App\Entity\Enum\ReportStatus;
use App\Form\Type\CommentType;
use App\Resolver\ReportListInputFiltersDtoResolver;
use App\Entity\Report;
use App\Entity\User;
use App\Form\Type\ReportType;
use App\Service\AttachmentService;
use App\Service\AttachmentServiceInterface;
use App\Service\CommentServiceInterface;
use App\Service\ProjectServiceInterface;
use App\Service\ReportServiceInterface;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
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
    public function __construct(private readonly ReportServiceInterface $reportService, private readonly CommentServiceInterface $commentService, private readonly AttachmentServiceInterface $attachmentService, private readonly TranslatorInterface $translator, private readonly ProjectServiceInterface $projectService)
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
    public function index(#[MapQueryString(resolver: ReportListInputFiltersDtoResolver::class)] ReportListInputFiltersDto $filters, #[MapQueryParameter] int $page = 1): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $pagination = $this->reportService->getPaginatedList(
            $filters,
            //$user,
            $page
        );

        return $this->render('report/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show archived reports.
     *
     * @param int $page Page
     *
     * @return Response HTTP Response
     */
    #[Route('/archived', name: 'report_archived', methods: 'GET')]
    public function show_archived(#[MapQueryString(resolver: ReportListInputFiltersDtoResolver::class)] ReportListInputFiltersDto $filters, #[MapQueryParameter] int $page = 1): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $pagination = $this->reportService->getPaginatedListOfArchived(
            $filters,
            //$user,
            $page
        );

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
        if(!$this->isGranted('CREATE_REPORT')) {
            return $this->redirectToRoute('index');
        }

        $report = new Report();
        $user = $this->getUser();
        $report->setAuthor($user);

        $projects = $this->projectService->getUserProjects($user);

        $form = $this->createForm(
            ReportType::class,
            $report,
            [
                'projects' => $projects,
                'action' => $this->generateUrl('report_create')
            ],
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            if (null !== $file)
            {
                $report->setAttachment($this->attachmentService->create($file, $report));
            }
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
        if(!$this->isGranted('EDIT_REPORT', $report)) {
            return $this->redirectToRoute('report_show', ['id' => $report->getId()]);
        }

        $projects = $this->projectService->getUserProjects($report->getAuthor());

        $form = $this->createForm(
            ReportType::class,
            $report,
            [
                'projects' => $projects,
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

    /**
     * Delete action.
     *
     * @param Request  $request  HTTP request
     * @param Report $report Report entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'report_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Report $report): Response
    {
        if(!$this->isGranted('DELETE_REPORT', $report)) {
            return $this->redirectToRoute('report_show', ['id' => $report->getId()]);
        }

        $form = $this->createForm(ReportType::class, $report, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('report_delete', ['id' => $report->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->reportService->delete($report);

            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));

            return $this->redirectToRoute('report_index');
        }

        return $this->render('report/delete.html.twig', ['form' => $form->createView(), 'report' => $report,]);
    }

    #[Route('/{id}/toggle_archive', name: 'report_toggle_archive', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function toggle_archive(Request $request, Report $report): Response
    {
        if(!$this->isGranted('TOGGLE_ARCHIVE', $report)) {
            return $this->redirectToRoute('report_show', ['id' => $report->getId()]);
        }

        $this->reportService->toggle_archive($report);

        if ($report->getStatus() === ReportStatus::STATUS_ARCHIVED) {
            $this->addFlash('success', $this->translator->trans('message.archived_successfully'));
        }
        else {
            $this->addFlash('success', $this->translator->trans('message.unarchived_successfully'));
        }

        return $this->redirectToRoute('report_show', ['id' => $report->getId()]);
    }

    #[Route('/{id}/comment', name: 'report_comment', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    public function comment(Request $request, Report $report): Response
    {
        if (!$this->isGranted('COMMENT', $report)) {
            return $this->redirectToRoute('report_show', ['id' => $report->getId()]);
        }

        $comment = new Comment();
        $user = $this->getUser();
        $comment->setAuthor($user);
        $comment->setReport($report);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->save($comment);

            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('report_show', ['id' => $report->getId()]);
        }

        return $this->render('report/show.html.twig',
            [
                'form' => $form->createView(),
                'report' => $report,
                'comments'=> $this->commentService->getPaginatedList($report)
            ]);
    }
}
