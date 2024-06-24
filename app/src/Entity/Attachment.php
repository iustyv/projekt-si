<?php
/**
 * Attachment entity.
 */

namespace App\Entity;

use App\Repository\AttachmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Attachment.
 *
 * @psalm-suppress MissingConstructor
 */
#[ORM\Table(name: 'attachments')]
#[ORM\UniqueConstraint(name: 'uq_attachment_filename', columns: ['filename'])]
#[UniqueEntity(fields: ['filename'])]
#[ORM\Entity(repositoryClass: AttachmentRepository::class)]
class Attachment
{
    /**
     * Id.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Filename.
     */
    #[ORM\Column(type: 'string', length: 191)]
    #[Assert\Type('string')]
    private ?string $filename = null;

    /**
     * Getter for id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for filename.
     *
     * @return string|null Filename
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Setter for filename.
     *
     * @param string $filename Filename
     */
    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }
}
