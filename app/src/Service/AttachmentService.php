<?php
/**
 * Attachment service.
 */

namespace App\Service;

use App\Entity\Attachment;
use App\Entity\Report;
use App\Repository\AttachmentRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class Attachment service.
 */
class AttachmentService implements AttachmentServiceInterface
{
    /**
     * Constructor.
     *
     * @param AttachmentRepository           $attachmentRepository  Attachment repository
     * @param FileUploadServiceInterface $fileUploadService File upload service
     */
    public function __construct(private readonly string $targetDirectory, private readonly AttachmentRepository $attachmentRepository, private readonly FileUploadServiceInterface $fileUploadService)
    {
    }

    /**
     * Create attachment.
     *
     * @param UploadedFile  $uploadedFile Uploaded file
     * @param Attachment        $attachment       Attachment entity
     * @param Report $report         Report entity
     */
    public function create(UploadedFile $uploadedFile, Report $report): Attachment
    {
        $attachmentFilename = $this->fileUploadService->upload($uploadedFile);

        $attachment = new Attachment();
        $attachment->setReport($report);
        $attachment->setFilename($attachmentFilename);

        $this->attachmentRepository->save($attachment);

        return $attachment;
    }
}
