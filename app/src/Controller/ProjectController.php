<?php
/**
 * Project controller.
 */

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Form\Type\ProjectType;
use App\Service\ProjectServiceInterface;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ProjectController.
 */
#[Route('/project')]
class ProjectController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param ProjectServiceInterface $projectService Project service interface
     * @param TranslatorInterface     $translator     Translator interface
     * @param UserServiceInterface    $userService    User service interface
     */
    public function __construct(private readonly ProjectServiceInterface $projectService, private readonly TranslatorInterface $translator, private readonly UserServiceInterface $userService)
    {
    }

    /**
     * Index action.
     *
     * @param int $page Page
     *
     * @return Response HTTP Response
     */
    #[Route(name: 'project_index', methods: 'GET')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        /** @var User $user User entity */
        $user = $this->getUser();

        $pagination = $this->projectService->getPaginatedList($user, $page);

        return $this->render('project/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Project $project Project entity
     *
     * @return Response HTTP Response
     */
    #[Route('/{id}', name: 'project_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    public function show(Project $project): Response
    {
        if (!$this->isGranted('VIEW', $project)) {
            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/show.html.twig', ['project' => $project]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'project_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        if (!$this->isGranted('CREATE_PROJECT')) {
            return $this->redirectToRoute('index');
        }

        $project = new Project();
        $user = $this->getUser();
        $project->setManager($user);

        $form = $this->createForm(
            ProjectType::class,
            $project,
            ['action' => $this->generateUrl('project_create')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newMembers = $form->get('members')->getData();
            $this->projectService->addMembers($project, $newMembers);

            $this->userService->refreshUserToken($user);

            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('project/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Add members to project.
     *
     * @param Request $request HTTP Request
     * @param Project $project Project entity
     *
     * @return Response HTTP Response
     */
    #[Route('/{id}/members_add', name: 'project_members_add', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function membersAdd(Request $request, Project $project): Response
    {
        if (!$this->isGranted('EDIT', $project)) {
            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        $form = $this->createForm(
            ProjectType::class,
            $project,
            [
                'include_name' => false,
                'method' => 'PUT',
                'action' => $this->generateUrl('project_members_add', ['id' => $project->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newMembers = $form->get('members')->getData();
            $this->projectService->addMembers($project, $newMembers);

            $this->addFlash('success', $this->translator->trans('message.edited_successfully'));

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('project/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit project.
     *
     * @param Request $request HTTP Request
     * @param Project $project Project entity
     *
     * @return Response HTTP Response
     */
    #[Route('/{id}/edit', name: 'project_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function edit(Request $request, Project $project): Response
    {
        if (!$this->isGranted('EDIT', $project)) {
            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        $form = $this->createForm(
            ProjectType::class,
            $project,
            [
                'include_members' => false,
                'method' => 'PUT',
                'action' => $this->generateUrl('project_edit', ['id' => $project->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->projectService->save($project);

            $this->addFlash('success', $this->translator->trans('message.edited_successfully'));

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('project/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit project.
     *
     * @param Request $request HTTP Request
     * @param Project $project Project entity
     *
     * @return Response HTTP Response
     */
    #[Route('/{id}/edit_manager', name: 'project_edit_manager', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function editManager(Request $request, Project $project): Response
    {
        if (!$this->isGranted('EDIT', $project)) {
            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        $form = $this->createForm(
            ProjectType::class,
            $project,
            [
                'include_name' => false,
                'include_members' => false,
                'include_manager' => true,
                'members' => $project->getMembers(),
                'method' => 'PUT',
                'action' => $this->generateUrl('project_edit_manager', ['id' => $project->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->projectService->save($project);

            $this->addFlash('success', $this->translator->trans('message.edited_successfully'));

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('project/edit.html.twig', ['form' => $form->createView()]);
    }
}
