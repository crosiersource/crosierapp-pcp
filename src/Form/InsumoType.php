<?php

namespace App\Form;

use App\Entity\Insumo;
use App\Entity\TipoInsumo;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\WhereBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InsumoType
 *
 * @author Carlos Eduardo Pauluk
 */
class InsumoType extends AbstractType
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

        $builder->add('unidadeProdutoId', IntegerType::class, array(
            'label' => 'Unidade'
        ));

        $builder->add('tipoInsumo', EntityType::class, array(
            'class' => TipoInsumo::class,
            'choices' => $this->doctrine->getRepository(TipoInsumo::class)->findAll(WhereBuilder::buildOrderBy('descricao')),
            'placeholder' => '...',
            'required' => false,
            'choice_label' => function (?TipoInsumo $tipoInsumo) {
                return $tipoInsumo && $tipoInsumo->getDescricaoMontada() ? $tipoInsumo->getDescricaoMontada() : '';
            },
            'attr' => ['class' => 'autoSelect2']
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Insumo::class
        ));
    }
}