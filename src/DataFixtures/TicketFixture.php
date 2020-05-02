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
            UserFixture::class,
        ];
    }
}

