<?php
/**
 * Report list filters DTO.
 */

namespace App\Dto;

use App\Entity\Category;
use App\Entity\Enum\ReportStatus;
use App\Entity\Project;
use App\Entity\Tag;

/**
 * Class ReportListFiltersDto.
 */
class ReportListFiltersDto
{
    /**
     * Constructor.
     *
     * @param Category|null $category   Category entity
     * @param Tag|null      $tag        Tag entity
     * @param ReportStatus|null    $reportStatus Report status
     */
    public function __construct(public readonly ?string $search, public readonly ?Category $category, public readonly ?Tag $tag, public readonly ?ReportStatus $reportStatus, public readonly ?Project $project)
    {
    }
}