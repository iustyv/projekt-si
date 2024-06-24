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
     * @param string|null       $search        Search query
     * @param Category|null     $category      Category entity
     * @param Tag|null          $tag           Tag entity
     * @param ReportStatus|null $reportStatus  Report status
     * @param Project|null      $project       Project entity
     * @param bool|null         $unassigned    Variable that filters reports unassigned to any project
     * @param bool|null         $assigned      Variable that filters reports assigned to user projects
     * @param bool|null         $adminAssigned Variable that filters reports assigned to other projects - for admin
     */
    public function __construct(public readonly ?string $search, public readonly ?Category $category, public readonly ?Tag $tag, public readonly ?ReportStatus $reportStatus, public readonly ?Project $project, public readonly ?bool $unassigned, public readonly ?bool $assigned, public readonly ?bool $adminAssigned)
    {
    }
}
