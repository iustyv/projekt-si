<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

abstract class AbstractBaseFixtures extends Fixture
{
    protected Generator $faker;

    protected ObjectManager $manager;

    public function load(ObjectManager $manager): void
    {
        $this->manager=$manager;
        $this->faker=Factory::create();
        $this->loadData();
    }

    abstract protected function loadData(): void;
}
