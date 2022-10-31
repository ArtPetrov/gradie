<?php

declare(strict_types=1);

namespace App\Model\Buyer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 */
class Buyer implements UserInterface, EquatableInterface
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Information
     * @ORM\Embedded(class="Information", columnPrefix="info_")
     * @Groups("login")
     */
    private $information;

    /**
     * @var ResetToken|null
     * @ORM\Embedded(class="ResetToken", columnPrefix="reset_")
     */
    private $resetToken;

    /**
     * @var BasketToken|null
     * @ORM\Embedded(class="BasketToken", columnPrefix="basket_")
     */
    private $basketToken;

    /**
     * @var Network[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Network", mappedBy="buyer", orphanRemoval=true, cascade={"persist"})
     */
    private $networks;

    /**
     * @ORM\OneToMany(targetEntity="App\Model\Review\Entity\Review", mappedBy="buyer", orphanRemoval=true, cascade={"persist","remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="buyer_id")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $reviews;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    private function __construct()
    {
        $this->networks = new ArrayCollection();
    }

    public static function signUpByEmail(?string $email, ?string $password, ?string $name = '', ?string $phone = ''): self
    {
        $buyer = new self();
        $buyer->updateInformation(new Information($email, $password, $name, $phone));
        return $buyer;
    }

    public static function signUpByNetwork(string $name, string $network, string $identity): self
    {
        $buyer = new self();
        $buyer->updateInformation(new Information(null, null, $name, null));
        $buyer->attachNetwork($network, $identity);
        return $buyer;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInformation(): Information
    {
        return $this->information;
    }

    public function initBasketToken(BasketToken $basketToken): self
    {
        $this->basketToken = $basketToken;
        return $this;
    }

    public function getBasketToken(): ?BasketToken
    {
        return $this->basketToken;
    }

    public function updateInformation(Information $information): self
    {
        $this->information = $information;
        return $this;
    }

    public function getResetToken(): ?ResetToken
    {
        return $this->resetToken;
    }

    public function getEmail(): ?string
    {
        return $this->getInformation()->getEmail();
    }

    public function getUsername(): ?string
    {
        return (string)$this->getId();
    }

    public function getRoles(): array
    {
        return ['ROLE_BUYER'];
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {

    }

    public function getPassword(): ?string
    {
        return $this->information->getPassword();
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof Buyer) {
            return false;
        }

        return true;
    }

    public function requestPasswordReset(ResetToken $token, \DateTimeImmutable $date): void
    {
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

        $this->getInformation()->changePassword($hash);
        $this->resetToken = null;
    }

    public function hasNetwork(string $network): bool
    {
        foreach ($this->networks as $existing) {
            if ($existing->isForNetwork($network)) {
                return true;
            }
        }
        return false;
    }

    public function findNetwork(string $network): ?Network
    {
        foreach ($this->networks as $existing) {
            if ($existing->isForNetwork($network)) {
                return $existing;
            }
        }
        return null;
    }

    public function attachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isForNetwork($network)) {
                throw new \DomainException('buyer.network.already.attached');
            }
        }
        $this->networks->add(new Network($this, $network, $identity));
    }

    public function detachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isFor($network, $identity)) {
                if (!$this->getInformation()->getEmail() && $this->networks->count() === 1) {
                    throw new \DomainException('buyer.network.detach.last.identity');
                }
                $this->networks->removeElement($existing);
                return;
            }
        }
        throw new \DomainException('buyer.network.not.attached');
    }

}

