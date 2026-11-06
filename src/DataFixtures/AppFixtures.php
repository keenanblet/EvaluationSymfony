<?php

namespace App\DataFixtures;

use App\Entity\Artiste;
use App\Entity\Lieu;
use App\Entity\Oeuvre;
use App\Entity\Photos;
use App\Entity\Technique;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Utilisateurs (mot de passe : "password")
        // Comptes de démo pour tester chaque niveau d'accès :
        //   user@artderue.fr     -> ROLE_USER (consultation)
        //   benevole@artderue.fr -> ROLE_BENEVOLE (ajout d'oeuvres)
        //   admin@artderue.fr    -> ROLE_ADMIN (tout le back-office)
        $users = [];
        foreach ([
            ['Martin', 'Claire', 'claire.martin@artderue.fr', []],
            ['Durand', 'Hugo', 'hugo.durand@artderue.fr', []],
            ['Petit', 'Lina', 'lina.petit@artderue.fr', []],
            ['Utilisateur', 'Demo', 'user@artderue.fr', []],
            ['Benevole', 'Demo', 'benevole@artderue.fr', ['ROLE_BENEVOLE']],
            ['Admin', 'Demo', 'admin@artderue.fr', ['ROLE_ADMIN']],
        ] as [$nom, $prenom, $email, $roles]) {
            $user = (new User())
                ->setNom($nom)
                ->setPrenom($prenom)
                ->setEmail($email)
                ->setRoles($roles);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $manager->persist($user);
            $users[] = $user;
        }

        // Techniques
        $techniques = [];
        foreach (['Bombe aérosol', 'Pochoir', 'Collage', 'Mosaïque', 'Fresque', 'Sticker'] as $nom) {
            $technique = (new Technique())->setNom($nom);
            $manager->persist($technique);
            $techniques[$nom] = $technique;
        }

        // Lieux
        $lieux = [];
        foreach ([
            ['Mur de la rue Denoyez', '12 rue Denoyez, 75020 Paris', 'Belleville', 48.8714, 2.3780],
            ['Quai de la Gare', '5 quai d\'Austerlitz, 75013 Paris', 'Austerlitz', 48.8400, 2.3700],
            ['Place du Marché', '3 place du Marché, 69001 Lyon', 'Croix-Rousse', 45.7745, 4.8320],
            ['Cours Julien', '18 cours Julien, 13006 Marseille', 'Notre-Dame-du-Mont', 43.2936, 5.3836],
            ['Passage des Arts', '7 rue Sainte-Catherine, 33000 Bordeaux', null, 44.8378, -0.5732],
            ['Hangar à Bananes', '21 quai des Antilles, 44200 Nantes', 'Île de Nantes', 47.2007, -1.5730],
        ] as [$nom, $adresse, $quartier, $lat, $lng]) {
            $lieu = (new Lieu())
                ->setNom($nom)
                ->setAdresse($adresse)
                ->setQuartier($quartier)
                ->setLatitude($lat)
                ->setLongitude($lng);
            $manager->persist($lieu);
            $lieux[] = $lieu;
        }

        // Artistes
        $artistes = [];
        foreach ([
            ['Ador', null, 'Artiste nantais aux personnages burlesques et colorés.', 'Française'],
            ['Katra', null, 'Muraliste ayant cosigné plusieurs fresques avec Ador.', null],
        ] as [$pseudo, $nomReel, $bio, $nationalite]) {
            $artiste = (new Artiste())
                ->setPseudonyme($pseudo)
                ->setNomReel($nomReel)
                ->setBio($bio)
                ->setNationalite($nationalite);
            $manager->persist($artiste);
            $artistes[] = $artiste;
        }

        // Oeuvres : [titre, description, date création, date destruction, recouverte (oui/non), lieu, artistes, techniques]
        $oeuvres = [];
        foreach ([
            ['Fresque du Hangar à Bananes', 'Fresque monumentale sur un pignon : un personnage déguisé en banane, l\'air inquiet, fait face à un bonimenteur qui lui tend des bananes posées sur un guéridon.', null, null, false, 5, [0, 1], ['Fresque']],
        ] as [$titre, $description, $creation, $destruction, $recouverte, $lieuIdx, $artisteIdx, $techNoms]) {
            $oeuvre = (new Oeuvre())
                ->setTitre($titre)
                ->setDescription($description)
                ->setDateCreation($creation ? new \DateTime($creation) : null)
                ->setDateDestruction($destruction ? new \DateTime($destruction) : null)
                ->setOeuvreRecouverte($recouverte)
                ->setLieu($lieux[$lieuIdx]);
            foreach ($artisteIdx as $i) {
                $oeuvre->addArtiste($artistes[$i]);
            }
            foreach ($techNoms as $nom) {
                $oeuvre->addTechnique($techniques[$nom]);
            }
            $manager->persist($oeuvre);
            $oeuvres[] = $oeuvre;
        }

        // Photos : [fichier, date, état, oeuvre, user]
        foreach ([
            ['fixture-hangar-bananes-1.png', '2024-06-01 15:00', 'Intacte', 0, 4],
        ] as [$fichier, $date, $etat, $oeuvreIdx, $userIdx]) {
            $photo = (new Photos())
                ->setNomFichier($fichier)
                ->setDatePriseVue(new \DateTime($date))
                ->setEtatOeuvre($etat);
            $oeuvres[$oeuvreIdx]->addPhoto($photo);
            $users[$userIdx]->addPhoto($photo);
            $manager->persist($photo);
        }

        $manager->flush();
    }
}
