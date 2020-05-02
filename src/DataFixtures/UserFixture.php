<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class UserFixture extends BaseFixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(10, 'usersCustomers', function ($count) use ($manager) {
            $user = new User();
            $user->setEmail(sprintf('spacebar%d@example.com', $count));
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'engage'
            ));
            return $user;
        });
        $this->createMany(1, 'usersAdmin', function ($count) use ($manager) {
            $user = new User();
            $user->setEmail(sprintf('admin%d@example.com', $count));
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'engage'
            ));
            $user->setRoles(['ROLE_ADMIN']);
            return $user;
        });
        $manager->flush();
        $this->createMany(5, 'usersAgentLine1', function ($count) use ($manager) {
            $user = new User();
            $user->setEmail(sprintf('agentLine1_%d@example.com', $count));
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'engage'
            ));
            $user->setRoles(['ROLE_AGENT_LINE_1']);
            return $user;
        });
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TicketFixture::class
        ];
    }

}
