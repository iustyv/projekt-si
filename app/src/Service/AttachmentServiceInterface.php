<?php
/**
 * Attachment service interface.
 */

namespace App\Service;

use App\Entity\Attachment;
use App\Entity\Report;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface AttachmentServiceInterface.
 */
interface AttachmentServiceInterface
{
    /**
     * Create attachment.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Report       $report       Report entity
     */
    public function create(UploadedFile $uploadedFile, Report $report): void;

    /**
     * Update attachment.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Report       $report       Report entity
     */
    public function update(UploadedFile $uploadedFile, Report $report): void;

    /**
     * Delete attachment.
     *
     * @param Report $report Report entity
     */
    public function delete(Report $report): void;
}
