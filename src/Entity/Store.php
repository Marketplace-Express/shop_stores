<?php

namespace App\Entity;

use App\Entity\Interfaces\ArrayData;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StoreRepository")
 * @ORM\Table(name="stores")
 * @Gedmo\SoftDeleteable()
 */
class Store implements ArrayData
{
    use TimestampableEntity, SoftDeleteableEntity;

    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $storeId;

    /**
     * @var string
     *
     * @ORM\Column(type="guid")
     */
    private $ownerId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @var Location
     * @ORM\OneToOne(
     *     targetEntity="Location",
     *     cascade={"persist", "remove"}
     * )
     * @ORM\JoinColumn(name="location_id", referencedColumnName="location_id")
     */
    private $location;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $productsCount = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $followersCount = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $ordersCount = 0;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $photo;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $coverPhoto;

    public function getStoreId(): string
    {
        return $this->storeId;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function setOwnerId(string $ownerId): self
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getProductsCount(): int
    {
        return $this->productsCount;
    }

    public function setProductsCount(int $productsCount): self
    {
        $this->productsCount = $productsCount;

        return $this;
    }

    public function getFollowersCount(): int
    {
        return $this->followersCount;
    }

    public function setFollowersCount(int $followersCount): self
    {
        $this->followersCount = $followersCount;

        return $this;
    }

    public function getOrdersCount(): int
    {
        return $this->ordersCount;
    }

    public function setOrdersCount(int $ordersCount): self
    {
        $this->ordersCount = $ordersCount;

        return $this;
    }


    public function getPhoto(): string
    {
        return $this->photo;
    }


    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }


    public function getCoverPhoto(): string
    {
        return $this->coverPhoto;
    }


    public function setCoverPhoto(string $coverPhoto): self
    {
        $this->coverPhoto = $coverPhoto;

        return $this;
    }

    /**
     * @return array
     */
    public function toApiArray(): array
    {
        return [
            'storeId' => $this->storeId,
            'ownerId' => $this->ownerId,
            'name' => $this->name,
            'description' => $this->description,
            'location' => $this->location->toApiArray(),
            'productsCount' => $this->productsCount,
            'followersCount' => $this->followersCount,
            'ordersCount' => $this->ordersCount,
            'photo' => $this->photo,
            'coverPhoto' => $this->coverPhoto,
            'createdAt' => $this->createdAt
        ];
    }
}
