<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class)
     */
    private $userRoles;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="author")
     */
    private $sendedMessages;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="recipient")
     */
    private $receivedMessages;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->status = 1;
        $this->sendedMessage = new ArrayCollection();
        $this->receivedMessage = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * Attention, la méthode getRoles() est spécifique.
     * Elle est utilisée lorque mon utilisateur se connecte,
     * pour que Symfony sache quels rôles sont associés à mon user en cours de connexion pour déclencher les ACL
     * 
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->userRoles;

        $userRoles = [];

        foreach ($roles as $role) {
            $userRoles[] = $role->getName();
        }

        return $userRoles;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Message[]
     */
    public function getSendedMessages(): Collection
    {
        return $this->sendedMessages;
    }

    public function addSendedMessage(Message $sendedMessage): self
    {
        if (!$this->sendedMessages->contains($sendedMessage)) {
            $this->sendedMessages[] = $sendedMessage;
            $sendedMessage->setAuthor($this);
        }

        return $this;
    }

    public function removeSendedMessage(Message $sendedMessage): self
    {
        if ($this->sendedMessages->contains($sendedMessage)) {
            $this->sendedMessages->removeElement($sendedMessage);
            // set the owning side to null (unless already changed)
            if ($sendedMessage->getAuthor() === $this) {
                $sendedMessage->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getReceivedMessages(): Collection
    {
        return $this->ReceivedMessages;
    }

    public function addReceivedMessage(Message $ReceivedMessage): self
    {
        if (!$this->ReceivedMessages->contains($ReceivedMessage)) {
            $this->ReceivedMessages[] = $ReceivedMessage;
            $ReceivedMessage->setRecipient($this);
        }

        return $this;
    }

    public function removeReceivedMessage(Message $ReceivedMessage): self
    {
        if ($this->ReceivedMessages->contains($ReceivedMessage)) {
            $this->ReceivedMessages->removeElement($ReceivedMessage);
            // set the owning side to null (unless already changed)
            if ($ReceivedMessage->getRecipient() === $this) {
                $ReceivedMessage->setRecipient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Role[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(Role $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
        }

        return $this;
    }

    public function removeUserRole(Role $userRole): self
    {
        if ($this->userRoles->contains($userRole)) {
            $this->userRoles->removeElement($userRole);
        }

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

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
