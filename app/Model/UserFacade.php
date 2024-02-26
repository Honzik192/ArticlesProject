<?php

namespace App\Model;

use Nette;

final class UserFacade {
    public function __construct(private Nette\Database\Explorer $database,) {
    }

    public function getUsers($user) {
        return $this->database->table('users')
            ->where('user_id', $user)
            ->order('created_at DESC');
    }
}