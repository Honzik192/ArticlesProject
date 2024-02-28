<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;


class LoginPresenter extends Nette\Application\UI\Presenter {

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
			$this->redirect( 'AddNewArticleForm:default' );

		} catch( Nette\Security\AuthenticationException $e ) {
			$this->flashMessage( $e->getMessage(), 'danger' );
            $form->addError('Špatně zadané jméno nebo heslo');
		}

	}

}