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
            $comment->setCommentText(<<<EOF
Spicy **jalapeno bacon** ipsum dolor amet veniam shank in dolore. Ham hock nisi landjaeger cow,
lorem proident [beef ribs](https://baconipsum.com/)
EOF
            );
            $comment->setIsCommentPublic($this->faker->boolean(60));
            $comment->setTicket($this->getRandomReference('tickets'));
            $comment->setUser($this->getRandomReference('usersCustomers'));
            return $comment;
        });
        // $product = new Product();
        // $manager->persist($product);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TicketFixture::class,
            UserFixture::class,
        ];
    }
//
//    protected function loadData(\Doctrine\Common\Persistence\ObjectManager $manager)
//    {
//        $this->createMany(100, 'comments', function () {
//            $comment = new Comment();
//            $comment->setCommentText(
//                $this->faker->boolean ? $this->faker->paragraph : $this->faker->sentences(2, true)
//            );
//
//            // aan te passen met echte
//            // $comment->setUser($this->faker->name);
//            // deze weet ik niet goed, proberen maar
//            $users = $this->getRandomReferences('usersCustomers', $this->faker->numberBetween(1, 2));
//            foreach ($users as $user) {
//                $comment->setUser($user);
//            }
//            // $comment->setUser(new User());
//
//
//            $comment->setIsCommentPublic($this->faker->boolean(60));
//            // $comment->setTicket($this->getRandomReference('tickets'));
//
//            return $comment;
//        });
//        // $product = new Product();
//        // $manager->persist($product);
//
//        $manager->flush();
//    }
//
//    public function getDependencies()
//    {
//        return [
//            // TicketFixture::class,
//            UserFixture::class,
//        ];
//    }
}
