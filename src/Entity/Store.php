<?php
/**
 * User: Wajdi Jurry
 * Date: 24/04/2020
 * Time: ٢:٣٥ ص
 */

namespace App\Entity;


use App\Entity\Filter\Disable\Traits\DisableTrait;
use App\Entity\Interfaces\ApiArrayData;
use App\Entity\Interfaces\DisableInterface;
use App\Enums\DisableReasonEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StoreRepository")
 * @ORM\Table(name="stores")
 *
 * @Gedmo\SoftDeleteable()
 */
class Store implements ApiArrayData, DisableInterface
{
    use TimestampableEntity, SoftDeleteableEntity, DisableTrait;

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

    /**
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    private $disableReason;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $disableComment;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Follower",
     *     mappedBy="store",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     */
    private $followers;

    /**
     * @var string
     * @ORM\Column(type="string", length=36)
     */
    private $deletionToken = 'N/A';

    /**
     * Store constructor.
     */
    public function __construct()
    {
        $this->followers = new ArrayCollection();
    }

    /**
     * @param string $storeId
     * For unit testing purposes only
     */
    public function setStoreId(string $storeId)
    {
        $this->storeId = $storeId;
    }

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
     * @return int|null
     */
    public function getDisableReason(): ?int
    {
        return $this->disableReason;
    }

    /**
     * @param int $disableReason
     */
    public function setDisableReason(int $disableReason): void
    {
        $this->disableReason = $disableReason;
    }

    /**
     * @return string|null
     */
    public function getDisableComment(): ?string
    {
        return $this->disableComment;
    }

    /**
     * @param string $disableComment
     */
    public function setDisableComment(string $disableComment): void
    {
        $this->disableComment = $disableComment;
    }

    /**
     * @param string $deletionToken
     */
    public function setDeletionToken(string $deletionToken): void
    {
        $this->deletionToken = $deletionToken;
    }

    private function locationToApiArray(): array
    {
        if ($this->location) {
            return $this->location->toApiArray();
        }

        return [];
    }

    /**
     * @return Follower[]|Collection
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(Follower $follower): self
    {
        if (!$this->followers->contains($follower)) {
            $this->followers[] = $follower;
            $follower->setStore($this);
            $this->followersCount++;
        }

        return $this;
    }

    public function removeFollower(Follower $follower): self
    {
        if ($this->followers->contains($follower)) {
            $this->followers->removeElement($follower);
            // set the owning side to null (unless already changed)
            if ($follower->getStore()->storeId === $this->storeId) {
                $follower->setStore(null);
            }
            $this->followersCount--;
        }

        return $this;
    }

    private function getDisableData(): array
    {
        return [
            'disabledAt' => $this->disabledAt,
            'disableReason' => DisableReasonEnum::getKey($this->disableReason),
            'disableComment' => $this->disableComment
        ];
    }

    /**
     * @return array
     */
    public function toApiArray(): array
    {
        return array_merge(
            [
                'storeId' => $this->storeId,
                'ownerId' => $this->ownerId,
                'name' => $this->name,
                'description' => $this->description,
                'location' => $this->locationToApiArray(),
                'productsCount' => $this->productsCount,
                'followersCount' => $this->followersCount,
                'ordersCount' => $this->ordersCount,
                'photo' => $this->photo,
                'coverPhoto' => $this->coverPhoto,
                'createdAt' => $this->createdAt
            ],
            $this->isDisabled() ? $this->getDisableData() : []
        );
    }
}
