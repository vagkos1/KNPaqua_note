<?php

namespace AppBundle\Entity;


use Symfony\Component\Security\Core\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    // we'll set the plain password on the user and encode it automatically via a Doctrine listener when it saves.
    // we are not going to persist this with Doctrine - never store plain text passwords
    // this is just a temporary storage place during a request
    private $plainPassword;

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getPassword()
    {
        return $this->password;
    }

    // not necessary since we'll use bcrypt, which comes with a built in mechanism to salt passwords.
    public function getSalt()
    {
    }

    public function getUsername()
    {
        return $this->email;
    }

    // Symfony calls this after logging in.
    //// minor security measure to prevent the plain password from accidentally getting saved.
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword($password): void
    {
        $this->password = $password;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword): void
    {
        $this->plainPassword = $plainPassword;

        // guarantees that the User entity looks "dirty" to Doctrine
        // when changing the plainPassword (since we are not saving the plain password in the DB)
        $this->password = null;
    }


}
