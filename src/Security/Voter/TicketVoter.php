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
        return in_array($attribute, ['TICKET_CREATE', 'TICKET_VIEW', 'TICKET_EDIT', 'TICKET_DELETE', 'TICKET_REOPEN', 'TICKET_ASSIGN_SELF'])
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
            case 'TICKET_CREATE':
                if ($this->security->isGranted('ROLE_TICKET_CREATE')) {
                    return true;
                }
                // logic to determine if the user can VIEW
                // return true or false
                break;
            case 'TICKET_VIEW':
                if ($ticket->getAuthor() == $user->getEmail()) {
                    return true;
                }
                if ($this->security->isGranted('ROLE_TICKET_VIEW')) {
                    return true;
                }
                // logic to determine if the user can VIEW
                // return true or false
                break;
            case 'TICKET_EDIT':
                if ($this->security->isGranted('ROLE_TICKET_EDIT')) {
                    return true;
                }
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case 'TICKET_DELETE':
                if ($this->security->isGranted('ROLE_TICKET_DELETE')) {
                    return true;
                }
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case 'TICKET_REOPEN':
                if ($this->security->isGranted('ROLE_TICKET_REOPEN')) {
                    return true;
                }
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case 'TICKET_ASSIGN_SELF':
                if ($this->security->isGranted('ROLE_TICKET_ASSIGN_SELF')) {
                    return true;
                }
                // logic to determine if the user can EDIT
                // return true or false
                break;

        }

        return false;
    }
}
