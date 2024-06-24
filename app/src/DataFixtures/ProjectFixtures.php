<?php
/**
 * Project fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Enum\UserRole;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class ProjectFixtures.
 */
class ProjectFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        if (!$this->manager instanceof \Doctrine\Persistence\ObjectManager || !$this->faker instanceof \Faker\Generator) {
            return;
        }

        $this->createMany(100, 'projects', function (int $i) {
            $project = new Project();
            $project->setName($this->faker->company());
            $project->setCreatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            $project->setUpdatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            /** @var User $manager User entity */
            $manager = $this->getRandomReference('users');
            $project->setManager($manager);
            $manager->addRole(UserRole::ROLE_PROJECT_MANAGER->value);
            $project->addMember($manager);

            $tagNum = $this->faker->numberBetween(1, 10);
            for ($j = 0; $j < $tagNum; ++$j) {
                /** @var User $member User entity */
                $member = $this->getRandomReference('users');
                $project->addMember($member);
            }

            return $project;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: UserFixtures::class}
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
