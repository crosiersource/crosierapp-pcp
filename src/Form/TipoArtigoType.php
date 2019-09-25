<?php

namespace App\Form;

use App\Entity\TipoArtigo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TipoArtigoType
 *
 * @author Carlos Eduardo Pauluk
 */
class TipoArtigoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('codigo', IntegerType::class, array(
            'label' => 'Código',
            'required' => false,
        ));

        $builder->add('descricao', TextType::class, array(
            'label' => 'Descrição',
            'attr' => ['class' => 'focusOnReady']
        ));

        $builder->add('modoCalculo', ChoiceType::class, array(
            'label' => 'Modo de Cálculo',
            'choices' => [
                'Modo 1' => 'MODO_1',
                'Modo 2' => 'MODO_2',
                'Modo 3' => 'MODO_3',
                'Modo 4' => 'MODO_4',
            ],
            'attr' => ['class' => 'autoSelect2']

        ));

        $builder->add('subdeptoId', IntegerType::class, array(
            'label' => 'Subdepto'
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TipoArtigo::class
        ));
    }
}