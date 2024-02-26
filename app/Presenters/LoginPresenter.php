<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Tracy\Debugger;


class LoginPresenter extends Nette\Application\UI\Presenter {

    public function __construct(private Nette\Database\Explorer $database,) {
    }

    public function createComponentLoginForm() {
        $form = new Form();
        $form->addText('login', 'Login:');
        $form->addText('password', 'Password:');
        $form->addSubmit('submit', 'Přihlásit');
        $form->onSuccess[] = $this->loginFormSucceeded(...);

        return $form;
    }

    public function loginFormSucceeded(Form $form, array $values): void {
        if ($this->authenticate($values['login'], $values['password'])) {
            $this->redirect('AddNewArticleForm:show');
        } else {
            $form->addError('Nesprávné přihlašovací jméno nebo heslo');
        }
    }

    private function authenticate(string $login, string $password): bool {
        $users = $this->database->table('users')->fetchAll();
        foreach ($users as $index => $user) {
            if ($user->login === $login && $user->password === $password) {
                return true;
            }
        }
        return false;

    }
}