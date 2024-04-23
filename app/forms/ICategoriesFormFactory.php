<?php

namespace App\Forms;

use Nette\Application\UI\Form;

interface ICategoriesFormFactory
{
    public function create(): Form;
}