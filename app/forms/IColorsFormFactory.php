<?php

namespace App\Forms;

use Nette\Application\UI\Form;

interface IColorsFormFactory
{
    public function create(): Form;
}