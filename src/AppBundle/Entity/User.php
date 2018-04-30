<?php

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity(fields={"email"}, message="It looks like you already have an account!")
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
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * we'll set the plain password on the user and encode it automatically via a Doctrine listener when it saves.
     * we are not going to persist this with Doctrine - never store plain text passwords
     * this is just a temporary storage place during a request
     *
     * we need the following assertion to only apply to the registration form.
     * when the user wants to update their user info, they may want to keep using the same password :)
     * @Assert\NotBlank(groups={"Registration"})
     */
    private $plainPassword;

    /**
     * The roles property will hold an array of roles.
     * When we save, Doctrine will automatically json_encode that array and store it in a singe field.
     * When we query, it will json_decode that back to the array.
     * Every user must have at least one role.
     *
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $avatarUri;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isScientist = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $universityName;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\GenusScientist", mappedBy="user")
     */
    private $studiedGenuses;

    public function __construct()
    {
        $this->studiedGenuses = new ArrayCollection();
    }

    public function getRoles()
    {
        $roles = $this->roles;

        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        return $roles;
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

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getIsScientist() : bool
    {
        return $this->isScientist;
    }

    public function setIsScientist(bool $iScientist = false): void
    {
        $this->isScientist = $iScientist;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName = null): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName = null): void
    {
        $this->lastName = $lastName;
    }

    public function getAvatarUri()
    {
        return $this->avatarUri;
    }

    public function setAvatarUri(string $avatarUri = null): void
    {
        $this->avatarUri = $avatarUri;
    }

    public function getUniversityName()
    {
        return $this->universityName;
    }

    public function setUniversityName($universityName): void
    {
        $this->universityName = $universityName;
    }

    public function getFullName()
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection|GenusScientist[]
     */
    public function getStudiedGenuses()
    {
        return $this->studiedGenuses;
    }
}
