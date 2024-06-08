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
class UserDataTransformer implements DataTransformerInterface
{
    /**
     * Constructor.
     *
     * @param UserServiceInterface $userService User service
     */
    public function __construct(private readonly UserServiceInterface $userService)
    {
    }


    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        return $value->getNickname();
    }

    public function reverseTransform($value): ?User
    {
        if (null === $value) return null;
        $value = trim($value);
        if ('' === $value) return null;
        if (!preg_match('/^[a-zA-Z0-9.]+$/', $value)) return null;

        return $this->userService->findOneByUsername($value);
    }
}
