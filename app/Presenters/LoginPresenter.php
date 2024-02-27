<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Tracy\Debugger;


class LoginPresenter extends Nette\Application\UI\Presenter {

	public function __construct( private Nette\Database\Explorer $database ) {
	}

	public function createComponentLoginForm() {
		$form = new Form();
		$form->addText( 'login', 'Login:' );
		$form->addPassword( 'password', 'Heslo:' );
		$form->addSubmit( 'submit', 'Přihlásit' );
		$form->onSuccess[] = $this->loginFormSucceeded( ... );

		return $form;
	}

	public function loginFormSucceeded( Nette\Application\UI\Form $form ) {
		$values = $form->getValues();

		try {
			$this->getUser()->login( $values->login, $values->password );
			$this->redirect( 'AddNewArticleForm:show' );

		} catch( Nette\Security\AuthenticationException $e ) {
			$this->flashMessage( $e->getMessage(), 'danger' );
		}

	}

}