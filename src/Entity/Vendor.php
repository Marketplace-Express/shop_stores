<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VendorRepository")
 */
class Vendor
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=36)
     */
    private $vendorId;

    /**
     * @ORM\Column(type="string", length=36)
     */
    private $ownerId;

    public function getVendorId(): ?string
    {
        return $this->vendorId;
    }

    public function setVendorId(string $vendorId): self
    {
        $this->vendorId = $vendorId;

        return $this;
    }

    public function getOwnerId(): ?string
    {
        return $this->ownerId;
    }

    public function setOwnerId(string $ownerId): self
    {
        $this->ownerId = $ownerId;

        return $this;
    }
}
