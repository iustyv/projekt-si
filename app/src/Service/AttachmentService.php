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
    public function __construct(private readonly string $targetDirectory, private readonly AttachmentRepository $attachmentRepository, private readonly FileUploadServiceInterface $fileUploadService, private readonly Filesystem $filesystem)
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

        if ($report->getAttachment() === null) {
            $attachment = new Attachment();
            $attachment->setReport($report);
        }
        else {
            $attachment = $report->getAttachment();
        }

        $attachment->setFilename($attachmentFilename);
        $report->setAttachment($attachment);

        $this->attachmentRepository->save($attachment);

        return $attachment;
    }

    public function update(UploadedFile $uploadedFile, Report $report): void
    {
        if ($report->getAttachment() !== null)
        {
            $filename = $report->getAttachment()->getFilename();
            $this->filesystem->remove(
                $this->targetDirectory.'/'.$filename
            );
        }
        $this->create($uploadedFile, $report);
    }

    public function delete(Report $report): void
    {
        if ($report->getAttachment() === null) return;
        $filename = $report->getAttachment()->getFilename();
        $this->filesystem->remove(
            $this->targetDirectory.'/'.$filename
        );
        $this->attachmentRepository->delete($report->getAttachment());
        $report->setAttachment(null);
    }
}
