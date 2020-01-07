<?php

namespace App\Form;

use App\Entity\InsumoPreco;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InsumoPrecoType
 *
 * @author Carlos Eduardo Pauluk
 */
class InsumoPrecoType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('dtCusto', DateType::class, [
            'label' => 'Dt Custo',
            'widget' => 'single_text',
            'required' => false,
            'html5' => false,
            'format' => 'dd/MM/yyyy',
            'attr' => [
                'class' => 'crsr-date'
            ]
        ]);

        $builder->add('precoCusto', MoneyType::class, [
            'label' => 'Preço Custo',
            'currency' => 'BRL',
            'grouping' => 'true',
            'attr' => [
                'class' => 'crsr-money'
            ],
            'required' => false
        ]);


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => InsumoPreco::class
        ));
    }
}