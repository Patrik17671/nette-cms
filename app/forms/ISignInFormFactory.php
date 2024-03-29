<?php

namespace App\Forms;

use Nette\Application\UI\Form;

interface ISignInFormFactory
{
    public function create(): Form;
}