<?php

declare(strict_types=1);

namespace App\Model\Dealer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Model\Cpanel\Entity\Manager;
use App\Model\Cpanel\Entity\CategoryDealer;

/**
 * @ORM\Entity(repositoryClass="App\Model\Dealer\Repository\DealerRepository")
 */
class Dealer implements UserInterface, EquatableInterface
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128,  unique=true)
     * @Groups("login")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Groups("login")
     */
    private $password;

    /**
     * @var CategoryDealer|null
     * @ORM\ManyToOne(targetEntity="App\Model\Cpanel\Entity\CategoryDealer", inversedBy="dealers")
     * @ORM\JoinColumn(name="category_dealer_id", referencedColumnName="id", onDelete="SET NULL")
     *
     */
    private $category;

    /**
     * @var Manager|null
     * @ORM\ManyToOne(targetEntity="App\Model\Cpanel\Entity\Manager")
     * @ORM\JoinColumn(name="manager_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $manager;

    /**
     * @var InformationDealer|null
     * @ORM\Embedded(class="InformationDealer", columnPrefix="information_")
     */
    private $information;

    /**
     * @var RequestData|null
     * @ORM\Embedded(class="RequestData", columnPrefix="request_")
     */
    private $request;

    /**
     * @var ResetToken|null
     * @ORM\Embedded(class="ResetToken", columnPrefix="reset_")
     */
    private $resetToken;

    /**
     * @var Status|null
     * @ORM\Embedded(class="Status", columnPrefix="moderation_")
     */
    private $status;

    /**
     * @var ArrayCollection[]
     * @ORM\OneToMany(targetEntity="App\Model\Ticket\Entity\Ticket\Ticket", mappedBy ="author", cascade={"remove"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="ticket", referencedColumnName="id")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $tickets;

    /**
     * @var ArrayCollection[]
     * @ORM\OneToMany(targetEntity="App\Model\Salon\Entity\Owners", mappedBy="dealer", cascade={"remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="dealer_id")
     */
    private $salons;

    /**
     * @var Mailer|null
     * @ORM\Embedded(class="Mailer", columnPrefix="subscribe_")
     */
    private $mailer;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->salons = new ArrayCollection();
        $this->mailer = new Mailer();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function notification(): ?Mailer
    {
        return $this->mailer;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function changeEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function changePassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getResetToken(): ?ResetToken
    {
        return $this->resetToken;
    }

    public function moderation(): ?Status
    {
        return $this->status;
    }

    public function request(): ?RequestData
    {
        return $this->request;
    }

    public function info(): ?InformationDealer
    {
        return $this->information;
    }

    public function getShowName(): string
    {
        return $this->information->getName() ?? $this->getEmail();
    }

    public function getUsername(): string
    {
        return $this->getEmail();
    }

    public function getRoles(): array
    {
        return ['ROLE_DEALER'];
    }

    public function getCategory(): ?CategoryDealer
    {
        return $this->category;
    }

    public function setCategory(CategoryDealer $category = null): self
    {
        $this->category = $category;
        return $this;
    }

    public function getManager(): ?Manager
    {
        return $this->manager;
    }

    public function assignManager(Manager $manager = null): self
    {
        $this->manager = $manager;
        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {

    }

    public static function create(string $email): self
    {
        $dealer = new self();
        $dealer->email = $email;
        $dealer->information = new InformationDealer();
        $dealer->request = new RequestData();
        $dealer->status = new Status(Status::STATUS_WAIT);
        $dealer->setCategory();
        $dealer->assignManager();

        return $dealer;
    }


    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof Dealer) {
            return false;
        }

        if ($user->moderation()->isBlocked()) {
            return false;
        }

        if ($user->moderation()->isWait()) {
            return false;
        }

        return true;
    }

    public function requestPasswordReset(ResetToken $token, \DateTimeImmutable $date): void
    {
        if ($this->moderation()->isBlocked()) {
            throw new \DomainException('dealer.blocked');
        }
        if ($this->moderation()->isWait()) {
            throw new \DomainException('dealer.moderation');
        }
        if ($this->resetToken && !$this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('reset.request.repeat');
        }
        $this->resetToken = $token;
    }

    public function passwordReset(\DateTimeImmutable $date, string $hash): void
    {
        if (!$this->resetToken) {
            throw new \DomainException('reset.not.request');
        }
        if ($this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('reset.token.expired');
        }
        $this->changePassword($hash);
        $this->resetToken = null;
    }

    public function getSalons()
    {
        return $this->salons;
    }
}
