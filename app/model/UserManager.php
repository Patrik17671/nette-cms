<?php
namespace App\Model;

use Nette;
use Nette\Security as NS;

class UserManager implements NS\Authenticator
{
    use Nette\SmartObject;

    public function __construct(
        private Nette\Database\Explorer $database,
        private Nette\Security\Passwords $passwords,
    ) {
    }
    public function authenticate(string $username, string $password): NS\SimpleIdentity
    {
        $row = $this->database->table('users')
            ->where('username', $username)
            ->fetch();

        if (!$row) {
            throw new NS\AuthenticationException('User not found.');
        }

        if (!$this->passwords->verify($password, $row->password)) {
            throw new NS\AuthenticationException('Invalid password.');
        }
        return new NS\SimpleIdentity($row->id, $row->role, ['username' => $row->username]);
    }
}