<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Tracy\Debugger;
use Ublaboo\DataGrid\DataGrid;

class AddNewArticleFormPresenter extends Nette\Application\UI\Presenter {

    public function __construct(private Nette\Database\Explorer $database, private Nette\Http\Session $session) {
    }

    public function createComponentGrid(): DataGrid {
        $grid = new DataGrid();
        $articlesQuery = $this->database->table('articles');

        $userId = $this->session->getSection('user')->get('user_id');

        if ($this->getUser()->isInRole('redaktor')) {
            $articlesQuery->where('user_id', $userId);
        }

        $grid->setDataSource($articlesQuery);
        $grid->setItemsPerPageList([20, 50, 100], true);
        $grid->addColumnText('id', 'Id')->setSortable();
        $grid->addColumnText('title', 'Titulek')->setSortable();
        $grid->addColumnText('text', 'Obsah');
        $grid->addColumnDateTime('created_at', 'Vytvořeno')->setFormat('j. n. Y')->setSortable();

        return $grid;

    }

    protected function createComponentAddNewArticleForm(): Form {
        $form = new Form;
        $form->addText('title', 'Titulek:')->setRequired();
        $form->addTextArea('text', 'Obsah:')->setRequired();
        $form->addSubmit('send', 'Uložit a publikovat');
        $form->onSuccess[] = $this->postFormSucceeded(...);

        return $form;
    }

    private function postFormSucceeded(array $data): void {
        // Zde můžete využít opět session proměnnou pro získání userId
        $userId = $this->session->getSection('user')->userId;

        // Dále můžete pracovat s userId a daty formuláře

        $article = $this->database->table('articles')->insert($data);
        $mail = new Nette\Mail\Message;
        $mail->setFrom('Pepík <pepik@example.com>')->addTo('h-blaha@seznam.cz')->setSubject('Byl vytvořen příspěvěk')->setBody("Byl vytvořen příspěvek pomocí od Redactora");
        $this->flashMessage("Email byl úspěšně odeslán.", 'success');

        $mailer = new Nette\Mail\SendmailMailer;
        $mailer->send($mail);

        $this->flashMessage("Příspěvek byl úspěšně publikován.", 'success');
        $this->redirect('show');
    }
}
