<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Oeuvre;
use App\Entity\Photos;
use App\Form\OeuvreType;
use App\Repository\LieuRepository;
use App\Repository\OeuvreRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/oeuvre')]
final class OeuvreController extends AbstractController
{
    #[Route(name: 'app_oeuvre_index', methods: ['GET'])]
    public function index(OeuvreRepository $oeuvreRepository): Response
    {
        return $this->render('oeuvre/index.html.twig', [
            'oeuvres' => $oeuvreRepository->findAll(),
        ]);
    }
    #[IsGranted('ROLE_BENEVOLE')]
    #[Route('/new', name: 'app_oeuvre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader, LieuRepository $lieuRepository): Response
    {
        $oeuvre = new Oeuvre();
        $form = $this->createForm(OeuvreType::class, $oeuvre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->rattacherLieuRecherche($form, $oeuvre, $lieuRepository, $entityManager);

            $fichier = $form->get('photo')->getData();
            if ($fichier) {
                $photo = (new Photos())
                    ->setNomFichier($fileUploader->upload($fichier))
                    ->setDatePriseVue(new \DateTime())
                    ->setEtatOeuvre('Intacte')
                    ->setUser($this->getUser());
                $oeuvre->addPhoto($photo);
                $entityManager->persist($photo);
            }

            $entityManager->persist($oeuvre);
            $entityManager->flush();

            return $this->redirectToRoute('app_oeuvre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('oeuvre/new.html.twig', [
            'oeuvre' => $oeuvre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_oeuvre_show', methods: ['GET'])]
    public function show(Oeuvre $oeuvre): Response
    {
        return $this->render('oeuvre/show.html.twig', [
            'oeuvre' => $oeuvre,
        ]);
    }
    #[IsGranted('ROLE_BENEVOLE')]
    #[Route('/{id}/edit', name: 'app_oeuvre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Oeuvre $oeuvre, EntityManagerInterface $entityManager, LieuRepository $lieuRepository): Response
    {
        $form = $this->createForm(OeuvreType::class, $oeuvre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->rattacherLieuRecherche($form, $oeuvre, $lieuRepository, $entityManager);
            $entityManager->flush();

            return $this->redirectToRoute('app_oeuvre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('oeuvre/edit.html.twig', [
            'oeuvre' => $oeuvre,
            'form' => $form,
        ]);
    }
    #[IsGranted('ROLE_BENEVOLE')]
    #[Route('/{id}', name: 'app_oeuvre_delete', methods: ['POST'])]
    public function delete(Request $request, Oeuvre $oeuvre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$oeuvre->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($oeuvre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_oeuvre_index', [], Response::HTTP_SEE_OTHER);
    }

    // Si un lieu a été choisi via la barre de recherche, on réutilise le Lieu du même nom ou on le crée
    private function rattacherLieuRecherche(FormInterface $form, Oeuvre $oeuvre, LieuRepository $lieuRepository, EntityManagerInterface $entityManager): void
    {
        $nom = mb_substr(trim((string) $form->get('lieuNom')->getData()), 0, 255);
        if ('' === $nom) {
            return;
        }

        $lieu = $lieuRepository->findOneBy(['nom' => $nom]);
        if (!$lieu) {
            $adresse = trim((string) $form->get('lieuAdresse')->getData());
            $lieu = (new Lieu())
                ->setNom($nom)
                ->setAdresse(mb_substr('' !== $adresse ? $adresse : $nom, 0, 255))
                ->setLatitude($oeuvre->getLatitude())
                ->setLongitude($oeuvre->getLongitude());
            $entityManager->persist($lieu);
        }

        $oeuvre->setLieu($lieu);
    }
}
