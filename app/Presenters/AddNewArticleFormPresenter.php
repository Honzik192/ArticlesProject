<?php

namespace App\Presenters;

use App\Constants\Constants;
use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\Image;
use Ublaboo\DataGrid\DataGrid;

class AddNewArticleFormPresenter extends Nette\Application\UI\Presenter {


    public function __construct(private Nette\Database\Explorer $database) {
    }

    public function createComponentGrid(): DataGrid {
        $grid = new DataGrid();

        $articlesQuery = $this->database->table(Constants::ARTICLES);
        $userId = $this->getUser()->getId();

        $isRedactor = $this->getUser()->isInRole(Constants::REDACTOR);
        if ($isRedactor) {
            $articlesQuery->where('user_id', $userId);
        }

        $grid->setDataSource($articlesQuery);
        $grid->setItemsPerPageList([20, 50, 100], true);
        $grid->addColumnText('id', 'Id');
        $grid->addColumnText('title', Constants::ARTICLE_TITLE);
        $grid->addColumnText('text', Constants::CONTENT);
        $grid->addColumnDateTime('created_at', Constants::CREATED_AT)
             ->setFormat('j. n. Y')->setSortable();

        return $grid;

    }

    protected function createComponentAddNewArticleForm(): Form {
        $form = new Form;
        $form->addText('title', Constants::ARTICLE_TITLE)
             ->setRequired()
             ->setHtmlAttribute('style', 'width: 300px;');

        $form->addTextArea('text', Constants::CONTENT)
             ->setRequired()
             ->setHtmlAttribute('style', 'width: 400px; height: 100px;');

        $form->addMultiUpload('image_path', Constants::UPLOAD_IMAGE);

        $form->addSubmit('send', Constants::SAVE_AND_PUBLIC);

        $form->onSuccess[] = $this->postFormSucceeded(...);

        return $form;
    }

    private function postFormSucceeded(array $data): void {
        $article = $this->database->table(Constants::ARTICLES)->insert(
            [
                'title' => $data['title'],
                'text' => $data['text'],
                'created_at' => new \DateTime(),
                'user_id' => $this->getUser()->getId(),
            ]
        );

        if ($data['image_path'] !== []) {
            $this->saveImageToLocalDisk($data['image_path'], $article);
        }
        if ($this->getUser()->isInRole('redaktor')) {
            $mail = new Nette\Mail\Message;

            $mail->setFrom('Pepík <pepik@example.com>')
                 ->addTo('') //todo vložit vlastní email
                 ->setSubject('Byl vytvořen příspěvěk')
                 ->setBody("Byl vytvořen příspěvek pomocí od Redactora");
            $this->flashMessage("Email byl zaslán adminovi.", 'success');

            $mailer = new Nette\Mail\SendmailMailer;
            $mailer->send($mail);
        }

        $this->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
        $this->redirect('default');
    }


    public function saveImageToLocalDisk(array $images, Nette\Database\Table\ActiveRow $article) {
        foreach ($images as $imageData) {
            $image = Image::fromFile($imageData);
            $imageFilename = 'article_' . uniqid() . '.jpg';
            $image->save($imageFilename);

            $this->database->table('images')->insert(['path' => $imageFilename, 'article_id' => $article->offsetGet("id"),]);
        }
    }
}
