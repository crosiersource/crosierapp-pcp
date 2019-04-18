<?php

namespace App\Form;

use App\Entity\Coisa;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FormType para a entidade Coisa.
 *
 * @package App\Form
 * @author Carlos Eduardo Pauluk
 */
class CoisaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nome', TextType::class, array(
            'label' => 'Nome'
        ));

        $builder->add('obs', TextareaType::class, array(
            'label' => 'Obs',
            'required' => false,
            'attr' => [
                'style' => 'text-transform: none;'
            ]
        ));

        $builder->add('dtCoisa', DateType::class, array(
            'label' => 'Dt Coisa',
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'attr' => array(
                'class' => 'crsr-date'
            ),
            'required' => false
        ));

        $builder->add('ordem', IntegerType::class, array(
            'label' => 'Ordem'
        ));


        $builder->add('importante', ChoiceType::class, array(
            'choices' => array(
                'Sim' => true,
                'Não' => false
            )
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Coisa::class
        ));
    }
}