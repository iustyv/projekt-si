<?php
/**
 * Attachment service.
 */

namespace App\Service;

use App\Entity\Attachment;
use App\Entity\Report;
use App\Repository\AttachmentRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class AttachmentService.
 */
class AttachmentService implements AttachmentServiceInterface
{
    /**
     * Constructor.
     *
     * @param string                     $targetDirectory      Target directory
     * @param AttachmentRepository       $attachmentRepository Attachment repository
     * @param FileUploadServiceInterface $fileUploadService    File upload service
     * @param Filesystem                 $filesystem           Filesystem
     */
    public function __construct(private readonly string $targetDirectory, private readonly AttachmentRepository $attachmentRepository, private readonly FileUploadServiceInterface $fileUploadService, private readonly Filesystem $filesystem)
    {
    }

    /**
     * Create attachment.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Report       $report       Report entity
     */
    public function create(UploadedFile $uploadedFile, Report $report): void
    {
        $attachmentFilename = $this->fileUploadService->upload($uploadedFile);

        $attachment = $report->getAttachment() ?? new Attachment();

        $attachment->setFilename($attachmentFilename);
        $report->setAttachment($attachment);

        $this->attachmentRepository->save($attachment);
    }

    /**
     * Update attachment.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Report       $report       Report entity
     */
    public function update(UploadedFile $uploadedFile, Report $report): void
    {
        if ($report->getAttachment() instanceof Attachment) {
            $filename = $report->getAttachment()->getFilename();
            $this->filesystem->remove(
                $this->targetDirectory.'/'.$filename
            );
        }
        $this->create($uploadedFile, $report);
    }

    /**
     * Delete attachment.
     *
     * @param Report $report Report entity
     */
    public function delete(Report $report): void
    {
        $attachment = $report->getAttachment();
        if (!$attachment instanceof Attachment) {
            return;
        }
        $filename = $attachment->getFilename();
        $this->filesystem->remove(
            $this->targetDirectory.'/'.$filename
        );
        $report->setAttachment(null);
        $this->attachmentRepository->delete($attachment);
    }
}
