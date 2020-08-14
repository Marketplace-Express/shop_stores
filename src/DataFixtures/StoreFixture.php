<?php

namespace App\DataFixtures;

use App\Entity\Location;
use App\Entity\Store;
use App\Enums\StoreType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StoreFixture extends Fixture
{
    const STORE_REFERENCE = 'store-reference';
    const MAX_NUMBER_OF_STORES = 5;

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < self::MAX_NUMBER_OF_STORES; $i++) {
            $location = new Location();
            $location->setCity($faker->city);
            $location->setCoordinates([substr($faker->longitude, 0, 4), substr($faker->latitude, 0, 4)]);
            $location->setCountry($faker->country);

            $store = new Store();
            $store->setName($faker->name . " Store");
            $store->setDescription($faker->text);
            $store->setLocation($location);
            $store->setOwnerId($faker->uuid);
            $store->setType($faker->randomKey(StoreType::getValues()));
            $store->setPhoto($faker->imageUrl());
            $store->setCoverPhoto($faker->imageUrl());

            $manager->persist($store);
            $manager->flush();

            $this->addReference(self::STORE_REFERENCE . "-" . $i, $store);
        }
    }
}
