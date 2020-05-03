<?php

namespace App\Entity;

use App\Entity\Interfaces\ApiArrayData;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FollowerRepository")
 * @ORM\Table(name="followers")
 */
class Follower implements ApiArrayData
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Store
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Store", inversedBy="followers")
     * @ORM\JoinColumn(name="store_id", referencedColumnName="store_id", nullable=false)
     */
    private $store;

    /**
     * @ORM\Column(type="string", length=36)
     */
    private $followerId;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $followedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function setStore(?Store $storeId): self
    {
        $this->store = $storeId;

        return $this;
    }

    public function getFollowerId(): ?string
    {
        return $this->followerId;
    }

    public function setFollowerId(string $followerId): self
    {
        $this->followerId = $followerId;

        return $this;
    }

    public function getFollowedAt(): ?\DateTimeInterface
    {
        return $this->followedAt;
    }

    public function setFollowedAt(\DateTimeInterface $followedAt): self
    {
        $this->followedAt = $followedAt;

        return $this;
    }

    /**
     * @return array
     */
    public function toApiArray(): array
    {
        return [
            'followerId' => $this->followerId
        ];
    }
}
