<?php

namespace App\Form;

use App\Business\PropBusiness;
use App\Entity\Insumo;
use App\Entity\TipoInsumo;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\WhereBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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

    /** @var ManagerRegistry */
    private $doctrine;

    /** @var PropBusiness */
    private $propBusiness;

    /**
     * @required
     * @param ManagerRegistry $doctrine
     */
    public function setDoctrine(ManagerRegistry $doctrine): void
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @required
     * @param PropBusiness $propBusiness
     */
    public function setPropBusiness(PropBusiness $propBusiness): void
    {
        $this->propBusiness = $propBusiness;
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

        $rUnidades = $this->propBusiness->findUnidades();
        $unidades = [];
        foreach ($rUnidades as $rUnidade) {
            $unidades[$rUnidade['label']] = $rUnidade['id'];
        }

        $builder->add('unidadeProdutoId', ChoiceType::class, array(
            'label' => 'Unidade',
            'choices' => $unidades,
            'attr' => ['class' => 'autoSelect2']
        ));

        $builder->add('tipoInsumo', EntityType::class, array(
            'label' => 'Tipo',
            'class' => TipoInsumo::class,
            'choices' => $this->doctrine->getRepository(TipoInsumo::class)->findAll(WhereBuilder::buildOrderBy('descricao')),
            'placeholder' => '...',
            'required' => false,
            'choice_label' => function (?TipoInsumo $tipoInsumo) {
                return $tipoInsumo && $tipoInsumo->getDescricaoMontada() ? $tipoInsumo->getDescricaoMontada() : '';
            },
            'attr' => ['class' => 'autoSelect2']
        ));

        $builder->add('precoAtual', InsumoPrecoType::class);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Insumo::class
        ));
    }
}