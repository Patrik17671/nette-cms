<?php
    namespace App\Forms;

    use Nette\Application\UI\Form;

    class SignInFormFactory implements ISignInFormFactory
    {
        public function create(): Form
        {
            $form = new Form;
            $form->addText('username', 'Username:')
                ->setRequired('Please enter your username.')
                ->setHtmlAttribute('class', 'form-control');
            $form->addPassword('password', 'Password:')
                ->setRequired('Please enter your password.')
                ->setHtmlAttribute('class', 'form-control');
            $form->addSubmit('send', 'Sign in')
                ->setHtmlAttribute('class', 'btn btn-primary mt-3');
            $form->setHtmlAttribute('class', 'card-body');

            return $form;
        }
    }