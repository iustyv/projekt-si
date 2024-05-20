<?php
/**
 * Comment Controller.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Report;
use App\Form\Type\CommentType;
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
 * Class CommentController.
 */

#[Route('/report/{id}/comment', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
class CommentController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param CommentServiceInterface $commentService Comment service interface
     * @param TranslatorInterface $translator Translator interface
     */
    public function __construct(private readonly CommentServiceInterface $commentService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'comment_create', methods: 'GET|POST')]
    public function create(Request $request, Report $report): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->save($comment, $report);

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
