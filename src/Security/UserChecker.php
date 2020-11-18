<?php

namespace App\Security;

use App\Entity\User as AppUser;
use App\Exception\AccountDeletedException;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof AppUser) {
            return;
        }

        if ($user->getIsEnabled() == 0) {
            // the message passed to this exception is meant to be displayed to the user
            throw new CustomUserMessageAccountStatusException("Votre email n'a pas encore été validé.");
        }

        if ($user->getIsBanned() == 1) {
            // the message passed to this exception is meant to be displayed to the user
            throw new CustomUserMessageAccountStatusException("Votre a été désactivé, veulliez nous contacté directement par mail pour toute réclamation.");
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof AppUser) {
            return;
        }

        // user account is expired, the user may be notified
        // if ($user->isExpired()) {
        //     throw new AccountExpiredException('...');
        // }
    }
}