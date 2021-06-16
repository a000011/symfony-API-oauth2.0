<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Group;
use App\Entity\BaseEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface, UserEntityInterface
{
    use EntityTrait;


//TODO: сделать нормальные поля
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     */
    private $roles;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;
    
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Length(
     *      min = 2,
     *      max = 20,
     *      minMessage = "Your firstname must be at least {{ limit }} characters long",
     *      maxMessage = "Your firstname cannot be longer than {{ limit }} characters"
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Length(
     *      min = 2,
     *      max = 20,
     *      minMessage = "Your lastname must be at least {{ limit }} characters long",
     *      maxMessage = "Your lastname cannot be longer than {{ limit }} characters"
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Length(
     *      min = 2,
     *      max = 20,
     *      minMessage = "Your username must be at least {{ limit }} characters long",
     *      maxMessage = "Your username cannot be longer than {{ limit }} characters"
     * )
     */
    private $username;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $Group;



    public function setByArray(array $array): void
    {
        foreach (get_object_vars((object)$array)[0] as $key=> $value) {
            $this->$key=$value;//
            if($key=='created_at'){
                $this->createdAt = new DateTime($value);
            }
            if($key=='updated_at'){
                $this->updatedAt = new DateTime($value);
            }

        }

    }


    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->id;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = json_decode($this->roles);
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getGroup(): ?Group
    {
        return $this->Group;
    }

    public function setGroup(?Group $Group): self
    {
        $this->Group = $Group;

        return $this;
    }
}
