<?php

namespace App\Form;

use App\Entity\FigureGroup;
use App\Entity\Tricks;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TrickFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('figureGroup', EntityType::class, [
                'class' => FigureGroup::class,
                'choice_label' => 'name',
                'label' => 'Groupe auquel appartient la figure'
            ])
            ->add('Description')
            ->add('mainMedia', FileType::class, [
                'data_class' => null,
                'mapped' => false,
                'label' => false,
                'required' => false
            ])
            ->add('media', FileType::class, [
                'data_class' => null,
                'mapped' => false,
                'label' => false,
                'multiple' => true,
                'required' => false
            ])
            ->add('video', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Vidéo'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tricks::class,
        ]);
    }
}
