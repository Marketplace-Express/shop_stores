<?php

namespace App\Entity;


use App\Traits\TimeStampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VendorRepository")
 * @ORM\Table(name="vendors")
 */
class Vendor
{
    use TimeStampable;

    /**
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(name="vendor_id", type="string", length=36)
     * @ORM\Id()
     */
    private $vendorId;

    /**
     * @ORM\Column(name="owner_id", type="string", length=36)
     */
    private $ownerId;

    /**
     * @return string|null
     */
    public function getVendorId(): ?string
    {
        return $this->vendorId;
    }

    /**
     * @return string|null
     */
    public function getOwnerId(): ?string
    {
        return $this->ownerId;
    }

    /**
     * @param string $ownerId
     * @return $this
     */
    public function setOwnerId(string $ownerId): self
    {
        $this->ownerId = $ownerId;

        return $this;
    }
}
