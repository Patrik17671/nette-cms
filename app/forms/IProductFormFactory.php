<?php

namespace App\Forms;

use Nette\Application\UI\Form;

interface IProductFormFactory
{
    public function create(): Form;
}