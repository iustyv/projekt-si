<?php
/**
 * ReportListInputFiltersDto resolver.
 */

namespace App\Resolver;

use App\Dto\ReportListInputFiltersDto;
//use App\Entity\Enum\ReportStatus;
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
        //$statusId = $request->query->get('statusId', ReportStatus::ACTIVE->value);

        //return [new ReportListInputFiltersDto($categoryId, $tagId, $statusId)];
        return [new ReportListInputFiltersDto($categoryId, $tagId)];
    }
}
