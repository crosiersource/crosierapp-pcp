<?php

namespace App\Form;

use App\Entity\LoteProducao;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LoteProducaoType
 *
 * @author Carlos Eduardo Pauluk
 */
class LoteProducaoType extends AbstractType
{

    /** @var RegistryInterface */
    private $doctrine;

    /**
     * @required
     * @param RegistryInterface $doctrine
     */
    public function setDoctrine(RegistryInterface $doctrine): void
    {
        $this->doctrine = $doctrine;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('codigo', IntegerType::class, array(
            'label' => 'Código',
            'required' => false
        ));

        $builder->add('descricao', TextType::class, array(
            'label' => 'Descrição'
        ));

        $builder->add('dtLote', DateType::class, [
            'label' => 'Dt Lote',
            'widget' => 'single_text',
            'required' => false,
            'format' => 'dd/MM/yyyy',
            'attr' => [
                'class' => 'crsr-date'
            ]
        ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => LoteProducao::class
        ));
    }
}