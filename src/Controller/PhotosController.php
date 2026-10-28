<?php

namespace App\Controller;

use App\Entity\Photos;
use App\Form\PhotosType;
use App\Repository\PhotosRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/photos')]
final class PhotosController extends AbstractController
{
    #[Route(name: 'app_photos_index', methods: ['GET'])]
    public function index(PhotosRepository $photosRepository): Response
    {
        return $this->render('photos/index.html.twig', [
            'photos' => $photosRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_photos_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $photo = new Photos();
        $form = $this->createForm(PhotosType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo->setNomFichier($fileUploader->upload($form->get('fichier')->getData()));
            $entityManager->persist($photo);
            $entityManager->flush();

            return $this->redirectToRoute('app_photos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('photos/new.html.twig', [
            'photo' => $photo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_photos_show', methods: ['GET'])]
    public function show(Photos $photo): Response
    {
        return $this->render('photos/show.html.twig', [
            'photo' => $photo,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_photos_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Photos $photo, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(PhotosType::class, $photo, ['fichier_requis' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($fichier = $form->get('fichier')->getData()) {
                $photo->setNomFichier($fileUploader->upload($fichier));
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_photos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('photos/edit.html.twig', [
            'photo' => $photo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_photos_delete', methods: ['POST'])]
    public function delete(Request $request, Photos $photo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$photo->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($photo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_photos_index', [], Response::HTTP_SEE_OTHER);
    }
}
