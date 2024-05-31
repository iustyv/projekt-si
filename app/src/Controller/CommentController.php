<?php
/**
 * Comment Controller.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Report;
use App\Form\Type\CommentType;
use App\Service\CommentServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CommentController.
 */

//#[Route('/report/{id}/comment', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
#[Route('/comment')]
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
    // FIXME Cannot autowire argument $report of "App\Controller\CommentController::create()": it needs an instance of "App\Entity\Report" but this type has been excluded in "config/services.yaml".
    #[Route('/create/{report_id}', name: 'comment_create', requirements: ['report_id' => '[1-9]\d*'], methods: 'GET|POST')]
    public function create(Request $request, Report $report): Response
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

    /**
     * Edit action.
     *
     * @param Request  $request  HTTP request
     * @param Comment $comment Comment entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'comment_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function edit(Request $request, Comment $comment): Response
    {
        $report = $comment->getReport();

        if (!$this->isGranted('COMMENT_EDIT', $comment)) {
            return $this->redirectToRoute('report_show', ['id' => $report->getId()]);
        }

        $form = $this->createForm(
            CommentType::class,
            $comment,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('comment_edit', ['id' => $comment->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->save($comment);

            $this->addFlash('success', $this->translator->trans('message.edited_successfully'));

            return $this->redirectToRoute('report_show', ['id' => $report->getId()]);
        }

        return $this->render('report/show.html.twig', [
            'form' => $form->createView(),
            'report' => $report,
            'comments'=> $this->commentService->getPaginatedList($report)
            // FIXME optimize query
            // TODO refactor comments (filters??)
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request  $request  HTTP request
     * @param Comment $comment Comment entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'comment_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Comment $comment): Response
    {
        $report = $comment->getReport();

        if(!$this->isGranted('DELETE_COMMENT', $comment))
        {
            return $this->redirectToRoute('report_show', ['id' => $report->getId()]);
        }

        $form = $this->createForm(CommentType::class, $comment, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('comment_delete', ['id' => $comment->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->delete($comment);

            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));

            return $this->redirectToRoute('report_show', ['id' => $report->getId()]);
        }

        return $this->render('report/delete.html.twig', [
            'form' => $form->createView(),
            'report' => $report,
            'comments'=> $this->commentService->getPaginatedList($report)]);
    }

    /**
     * Edit action.
     *
     * @param Request  $request  HTTP request
     * @param Comment $comment Comment entity
     *
     * @return Response HTTP response
     */
}
