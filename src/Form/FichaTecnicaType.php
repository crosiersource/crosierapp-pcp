<?php

namespace App\Form;

use App\Business\FichaTecnicaBusiness;
use App\Entity\FichaTecnica;
use App\Entity\TipoArtigo;
use CrosierSource\CrosierLibBaseBundle\APIClient\Base\PropAPIClient;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\WhereBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FichaTecnicaType
 *
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaType extends AbstractType
{

    /** @var RegistryInterface */
    private $doctrine;

    /** @var PropAPIClient */
    private $propAPIClient;

    /** @var FichaTecnicaBusiness */
    private $fichaTecnicaBusiness;

    /**
     * @required
     * @param RegistryInterface $doctrine
     */
    public function setDoctrine(RegistryInterface $doctrine): void
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @required
     * @param PropAPIClient $propAPIClient
     * @return FichaTecnicaType
     */
    public function setPropAPIClient(PropAPIClient $propAPIClient): FichaTecnicaType
    {
        $this->propAPIClient = $propAPIClient;
        return $this;
    }

    /**
     * @required
     * @param FichaTecnicaBusiness $fichaTecnicaBusiness
     */
    public function setFichaTecnicaBusiness(FichaTecnicaBusiness $fichaTecnicaBusiness): void
    {
        $this->fichaTecnicaBusiness = $fichaTecnicaBusiness;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            /** @var FichaTecnica $fichaTecnica */
            $fichaTecnica = $event->getData();
            $form = $event->getForm();

            $form->add('pessoaId', ChoiceType::class, [
                'label' => 'Instituição',
                'required' => false,
                'attr' => [
                    'data-options' => $this->fichaTecnicaBusiness->buildInstituicoesSelect2(),
                    'data-val' => $fichaTecnica && $fichaTecnica->getPessoaId() ? $fichaTecnica->getPessoaId() : '',
                    'class' => 'autoSelect2'
                ]
            ]);

            $form->add('tipoArtigo', EntityType::class, [
                'class' => TipoArtigo::class,
                'choices' => $this->doctrine->getRepository(TipoArtigo::class)->findAll(WhereBuilder::buildOrderBy('descricao')),
                'placeholder' => '...',
                'required' => false,
                'choice_label' => function (?TipoArtigo $tipoArtigo) {
                    return $tipoArtigo && $tipoArtigo->getDescricaoMontada() ? $tipoArtigo->getDescricaoMontada() : '';
                },
                'attr' => ['class' => 'autoSelect2']
            ]);

            $form->add('descricao', TextType::class, [
                'label' => 'Descrição'
            ]);

            $form->add('bloqueada', ChoiceType::class, [
                'choices' => [
                    'Sim' => true,
                    'Não' => false
                ]
            ]);

            $form->add('oculta', ChoiceType::class, [
                'choices' => [
                    'Sim' => true,
                    'Não' => false
                ]
            ]);

            $form->add('custoOperacionalPadrao', NumberType::class, [
                'label' => 'Cto Op Padrão',
                'grouping' => 'true',
                'scale' => 3,
                'required' => false,
                'attr' => [
                    'class' => 'crsr-dec3'
                ]
            ]);


            $form->add('margemPadrao', NumberType::class, [
                'label' => 'Margem Padrão',
                'grouping' => 'true',
                'scale' => 3,
                'required' => false,
                'attr' => [
                    'class' => 'crsr-dec3'
                ]
            ]);

            $form->add('prazoPadrao', IntegerType::class, [
                'label' => 'Prazo Padrão'
            ]);

            $form->add('custoFinanceiroPadrao', NumberType::class, [
                'label' => 'Cto Fin Padrão',
                'grouping' => 'true',
                'scale' => 3,
                'required' => false,
                'attr' => [
                    'class' => 'crsr-dec3'
                ]
            ]);

            $form->add('modoCalculo', ChoiceType::class, [
                'choices' => [
                    'MODO 1' => 'MODO_1',
                    'MODO 2' => 'MODO_2',
                    'MODO 3' => 'MODO_3',
                    'MODO 4' => 'MODO_4',
                ]
            ]);

            $grades = array_flip($this->propAPIClient->findGrades());
            $form->add('gradeId', ChoiceType::class, [
                'label' => 'Grade',
                'required' => false,
                'choices' => $grades,
                'attr' => [
                    'class' => 'autoSelect2'
                ]
            ]);


            $form->add('obs', TextareaType::class, [
                'label' => 'Obs',
                'required' => false
            ]);

        });

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();

                $instituicao = $event->getData()['pessoaId'] ?: null;
                $form->remove('pessoaId');
                $form->add('pessoaId', ChoiceType::class, array(
                    'label' => 'Instituição',
                    'required' => false,
                    'choices' => [$instituicao]
                ));
            }
        );


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => FichaTecnica::class
        ));
    }
}