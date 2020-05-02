<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use App\Entity\Ticket;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TagFixture extends BaseFixture
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(20, 'tags', function ($count) use ($manager) {
            $tag = new Tag();
            $tag->setName($this->faker->firstName);
            return $tag;
        });
        $manager->flush();
    }
}
