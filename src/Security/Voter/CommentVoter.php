<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $comment)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['COMMENT_CREATE', 'COMMENT_VIEW', 'COMMENT_EDIT', 'COMMENT_DELETE'])
            && $comment instanceof \App\Entity\Comment;
    }

    protected function voteOnAttribute($attribute, $comment, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'COMMENT_CREATE':
                if ($this->security->isGranted('ROLE_COMMENT_CREATE')) {
                    return true;
                }
                // logic to determine if the user can VIEW
                // return true or false
                break;
            case 'COMMENT_VIEW':
                if ($comment->getAuthor() == $user->getEmail()) {
                    return true;
                }
                if ($this->security->isGranted('ROLE_COMMENT_VIEW')) {
                    return true;
                }
                // logic to determine if the user can VIEW
                // return true or false
                break;
            case 'COMMENT_EDIT':
                if ($this->security->isGranted('ROLE_COMMENT_EDIT')) {
                    return true;
                }
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case 'COMMENT_DELETE':
                if ($this->security->isGranted('ROLE_COMMENT_DELETE')) {
                    return true;
                }
                // logic to determine if the user can EDIT
                // return true or false
                break;
        }

        return false;
    }
}
