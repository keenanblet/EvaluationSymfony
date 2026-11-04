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
        $users = [];
        foreach ([
            ['Martin', 'Claire', 'claire.martin@artderue.fr'],
            ['Durand', 'Hugo', 'hugo.durand@artderue.fr'],
            ['Petit', 'Lina', 'lina.petit@artderue.fr'],
        ] as [$nom, $prenom, $email]) {
            $user = (new User())
                ->setNom($nom)
                ->setPrenom($prenom)
                ->setEmail($email);
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
            ['Nova', 'Camille Roux', 'Graffeuse parisienne connue pour ses portraits aux couleurs vives.', 'Française'],
            ['K-Tag', null, 'Spécialiste du lettrage et du wildstyle depuis les années 2000.', 'Belge'],
            ['Miss Pochoir', 'Julie Bernard', 'Artiste au pochoir, ses œuvres engagées questionnent la société.', 'Française'],
            ['Tesselle', 'Marco Bianchi', 'Mosaïste urbain qui pose des carreaux pixelisés dans toute l\'Europe.', 'Italienne'],
            ['Urbano', 'Diego Alvarez', 'Muraliste réalisant des fresques monumentales.', 'Espagnole'],
        ] as [$pseudo, $nomReel, $bio, $nationalite]) {
            $artiste = (new Artiste())
                ->setPseudonyme($pseudo)
                ->setNomReel($nomReel)
                ->setBio($bio)
                ->setNationalite($nationalite);
            $manager->persist($artiste);
            $artistes[] = $artiste;
        }

        // Oeuvres : [titre, description, date création, date destruction, oeuvre recouverte (index), lieu, artistes, techniques]
        $oeuvres = [];
        foreach ([
            ['Regard de Belleville', 'Grand portrait féminin aux tons bleus et orangés.', '2021-04-12', null, null, 0, [0], ['Bombe aérosol']],
            ['Le Cri du Bitume', 'Lettrage wildstyle sur toute la longueur du mur.', '2019-07-03', '2023-02-15', null, 1, [1], ['Bombe aérosol']],
            ['Liberté conditionnelle', 'Pochoir d\'un oiseau s\'échappant d\'une cage.', '2022-09-20', null, null, 0, [2], ['Pochoir']],
            ['Pixel Invader', 'Petit personnage en mosaïque posé en hauteur.', '2018-05-01', null, null, 2, [3], ['Mosaïque']],
            ['Méditerranée', 'Fresque monumentale représentant la mer et les pêcheurs.', '2020-06-15', null, null, 3, [4, 0], ['Fresque', 'Bombe aérosol']],
            ['Visages collés', 'Série de portraits en papier collé.', '2023-03-08', '2024-01-10', null, 4, [2], ['Collage']],
            ['Stickers en vrac', 'Mur recouvert de stickers de plusieurs artistes.', '2024-08-22', null, 1, 1, [1, 2], ['Sticker', 'Collage']],
        ] as [$titre, $description, $creation, $destruction, $recouvreIdx, $lieuIdx, $artisteIdx, $techNoms]) {
            $oeuvre = (new Oeuvre())
                ->setTitre($titre)
                ->setDescription($description)
                ->setDateCreation(new \DateTime($creation))
                ->setDateDestruction($destruction ? new \DateTime($destruction) : null)
                ->setRecouvre(null !== $recouvreIdx ? $oeuvres[$recouvreIdx] : null)
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
            ['fixture-regard-1.jpg', '2021-05-01 14:30', 'Intacte', 0, 0],
            ['fixture-regard-2.jpg', '2024-03-12 10:15', 'Légèrement dégradée', 0, 1],
            ['fixture-cri-1.jpg', '2019-08-10 18:00', 'Intacte', 1, 1],
            ['fixture-cri-2.jpg', '2023-02-20 09:45', 'Recouverte', 1, 2],
            ['fixture-liberte-1.jpg', '2022-10-02 16:20', 'Intacte', 2, 0],
            ['fixture-pixel-1.jpg', '2018-06-15 11:00', 'Intacte', 3, 2],
            ['fixture-mediterranee-1.jpg', '2020-07-01 19:30', 'Intacte', 4, 1],
            ['fixture-visages-1.jpg', '2023-04-01 13:10', 'Dégradée', 5, 0],
            ['fixture-stickers-1.jpg', '2024-09-05 17:45', 'Intacte', 6, 2],
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
