<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\User;

class UserFixture extends BaseFixture implements DependentFixtureInterface
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function loadData(\Doctrine\Common\Persistence\ObjectManager $manager)
    {
        $this->createMany(10, 'usersCustomers', function($i) use ($manager) {
            $user = new User();
            $user->setEmail(sprintf('spacebar%d@example.com', $i));


            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'engage'
            ));

            $tickets = $this->getRandomReferences('tickets', $this->faker->numberBetween(0, 5));
            foreach ($tickets as $ticket) {
                $ticket->addTicket($ticket);
            }

            return $user;
        });

        // uit te breiden met andere groepjes en daar dan setRoles op toepassen

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TicketFixture::class,
        ];
    }
}
