<?php

namespace App\Security\User;

use Hslavich\OneloginSamlBundle\Security\User\SamlUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements SamlUserInterface
{
    /* The username displayed */
    private $username;

    /* Provide UUID instead of normal password */
    private $password;

    /* The name of the user */
    private $name;

    /* The roles of the user */
    private $roles;

    /* Leave empty! */
    private $salt;

    /* Always true */
    private $isActive;

    public function __construct(string $username = '',  string $name = '', string $salt = null, $roles = [], $password = '')
    {
        $this->username = $username;
        $this->name = $name;
        $this->salt = $salt;
        $this->isActive = true;
        $this->password = $password;
        $this->roles = $roles;

    }

    public function __toString()
    {
        return $this->name;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getName()
    {
        return $this->name;
    }


    public function isEnabled()
    {
        return $this->isActive;
    }

    public function eraseCredentials()
    {
    }

    // serialize and unserialize must be updated - see below
    public function serialize()
    {
        return serialize([
            $this->username
            // see section on salt below
            // $this->salt,
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->username) = unserialize($serialized);
    }

    public function isEqualTo(UserInterface $user)
    {
        return true;
    }

    public function setSamlAttributes(array $attributes)
    {
        $this->email = $attributes['mail'][0];
        $this->name = $attributes['givenName'];
    }
}
