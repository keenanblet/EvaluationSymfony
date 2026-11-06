<?php

namespace App\Form;

use App\Entity\Artiste;
use App\Entity\Lieu;
use App\Entity\Oeuvre;
use App\Entity\Technique;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Range;

class OeuvreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // En modification, on pré-remplit les champs de la carte avec le lieu actuel
        $oeuvre = $builder->getData();
        $lieu = $oeuvre?->getLieu();

        $builder
            ->add('titre', null, [
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Titre de l’œuvre'],
            ])
            ->add('dateCreation', null, [
                'label' => 'Date de création',
                'widget' => 'single_text',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 4, 'placeholder' => 'Décrivez l’œuvre…'],
            ])
            ->add('dateDestruction', null, [
                'label' => 'Date de destruction',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('oeuvreRecouverte', CheckboxType::class, [
                'label' => 'Œuvre recouverte',
                'required' => false,
            ])
            ->add('artistes', EntityType::class, [
                'class' => Artiste::class,
                'choice_label' => 'pseudonyme',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Artistes',
            ])
            // Remplis par la barre de recherche de la carte : le contrôleur crée le Lieu
            ->add('lieuNom', HiddenType::class, [
                'mapped' => false,
                'required' => false,
                'data' => $lieu?->getNom(),
            ])
            ->add('lieuAdresse', HiddenType::class, [
                'mapped' => false,
                'required' => false,
                'data' => $lieu?->getAdresse(),
            ])
            // Coordonnées du lieu (stockées dans Lieu, plus dans Oeuvre)
            ->add('latitude', HiddenType::class, [
                'mapped' => false,
                'data' => $lieu?->getLatitude(),
                'error_bubbling' => true,
                'constraints' => [new Range(min: -90, max: 90)],
            ])
            ->add('longitude', HiddenType::class, [
                'mapped' => false,
                'data' => $lieu?->getLongitude(),
                'error_bubbling' => true,
                'constraints' => [new Range(min: -180, max: 180)],
            ])
            ->add('techniques', EntityType::class, [
                'class' => Technique::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Techniques',
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo de l’œuvre',
                'mapped' => false,
                'required' => false,
                'help' => 'JPEG, PNG, WebP ou GIF — 2 Mo maximum.',
                'constraints' => [
                    new File(
                        maxSize: '2M', // limite du serveur PHP (upload_max_filesize = 2M)
                        mimeTypes: ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
                        mimeTypesMessage: 'Merci d’envoyer une image valide JPEG, PNG, WebP ou GIF.',
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Oeuvre::class,
        ]);
    }
}
