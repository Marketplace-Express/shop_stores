<?php

namespace App\Traits;


use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Trait TimeStampable
 * @package App\Traits
 */
trait TimeStampable
{
    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;
}