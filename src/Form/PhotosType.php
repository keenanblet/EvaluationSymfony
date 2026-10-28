<?php

namespace App\Form;

use App\Entity\Photos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PhotosType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('datePriseVue')
            ->add('fichier', FileType::class, [
                'label' => 'Fichier photo',
                'mapped' => false,
                'required' => $options['fichier_requis'],
                'constraints' => [
                    new File(
                        maxSize: '5M',
                        mimeTypes: [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                            'image/gif',
                        ],
                        mimeTypesMessage :
                            'Merci d’envoyer une image valide JPEG, PNG, WebP ou GIF.',
                    ),
                ],
            ])
            ->add('etatOeuvre');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Photos::class,
            'fichier_requis' => true,
        ]);

        $resolver->setAllowedTypes('fichier_requis', 'bool');
    }
}