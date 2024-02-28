<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Constants\Constants;
use Nette;
use Ublaboo\DataGrid\DataGrid;


final class HomePresenter extends Nette\Application\UI\Presenter {

    public function __construct(private Nette\Database\Explorer $database) {
    }

    public function renderDefault(): void {
        $this->template->articles = $this->database->table('articles')->order('created_at DESC')->limit(5);
    }

    public function createComponentGrid(): DataGrid {
        $grid = new DataGrid();
        $grid->setDataSource($this->database->table('articles'));

        $grid->setItemsPerPageList([20, 50, 100], true);
        $grid->addColumnText('id', 'Id');
        $grid->addColumnText('title', Constants::ARTICLE_TITLE);
        $grid->addColumnText('text', Constants::CONTENT);
        $grid->addColumnDateTime('created_at', Constants::CREATED_AT)
            ->setFormat('j. n. Y')
            ->setSortable();

        return $grid;
    }

}
