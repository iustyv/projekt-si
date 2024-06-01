<?php
/**
 * User Controller.
 */

namespace App\Controller;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Form\Type\User\UserNicknameType;
use App\Form\Type\User\UserPasswordType;
use App\Form\Type\User\UserRegistrationType;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController.
 */
#[Route('/user')]
class UserController extends AbstractController
{
    public function __construct(private readonly TranslatorInterface $translator, private readonly UserPasswordHasherInterface $passwordHasher, private readonly UserServiceInterface $userService)
    {
    }

    /**
     * Index action.
     *
     * @param int $page Page
     *
     * @return Response HTTP Response
     */
    #[Route(name: 'user_index', methods: 'GET')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        if(!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('index');
        }
        $pagination = $this->userService->getPaginatedList($page);

        return $this->render('user/index.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/{id}', name: 'user_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    public function show(?User $user = null): Response
    {
        //$user = $user ?? $this->getUser();
        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'user_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        if($this->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            return $this->redirectToRoute('index');
        }

        $user = new User();
        $user->setRoles([UserRole::ROLE_USER->value]);

        $form = $this->createForm(
            UserRegistrationType::class,
            $user,
            ['action' => $this->generateUrl('user_create')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $user->getPassword();
            $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

            $this->userService->save($user);

            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('index');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit action.
     *
     * @param Request  $request  HTTP request
     * @param User $user User entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/nick', name: 'nick_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function nick_edit(Request $request, User $user): Response
    {
        if(!$this->isGranted('EDIT_USER', $user))
        {
            return $this->redirectToRoute('index');
        }

        $form = $this->createForm(
            UserNicknameType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('nick_edit', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if(!$this->isGranted('ROLE_ADMIN')) {
                $currentPassword = $form->get('password')->getData();
                if(!$this->passwordHasher->isPasswordValid($user, $currentPassword))
                {
                    $this->addFlash('error', $this->translator->trans('message.error_password'));

                    return $this->redirectToRoute('nick_edit', ['id' => $user->getId()]);
                }
            }

            $this->userService->save($user);

            $this->addFlash('success', $this->translator->trans('message.edited_successfully'));

            return $this->redirectToRoute('index');
        }

        return $this->render('user/edit_nick.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }

    /**
     * Edit password.
     *
     * @param Request $request HTTP Request
     * @param User $user User entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/pass', name: 'pass_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function pass_edit(Request $request, User $user): Response
    {
        if(!$this->isGranted('EDIT_USER', $user)) {
            return $this->redirectToRoute('index');
        }

        $form = $this->createForm(
            UserPasswordType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('pass_edit', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isGranted('ROLE_ADMIN')) {
                $currentPassword = $form->get('current_password')->getData();
                if(!$this->passwordHasher->isPasswordValid($user, $currentPassword))
                {
                    $this->addFlash('error', $this->translator->trans('message.error_password'));

                    return $this->redirectToRoute('pass_edit', ['id' => $user->getId()]);
                }
            }

            $newPassword = $form->get('password')->getData();
            $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
            $this->userService->save($user);

            $this->addFlash('success', $this->translator->trans('message.edited_successfully'));

            return $this->redirectToRoute('index');
        }

        return $this->render('user/edit_pass.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }

    /**
     * Toggle block user.
     *
     * @param User $user User entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/toggle_block', name: 'user_toggle_block', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function user_toggle_block(User $user): Response
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('index');
        }

        $user->setIsBlocked(!$user->isBlocked());
        $this->userService->save($user);

        return $this->redirectToRoute('user_index');
    }

    /**
     * Set admin.
     *
     * @param User $user User entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/set_admin', name: 'set_admin', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function set_admin(User $user): Response
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('index');
        }

        $user->setRoles([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
        $this->userService->save($user);

        return $this->redirectToRoute('user_index');
    }

    /**
     * Remove admin.
     *
     * @param User $user User entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/remove_admin', name: 'remove_admin', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function remove_admin(User $user): Response
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('index');
        }

        if(!$this->userService->adminCanBeDeleted($user))
        {
            $this->addFlash('warning', $this->translator->trans('message.cannot_remove_admin'));

            return $this->redirectToRoute('user_index');
        }

        $this->userService->removeAdmin($user);

        return $this->redirectToRoute('user_index');
    }
}