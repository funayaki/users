<?php

namespace Users\Mailer;

use Users\Model\Entity\User;
use Cake\Mailer\Email;
use Cake\Mailer\Mailer;

class UserMailer extends Mailer
{

    public function resetPassword(User $user)
    {
        $this
            ->setTemplate('Users.reset_password')
            ->setTo($user->email)
            ->setSubject('Reset password')
            ->set(compact('user'));
    }
}
