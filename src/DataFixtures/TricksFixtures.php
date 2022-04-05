<?php

namespace App\DataFixtures;

use App\Entity\Comments;
use App\Entity\Media;
use App\Entity\Tricks;
use App\Entity\Users;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TricksFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 2; $i++) {
            $user = new Users();
            $user->setUsername("user $i")
                ->setEmail("user$i@gmail.com")
                ->setPassword("passworduser$i")
                ->setPhoto("{{ asset('uploads/avatars/defaultuser.jpg') }}");

            $manager->persist($user);


            for ($j = 1; $j <= 6; $j++) {
                $img = new Media();
                $img->setSource("{{ asset('uploads/tricks/fixtures2.jpg') }}");

                $trick = new Tricks();
                $trick->setName("Figure $j, ajoutée par l'utilisateur $i")
                    ->setFigureGroup("Grab")
                    ->setDescription("Description de la figure n°$j")
                    ->setMainMedia("{{ asset('uploads/mainmediatricks/fixtures1.jpg') }}")
                    ->addMedium($img)
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setModifiedAt(new \DateTimeImmutable())
                    ->setUsers($user);

                $manager->persist($trick);

                for ($k = 1; $k <= 3; $k++) {
                    $comment = new Comments();
                    $comment->setUsers($user)
                        ->setTricks($trick)
                        ->setContent("comment n°$k")
                        ->setCreatedAt(new \DateTimeImmutable());

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
