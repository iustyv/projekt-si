<?php
/**
 * Attachment service interface.
 */

namespace App\Service;

use App\Entity\Attachment;
use App\Entity\Report;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Attachment service.
 */
interface AttachmentServiceInterface
{
    /**
     * Create attachment.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Attachment       $attachment       Attachment entity
     * @param Report         $report         Report entity
     */
    public function create(UploadedFile $uploadedFile, Report $report): Attachment;
}
