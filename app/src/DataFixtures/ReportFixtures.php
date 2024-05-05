<?php
/**
 * Report fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Report;

/**
 * Class ReportFixtures.
 */
class ReportFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     */
    protected function loadData(): void
    {
        for ($i = 0; $i < 10; ++$i) {
            $report = new Report();
            $report->setTitle($this->faker->sentence);
            $report->setCreatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $report->setUpdatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $this->manager->persist($report);
        }

        $this->manager->flush();
    }
}
