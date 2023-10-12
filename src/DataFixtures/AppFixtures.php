<?php

namespace App\DataFixtures;

use App\Entity\Comments;
use App\Entity\FigureGroup;
use App\Entity\Media;
use App\Entity\Tricks;
use App\Entity\Users;
use App\Entity\Videos;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('FR-fr');

        $users = [];
        $group = [];
        $FigureGroupNames = ['Grabs', 'Rotations', 'Flips', 'Rotations désaxées', 'Slides', 'One foot', 'Old school'];
        $tricksNames = ['Mute', 'Indy', '360', '720', 'Backflip', 'Misty', 'Tail slide', 'Method air', 'Backside air'];



        for ($i = 1; $i <= 5; $i++) {
            $user = new Users();
            $user->setUsername($faker->Username)
                ->setEmail($faker->safeEmail)
                ->setPassword($this->userPasswordHasher->hashPassword($user, 'password'))
                ->setPhoto("defaultuser.png")
                ->setIsVerified(true);

            $manager->persist($user);
            $users[] = $user;
        }

        foreach ($FigureGroupNames as $FigureGroupName) {
            $FigureGroup = new FigureGroup();
            $FigureGroup->setName($FigureGroupName);

            $manager->persist($FigureGroup);
            $group[] = $FigureGroup;
        }

        foreach ($tricksNames as $trickName) {
            $img = new Media();
            $img->setSource("fixtures1.jpg");

            $img2 = new Media();
            $img2->setSource("fixtures2.jpg");

            $video = new Videos();
            $video->setSource('<iframe width="560" height="315" src="https://www.youtube.com/embed/T92n4e5bEpE?si=HgzwaDygpZTlGf9A" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>');

            $slugger = new AsciiSlugger();
            $slug = $slugger->slug($trickName);


            $trick = new Tricks();
            $trick->setName($trickName)
                ->setFigureGroup($faker->randomElement($group))
                ->setDescription($faker->paragraph(5))
                ->setMainMedia("DefaultMainTrick.jpg")
                ->addMedium($img)
                ->addMedium($img2)
                ->addVideos($video)
                ->setSlug($slug)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setModifiedAt(new \DateTimeImmutable())
                ->setUsers($faker->randomElement($users));

            $manager->persist($trick);

            for ($j = 1; $j < mt_rand(0, 30); $j++) {
                $comment = new Comments();
                $comment->setUsers($faker->randomElement($users))
                    ->setTricks($trick)
                    ->setContent($faker->sentence(mt_rand(1, 5)))
                    ->setCreatedAt(new \DateTimeImmutable());

                $manager->persist($comment);
            }
        }


        $manager->flush();
    }
}
