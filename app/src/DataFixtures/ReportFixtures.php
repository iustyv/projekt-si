<?php
/**
 * Report fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Report;
use App\Entity\Category;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class ReportFixtures.
 */
class ReportFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(100, 'reports', function (int $i) {
            $report = new Report();
            $report->setTitle($this->faker->realTextBetween(20, 35));
            $report->setDescription($this->faker->realTextBetween(150, 500));
            $report->setCreatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            $report->setUpdatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            /** @var Category $category */
            $category = $this->getRandomReference('categories');
            $report->setCategory($category);

            return $report;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: CategoryFixtures::class}
     */
    public function getDependencies(): array
    {
        return [CategoryFixtures::class];
    }
}
