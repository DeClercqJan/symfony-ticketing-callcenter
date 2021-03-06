<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Ticket;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\User\User;

class CommentFixture extends BaseFixture implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(100, 'comments', function ($count) use ($manager) {
            $comment = new Comment();
            $comment->setCommentText($this->faker->paragraph(1, true));
            $comment->setIsCommentPublic($this->faker->boolean(60));
            $comment->setTicket($this->getRandomReference('tickets'));
            $comment->setAuthor($this->getRandomReference('usersCustomers'));
            return $comment;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TicketFixture::class,
            UserFixture::class,
        ];
    }
}
