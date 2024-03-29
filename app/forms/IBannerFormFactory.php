<?php

namespace App\Forms;

use Nette\Application\UI\Form;

interface IBannerFormFactory
{
    public function create(): Form;
}