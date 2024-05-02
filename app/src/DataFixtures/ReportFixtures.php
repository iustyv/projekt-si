<?php

namespace App\DataFixtures;

use App\Entity\Report;

class ReportFixtures extends AbstractBaseFixtures
{
    protected function loadData(): void
    {
        for ($i = 0; $i < 10; ++$i) {
            $task = new Report();
            $task->setTitle($this->faker->sentence);
            $task->setCreatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $task->setUpdatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $this->manager->persist($task);
        }

        $this->manager->flush();
    }
}
