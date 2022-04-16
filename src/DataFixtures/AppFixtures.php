<?php
// src/DataFixtures/AppFixtures.php


use App\Entity\Event;
use App\Entity\Group;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        define("NB_EVENTS", 5);

        $namesEvent = array('Session crossfit', 'Session course à pieds', 'Entraînement senior', 'Remise en forme post-partum', 'session cap', 'initiation musculation', 'entraînement tennis junior', 'session pétanque', 'tournoi ping-pong cycle des Bromont');

        for ($i = 0; $i < NB_EVENTS; $i++) {
            $event = new Event();
            $randomKeyEvent = array_rand($namesEvent, 1);
            $event->setName($namesEvent[$randomKeyEvent]);
            $event->setAdminLinkToken('linkToken'.$i);
            $event->setEmail('évenement'.$i.'@gmail.com');
            $manager->persist($event);
        }

        $namesGroup = array('Michel', 'Anne', 'Pierre-Noël', 'Jean', 'Thierry', 'Momo', 'Guilibert','Edmond', 'Benjamin', 'Marguerite', 'Armand', 'Shelton', 'Bakou','Jojo', 'Dylan', 'Dayana', 'Aimé', 'Noel', 'Philippe', 'Philou', 'Florentin', 'Malia', 'Sania', 'Anne-Marie');

        for ($i = 0; $i < 20; $i++) 
        {
            $group = new Group();
            $randomKeyName = array_rand($namesGroup, 1);
            $group->setName('Le groupe de '. $namesGroup[$randomKeyName]);

            $randomListTokenFromNbEvents = rand(0, NB_EVENTS);
            $group->setLinkToken('linkToken'.$randomListTokenFromNbEvents);
            $group->setLastArchived(new DateTime('now'));
            $manager->persist($group);
        }

        $manager->flush();
    }
}
