<?php
/**
 * Comment fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Report;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class CommentFixtures.
 *
 * @psalm-suppress MissingConstructor
 */
class CommentFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    public function loadData(): void
    {
        if (!$this->manager instanceof \Doctrine\Persistence\ObjectManager || !$this->faker instanceof \Faker\Generator) {
            return;
        }

        $this->createMany(1500, 'comments', function ($i) {
            $comment = new Comment();
            $comment->setContent($this->faker->realTextBetween(100, 250));
            $comment->setCreatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $comment->setUpdatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            /** @var Report $report */
            $report = $this->getRandomReference('reports');
            $comment->setReport($report);

            /** @var User $author */
            $author = $this->getRandomReference('users');
            $comment->setAuthor($author);

            return $comment;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: ReportFixtures::class}
     */
    public function getDependencies(): array
    {
        return [
            ReportFixtures::class,
            UserFixtures::class,
        ];
    }
}
