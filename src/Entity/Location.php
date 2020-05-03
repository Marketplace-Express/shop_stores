<?php
/**
 * User: Wajdi Jurry
 * Date: ٢‏/٥‏/٢٠٢٠
 * Time: ٢:٣٥ ص
 */

namespace App\Entity;


use App\Entity\Interfaces\ApiArrayData;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="locations")
 */
class Location implements ApiArrayData
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36)
     */
    private $locationId;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $coordinates;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $city;

    /**
     * @return mixed
     */
    public function getLocationId()
    {
        return $this->locationId;
    }

    public function getCoordinates(): ?string
    {
        return $this->coordinates;
    }

    public function setCoordinates(array $coordinates = []): self
    {
        $this->coordinates = implode(", ", $coordinates);

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function toApiArray(): array
    {
        return [
            'coordinates' => $this->coordinates,
            'country' => $this->country,
            'city' => $this->city
        ];
    }
}
