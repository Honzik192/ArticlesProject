<?php declare( strict_types=1 );

use Nette\Database\Explorer;
use Nette\Security\AuthenticationException;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;
use Tracy\Debugger;

class Authenticator implements \Nette\Security\Authenticator {

	public function __construct(
		private Explorer $database,
	) {
	}

	public function authenticate( string $user, string $password ): IIdentity {
		$user = $this->database->table( 'users' )->where( 'login', $user )->fetch();

		if( $user === null )
			throw new AuthenticationException( 'Uživatel neexistuje' );

		if( ( $password === $user->password ) === false )
			throw new AuthenticationException( 'Špatné heslo' );

		return new SimpleIdentity( $user->id, $user->role, [ 'login' => $user->login ] );
	}
}