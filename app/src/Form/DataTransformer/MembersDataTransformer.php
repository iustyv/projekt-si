<?php
/**
 * Members data transformer.
 */

namespace App\Form\DataTransformer;

use App\Entity\User;
use App\Service\UserServiceInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class MembersDataTransformer.
 *
 * @implements DataTransformerInterface<mixed, mixed>
 */
class MembersDataTransformer implements DataTransformerInterface
{
    /**
     * Constructor.
     *
     * @param UserServiceInterface $userService User service
     */
    public function __construct(private readonly UserServiceInterface $userService)
    {
    }

    /**
     * Transform array of tags to string of tag titles.
     *
     * @param Collection<int, User> $value Tags entity collection
     *
     * @return string Result
     */
    public function transform($value): string
    {
        if (null === $value || $value->isEmpty()) {
            return '';
        }

        $tagTitles = [];

        foreach ($value as $member) {
            $members[] = $member->getNickname();
        }

        return implode(', ', $members);
    }

    /**
     * Transform string of tag names into array of User entities.
     *
     * @param string $value String of tag names
     *
     * @return array<int, User> Result
     */
    public function reverseTransform($value): array
    {
        $usernames = explode(',', $value);

        $members = [];

        foreach ($usernames as $username) {
            $username = trim($username);
            if ('' === $username) continue;
            if (!preg_match('/^[a-zA-Z0-9.]+$/', $username)) continue;

            $member = $this->userService->findOneByUsername($username);
            if (null === $member) continue;

            $members[] = $member;
        }

        return $members;
    }
}
