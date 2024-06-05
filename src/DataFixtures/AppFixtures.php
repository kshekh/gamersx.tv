<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }

    protected function homeRows(): void
    {
//        $homeRows = [
//            ['title' => 'FullWidthDescriptive', 'sort_index' => 0, 'layout' => 'FullWidthDescriptive', 'options' => ]
//        ];
    }
}
