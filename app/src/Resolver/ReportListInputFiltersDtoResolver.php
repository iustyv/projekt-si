<?php
/**
 * ReportListInputFiltersDto resolver.
 */

namespace App\Resolver;

use App\Dto\ReportListInputFiltersDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * ReportListInputFiltersDtoResolver class.
 */
class ReportListInputFiltersDtoResolver implements ValueResolverInterface
{
    /**
     * Returns the possible value(s).
     *
     * @param Request          $request  HTTP Request
     * @param ArgumentMetadata $argument Argument metadata
     *
     * @return iterable Iterable
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();

        if (!$argumentType || !is_a($argumentType, ReportListInputFiltersDto::class, true)) {
            return [];
        }

        $categoryId = $request->query->get('categoryId');
        $tagId = $request->query->get('tagId');
        $statusId = $request->query->get('statusId');
        $projectId = $request->query->get('projectId');
        $search = $request->query->get('search');

        return [new ReportListInputFiltersDto($search, $categoryId, $tagId, $statusId, $projectId)];
    }
}
