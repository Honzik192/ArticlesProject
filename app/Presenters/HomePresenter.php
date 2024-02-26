<?php

declare(strict_types=1);

namespace App\Presenters;

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

        $grid->addColumnText('id', 'Id')->setSortable();

        $grid->addColumnText('title', 'Titulek')->setSortable();

        $grid->addColumnText('text', 'Obsah');

        $grid->addColumnDateTime('created_at', 'Vytvořeno')->setFormat('j. n. Y')->setSortable();

        return $grid;
    }

}
