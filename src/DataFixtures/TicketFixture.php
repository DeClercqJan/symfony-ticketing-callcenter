<?php

namespace App\DataFixtures;

use App\Entity\Ticket;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TicketFixture extends BaseFixture implements DependentFixtureInterface
{
    private static $externalStatusMessages = [
        'open',
        'in progress',
        'waiting for customer feedback',
        'closed',
        'won\'t fix'
    ];

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(20, 'tickets', function ($count) use ($manager) {
            $ticket = new Ticket();
            $ticket->setPriorityLevel($this->faker->numberBetween(0, 2));
            $ticket->setExternalStatusMessage($this->faker->randomElement(self::$externalStatusMessages));
            $ticket->setTicketText($this->faker->paragraph(1, true));
            $ticket->addUser($this->getRandomReference('usersCustomers'));
            $ticket->setAuthor($this->getRandomReference('usersCustomers'));
            return $ticket;
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
            UserFixture::class,
        ];
    }

//    private static $externalStatusMessages = [
//        null,
//        'open',
//        'in progress',
//        'waiting for customer feedback',
//        'closed',
//        'won\'t fix'
//    ];
//
//    public function loadData(ObjectManager $manager)
//    {
//        $this->createMany(20, 'tickets', function ($count) use ($manager) {
//            $ticket = new Ticket();
//            $ticket->setPriorityLevel($this->faker->numberBetween(0, 2));
//            $ticket->setExternalStatusMessage($this->faker->randomElement(self::$externalStatusMessages));
//            $ticket->setTicketText(<<<EOF
//Spicy **jalapeno bacon** ipsum dolor amet veniam shank in dolore. Ham hock nisi landjaeger cow,
//lorem proident [beef ribs](https://baconipsum.com/) aute enim veniam ut cillum pork chuck picanha. Dolore reprehenderit
//labore minim pork belly spare ribs cupim short loin in. Elit exercitation eiusmod dolore cow
//**turkey** shank eu pork belly meatball non cupim.
//
//Laboris beef ribs fatback fugiat eiusmod jowl kielbasa alcatra dolore velit ea ball tip. Pariatur
//laboris sunt venison, et laborum dolore minim non meatball. Shankle eu flank aliqua shoulder,
//capicola biltong frankfurter boudin cupim officia. Exercitation fugiat consectetur ham. Adipisicing
//picanha shank et filet mignon pork belly ut ullamco. Irure velit turducken ground round doner incididunt
//occaecat lorem meatball prosciutto quis strip steak.
//
//Meatball adipisicing ribeye bacon strip steak eu. Consectetur ham hock pork hamburger enim strip steak
//mollit quis officia meatloaf tri-tip swine. Cow ut reprehenderit, buffalo incididunt in filet mignon
//strip steak pork belly aliquip capicola officia. Labore deserunt esse chicken lorem shoulder tail consectetur
//cow est ribeye adipisicing. Pig hamburger pork belly enim. Do porchetta minim capicola irure pancetta chuck
//fugiat.
//EOF
//            );
//
//            // $ticket->set($this->getRandomReference('usersCustomers'));
//
////            $comments = $this->getRandomReferences('comments', $this->faker->numberBetween(0, 5));
////            foreach ($comments as $comment) {
////                $ticket->addComment($comment);
////            }
//
//            return $ticket;
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
//            // CommentFixture::class,
//        ];
//    }
}

