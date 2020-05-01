<?php
/**
 * User: Wajdi Jurry
 * Date: ١‏/٥‏/٢٠٢٠
 * Time: ١٢:٥٩ ص
 */

namespace App\Entity\Filter\Disable\Traits;


trait DisableTrait
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $disabledAt;

    /**
     * @return \DateTime|null
     */
    public function getDisabledAt(): ?\DateTime
    {
        return $this->disabledAt;
    }

    /**
     * @param \DateTime $disabledAt
     * @return DisableTrait
     */
    public function setDisabledAt(\DateTime $disabledAt): self
    {
        $this->disabledAt = $disabledAt;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return null !== $this->disabledAt;
    }
}