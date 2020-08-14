<?php
/**
 * User: Wajdi Jurry
 * Date: ٧‏/٩‏/٢٠١٩
 * Time: ١٢:٣٦ م
 */

namespace App\Entity\Sort;

/**
 * Class SortStore
 * @package App\Entity\Sort
 */
class SortStore
{
    const ORDERING =  [
        1 => 'ASC',
        -1 => 'DESC'
    ];

    const FIELD_MAPPING = [
        'id' => 'storeId',
        'name' => 'name',
        'createdAt' => 'createdAt'
    ];

    /** @var string */
    public $id;

    /** @var int */
    public $name;

    /** @var int */
    public $createdAt;

    /** @var array */
    private $sorting = [];

    /**
     * SortStore constructor.
     * @param string $data
     */
    public function __construct(?string $data)
    {
        $data = json_decode($data, true);
        if ($data) {
            foreach ($data as $attribute => $order) {
                if (property_exists($this, $attribute)) {
                    $this->$attribute = $order;
                }
            }
        }
    }

    /**
     * @return array
     */
    protected function prepareSorting(): array
    {
        foreach (self::FIELD_MAPPING as $field => $modelAttribute) {
            if ($this->$field) {
                $this->sorting[$modelAttribute] = $this->$field;
            }
        }

        $this->sorting = array_filter($this->sorting, function ($attribute) {
            return !empty($attribute);
        });

        return $this->sorting;
    }

    /**
     * @return array
     */
    protected function prepareDirection(): array
    {
        foreach ($this->sorting as $sortBy => $order) {
            $this->sorting[$sortBy] = self::ORDERING[$order];
        }

        return $this->sorting;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getSqlSort(): array
    {
        $this->prepareSorting();
        $this->prepareDirection();
        if (empty($this->sorting)) {
            throw new \Exception('Invalid sorting arguments', 400);
        }
        return $this->sorting;
    }
}
