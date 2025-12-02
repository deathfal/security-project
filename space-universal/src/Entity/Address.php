<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $street = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 255)]
    private ?string $planet = null;

    #[ORM\Column(length: 255)]
    private ?string $galaxy = null;

    #[ORM\Column(length: 255)]
    private ?string $country = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'billingAddress')]
    private Collection $billingOrders;

    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'shippingAddress')]
    private Collection $shippingOrders;

    #[ORM\OneToOne(inversedBy: 'address', targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    public function __construct()
    {
        $this->billingOrders = new ArrayCollection();
        $this->shippingOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getPlanet(): ?string
    {
        return $this->planet;
    }

    public function setPlanet(string $planet): static
    {
        $this->planet = $planet;

        return $this;
    }

    public function getGalaxy(): ?string
    {
        return $this->galaxy;
    }

    public function setGalaxy(string $galaxy): static
    {
        $this->galaxy = $galaxy;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getBillingOrders(): Collection
    {
        return $this->billingOrders;
    }

    public function addBillingOrder(Order $order): static
    {
        if (!$this->billingOrders->contains($order)) {
            $this->billingOrders->add($order);
            $order->setBillingAddress($this);
        }

        return $this;
    }

    public function removeBillingOrder(Order $order): static
    {
        if ($this->billingOrders->removeElement($order)) {
            if ($order->getBillingAddress() === $this) {
                $order->setBillingAddress(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getShippingOrders(): Collection
    {
        return $this->shippingOrders;
    }

    public function addShippingOrder(Order $order): static
    {
        if (!$this->shippingOrders->contains($order)) {
            $this->shippingOrders->add($order);
            $order->setShippingAddress($this);
        }

        return $this;
    }

    public function removeShippingOrder(Order $order): static
    {
        if ($this->shippingOrders->removeElement($order)) {
            if ($order->getShippingAddress() === $this) {
                $order->setShippingAddress(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
