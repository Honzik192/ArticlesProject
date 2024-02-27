<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\Image;
use Tracy\Debugger;
use Ublaboo\DataGrid\DataGrid;

class AddNewArticleFormPresenter extends Nette\Application\UI\Presenter {

	public function __construct( private Nette\Database\Explorer $database ) {
	}

	public function createComponentGrid(): DataGrid {
		$grid = new DataGrid();
		$articlesQuery = $this->database->table( 'articles' );

		$userId = $this->getUser()->getId();

		if( $this->getUser()->isInRole( 'redaktor' ) ) {
			$articlesQuery->where( 'user_id', $userId );
		}

		$grid->setDataSource( $articlesQuery );
		$grid->setItemsPerPageList( [ 20, 50, 100 ], true );
		$grid->addColumnText( 'id', 'Id' )->setSortable();
		$grid->addColumnText( 'title', 'Titulek' )->setSortable();
		$grid->addColumnText( 'text', 'Obsah' );
		$grid->addColumnDateTime( 'created_at', 'Vytvořeno' )->setFormat( 'j. n. Y' )->setSortable();

		return $grid;

	}

	protected function createComponentAddNewArticleForm(): Form {
		$form = new Form;
		$form->addText( 'title', 'Titulek:' )->setRequired();
		$form->addTextArea( 'text', 'Obsah:' )->setRequired();
		$form->addMultiUpload( 'image_path', 'Nahrát obrázky' );
		$form->addSubmit( 'send', 'Uložit a publikovat' );
		$form->onSuccess[] = $this->postFormSucceeded( ... );

		return $form;
	}

	private function postFormSucceeded( array $data ): void {
		$article = $this->database->table( 'articles' )->insert( [
			'title'      => $data[ 'title' ],
			'text'       => $data[ 'text' ],
			'created_at' => new \DateTime(),
			'user_id'    => $this->getUser()->getId(),
		] );

		if( $data[ 'image_path' ] !== [] ) {
			$this->saveImageToLocalDisk( $data[ 'image_path' ], $article );
		}

		$mail = new Nette\Mail\Message;
		$mail->setFrom( 'Pepík <pepik@example.com>' )
			->addTo( 'h-blaha@seznam.cz' )
			->setSubject( 'Byl vytvořen příspěvěk' )
			->setBody( "Byl vytvořen příspěvek pomocí od Redactora" );
		$this->flashMessage( "Email byl úspěšně odeslán.", 'success' );


		//todo jb odkomentovat
//		$mailer = new Nette\Mail\SendmailMailer;
//		$mailer->send( $mail );

		$this->flashMessage( "Příspěvek byl úspěšně publikován.", 'success' );
		$this->redirect( 'show' );
	}


	public function saveImageToLocalDisk( array $images, Nette\Database\Table\ActiveRow $article ) {
		Debugger::log( $article->offsetGet( "id" ) );

		foreach( $images as $imageData ) {
			$image = Image::fromFile( $imageData );
			$imageFilename = 'article_' . uniqid() . '.jpg';
			$image->save( $imageFilename );

			$this->database->table( 'images' )->insert( [
				'path'       => $imageFilename,
				'article_id' => $article->offsetGet( "id" ),
			] );
		}
	}
}
