<?php
/**
 * Category Controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Service\CategoryServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class CategoryController.
 */
#[Route('/category')]
class CategoryController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param CategoryServiceInterface  $categoryService  Category service interface
     */
    public function __construct(private readonly CategoryServiceInterface $categoryService)
    {
    }

    /**
     * Index action.
     *
     * @param int $page Page
     *
     * @return Response HTTP Response
     */
    #[Route(name: 'category_index', methods: 'GET')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->categoryService->getPaginatedList($page);

        return $this->render('category/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Category $category Category entity
     * @param int    $page   Page number
     *
     * @return Response HTTP Response
     */
    #[Route('/{id}', name: 'category_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    public function show(Category $category, #[MapQueryParameter] int $page = 1): Response
    {
        $reports = $this->categoryService->getPaginatedListOfReports($category, $page);

        return $this->render('category/show.html.twig', ['category' => $category, 'reports' => $reports]);
    }
}
