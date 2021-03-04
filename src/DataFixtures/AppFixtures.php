<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Client;
use App\Entity\User;
use App\Entity\Product;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{

    /**
     * @var UserPasswordEncoder
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $client = new Client();
        $client->setName('Client');
        $client->setPassword($this->encoder->encodePassword(
            $client,'client'));
        $manager->persist($client);

        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product->setName('Product '.($i+1));
            $product->setPrice(mt_rand(100, 600));
            $product->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.');
            $product->setTechSpecs(['cpu' => 'Qualcomm SnapDragon '.mt_rand(400, 500), 'ram' => mt_rand(1,8).'GB', 'rom' => mt_rand(32, 256).'GB', 'battery' => mt_rand(1000, 2000).' mAh']);
            $manager->persist($product);
        }

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setName('User '.($i+1));
            $user->setMail('user'.($i+1).'@mail.com');
            $user->setRegisteredAt(new \DateTime());
            $manager->persist($user);
        }

        $manager->flush();
    }
}
