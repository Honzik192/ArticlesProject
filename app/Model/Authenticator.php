<?php declare(strict_types=1);

use App\Constants\Constants;
use Nette\Database\Explorer;
use Nette\Security\AuthenticationException;
use Nette\Security\IIdentity;
use Nette\Security\SimpleIdentity;

class Authenticator implements \Nette\Security\Authenticator {

    public function __construct(private Explorer $database,) {
    }

    public function authenticate(string $user, string $password): IIdentity {
        $user = $this->database->table('users')->where('login', $user)->fetch();

        if ($user === null) {
            throw new AuthenticationException(Constants::USER_NOT_EXIST);
        }
        if (($password === $user->password) === false) {
            throw new AuthenticationException(Constants::BAD_PASSWORD);
        }
        return new SimpleIdentity($user->id, $user->role, ['login' => $user->login]);
    }
}