<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EventFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData()
    {
        $this->createMany(150, 'event', function () {
            return (new Event())
                ->setName($this->faker->catchPhrase, 0 , 50)
                ->setDescription($this->faker->optional()->realText())
                ->setPlace($this->faker->city)
                ->setDate($this->faker->dateTimeThisYear())
                ->setAuthor($this->getRandomReference('user_user'))
            ;
        });
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
