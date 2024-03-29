<?php

namespace App\Components\Sidebar;

use Nette\Application\UI\Control;

class SidebarControl extends Control
{
    public function render(): void
    {
        $this->template->items = [
            'Banners' => $this->getPresenter()->link('Home:banners'),
            'Products' => $this->getPresenter()->link('Home:products'),
        ];
        $this->template->setFile(__DIR__ . '/sidebar.latte');
        $this->template->render();
    }
}
