<?php

namespace App\Form;

use App\Business\FichaTecnicaBusiness;
use App\Entity\Insumo;
use App\Entity\TipoInsumo;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\WhereBuilder;
use CrosierSource\CrosierLibRadxBundle\Entity\Estoque\Unidade;
use CrosierSource\CrosierLibRadxBundle\Repository\Estoque\UnidadeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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

    private EntityManagerInterface $doctrine;

    /**
     * @required
     * @param EntityManagerInterface $doctrine
     */
    public function setDoctrine(EntityManagerInterface $doctrine): void
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

        $builder->add('marca', TextType::class, array(
            'label' => 'Marca'
        ));

        /** @var UnidadeRepository $repoUnidade */
        $repoUnidade = $this->doctrine->getRepository(Unidade::class);
        
        $rUnidades = $repoUnidade->findAll();
        $unidades = [];
        
        /** @var Unidade $unidade */
        foreach ($rUnidades as $unidade) {
            $unidades[$unidade->label] = $unidade->getId();
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
            'attr' => ['class' => 'autoSelect2 focusOnReady']
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
