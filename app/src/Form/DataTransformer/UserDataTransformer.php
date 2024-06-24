<?php
/**
 * User data transformer.
 */

namespace App\Form\DataTransformer;

use App\Entity\User;
use App\Service\UserServiceInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class UserDataTransformer.
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

    /**
     * Transforms User object to a string.
     *
     * @param User|null $value User object to transform
     *
     * @return string Nickname of the User or empty string
     */
    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        return $value->getNickname();
    }

    /**
     * Transforms string to User object.
     *
     * @param string|null $value Nickname string to transform
     *
     * @return User|null User object corresponding to the nickname or null
     */
    public function reverseTransform($value): ?User
    {
        if (null === $value) {
            return null;
        }
        $value = trim($value);
        if ('' === $value) {
            return null;
        }
        if (!preg_match('/^[a-zA-Z0-9.]+$/', $value)) {
            return null;
        }

        return $this->userService->findOneByUsername($value);
    }
}
