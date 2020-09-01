<?php

namespace App\DataFixtures;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends BaseFixture
{
    private $encoder;

    /**
     * Dans une classe (autre qu'un controlleur), on peut récupérer des services
     * par autowiring uniquement dans le constructeur
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    protected function loadData()
    {
        // Administrateurs
        $this->createMany(1, 'user_admin', function (int $num) {
            $admin = new User();
            $password = $this->encoder->encodePassword($admin, 'admin' . $num);

            return $admin
                ->setEmail('admin@events.fr')
                ->setRoles(['ROLE_ADMIN'])
                ->setPassword($password)
            ;
        });

        // Utilisateurs
        $this->createMany(10, 'user_user', function (int $num) {
            $user = new User();
            $password = $this->encoder->encodePassword($user, 'user' . $num);

            return $user
                ->setEmail('user' . $num . '@events.fr')
                ->setPassword($password)
            ;
        });
    }
}