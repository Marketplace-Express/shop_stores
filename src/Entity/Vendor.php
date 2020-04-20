<?php

namespace App\Entity;


use App\Entity\Interfaces\ArrayData;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\Timestampable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VendorRepository")
 * @ORM\Table(name="vendors")
 * @Gedmo\SoftDeleteable()
 */
class Vendor implements ArrayData
{
    use Timestampable, SoftDeleteableEntity;

    /**
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36)
     * @ORM\Id()
     */
    protected $vendorId;

    /**
     * @ORM\Column(type="string", length=36)
     */
    protected $ownerId;

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

    /**
     * @return array
     */
    public function toApiArray(): array
    {
        return [
            'vendorId' => $this->vendorId,
            'ownerId' => $this->ownerId
        ];
    }
}
