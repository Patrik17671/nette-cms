<?php
    namespace App\Forms;

    use Nette\Application\UI\Form;

    class SignInFormFactory implements ISignInFormFactory
    {
        public function create(): Form
        {
            $form = new Form;
            $form->addText('username', 'Username:')
                ->setRequired('Please enter your username.');
            $form->addPassword('password', 'Password:')
                ->setRequired('Please enter your password.');
            $form->addSubmit('send', 'Sign in');

            return $form;
        }
    }