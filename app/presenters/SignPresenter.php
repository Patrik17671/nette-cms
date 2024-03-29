<?php
namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Forms\SignInFormFactory;

class SignPresenter extends Nette\Application\UI\Presenter
{
    private $authenticator;
    private $signInFormFactory;


    public function __construct(Nette\Security\Authenticator $authenticator, SignInFormFactory $signInFormFactory)
    {
        $this->authenticator = $authenticator;
        $this->signInFormFactory = $signInFormFactory;
    }

    protected function createComponentSignInForm(): Form
    {
        $form = $this->signInFormFactory->create();

        $form->onSuccess[] = function (Form $form, \stdClass $values): void {
            $this->signInFormSucceeded($form, $values);
        };

        return $form;
    }
    public function signInFormSucceeded(Form $form, \stdClass $values): void
    {
        try {
            $this->getUser()->login($values->username, $values->password);
            $this->redirect('Home:default');
        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError('Incorrect username or password.');
        }
    }
}
