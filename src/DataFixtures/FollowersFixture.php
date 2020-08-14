<?php
/**
 * User: Wajdi Jurry
 * Date: ١٢‏/٨‏/٢٠٢٠
 * Time: ٩:١٦ م
 */

namespace App\DataFixtures;


use App\Entity\Follower;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FollowersFixture extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $follower = new Follower();
            $follower->setFollowerId($faker->uuid);
            $follower->setStore(
                $this->getReference(StoreFixture::STORE_REFERENCE . "-" . $faker->numberBetween(1, StoreFixture::MAX_NUMBER_OF_STORES - 1))
            );

            $manager->persist($follower);
            $manager->flush();
        }
    }

    /**
     * @return mixed
     */
    public function getDependencies()
    {
        return [
            StoreFixture::class
        ];
    }
}