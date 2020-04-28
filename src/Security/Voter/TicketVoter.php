<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TicketVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $ticket)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['POST_CREATE', 'POST_EDIT', 'POST_VIEW'])
            && $ticket instanceof \App\Entity\Ticket;
    }

    protected function voteOnAttribute($attribute, $ticket, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'POST_VIEW':
                // if ($ticket->getAuthor() == $user->getEmail()) {
                    return true;
                // }
                // logic to determine if the user can VIEW
                // return true or false
                break;
            case 'POST_CREATE':
                if ($this->security->isGranted('ROLE_CUSTOMER')) {
                    return true;
                }
                // logic to determine if the user can VIEW
                // return true or false
                break;
            case
            'POST_EDIT':
                // logic to determine if the user can EDIT
                // return true or false
                break;
        }

        return false;
    }
}
