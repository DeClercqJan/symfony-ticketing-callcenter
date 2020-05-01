<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends BaseFixture implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(20, 'tasks', function ($count) use ($manager) {
            $task = new Task();
            $task->setDescription($this->faker->paragraphs(1, true));
            $task->addTag($this->getRandomReference('tags'));
            return $task;
        });
        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [
            // CommentFixture::class,
            TagFixture::class,
        ];
    }
}
