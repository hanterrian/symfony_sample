<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Conference;
use App\Repository\ConferenceRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function __construct(private readonly ConferenceRepository $conferenceRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i <= 10; $i++) {
            $conference = new Conference();

            $conference->setCity($faker->city);
            $conference->setYear($faker->year);
            $conference->setIsInternational($faker->boolean());

            $manager->persist($conference);
        }

        $manager->flush();

        for ($i = 0; $i <= 100; $i++) {
            $comment = new Comment();

            $comment->setConference($this->conferenceRepository->getRandomField());
            $comment->setAuthor($faker->firstName);
            $comment->setEmail($faker->email);
            $comment->setText($faker->realText());
            $comment->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime));

            $manager->persist($comment);
        }

        $manager->flush();
    }
}
