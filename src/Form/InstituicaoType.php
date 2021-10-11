<?php

namespace App\Form;


use CrosierSource\CrosierLibRadxBundle\Entity\CRM\Cliente;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Carlos Eduardo Pauluk
 */
class InstituicaoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('id', TextType::class, [
            'label' => 'Id',
            'required' => false,
            'attr' => ['readonly' => 'readonly']
        ]);

        $builder->add('documento', TextType::class, [
            'label' => 'CPF/CNPJ',
            'required' => false,
            'attr' => [
                'class' => 'cpfCnpj'
            ],
        ]);

        $builder->add('nome', TextType::class, [
            'label' => 'Nome',
            'attr' => ['class' => 'focusOnReady']
        ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cliente::class
        ]);
    }
}
