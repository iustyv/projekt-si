<?php
/**
 * Report list input filters DTO.
 */

namespace App\Dto;

/**
 * Class ReportListInputFiltersDto.
 */
class ReportListInputFiltersDto
{
    /**
     * Constructor.
     *
     * @param string|null $search        Search query
     * @param int|null    $categoryId    Category id
     * @param int|null    $tagId         Tag id
     * @param int|null    $statusId      Status id
     * @param int|null    $projectId     Project id
     * @param bool|null   $unassigned    Variable that filters reports unassigned to any project
     * @param bool|null   $assigned      Variable that filters reports assigned to user projects
     * @param bool|null   $adminAssigned Variable that filters reports assigned to other projects - for admin
     */
    public function __construct(public readonly ?string $search = null, public readonly ?int $categoryId = null, public readonly ?int $tagId = null, public readonly ?int $statusId = null, public readonly ?int $projectId = null, public readonly ?bool $unassigned = false, public readonly ?bool $assigned = false, public readonly ?bool $adminAssigned = false)
    {
    }
}
