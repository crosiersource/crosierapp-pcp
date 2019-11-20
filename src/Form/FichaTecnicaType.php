<?php

namespace App\Form;

use App\Business\FichaTecnicaBusiness;
use App\Business\PropBusiness;
use App\Entity\FichaTecnica;
use App\Entity\TipoArtigo;
use CrosierSource\CrosierLibBaseBundle\Entity\Base\Pessoa;
use CrosierSource\CrosierLibBaseBundle\Repository\Base\PessoaRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\WhereBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
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

    /** @var EntityManagerInterface */
    private $doctrine;

    /** @var FichaTecnicaBusiness */
    private $fichaTecnicaBusiness;

    /** @var PropBusiness */
    private $propBusiness;

    /**
     * @required
     * @param EntityManagerInterface $doctrine
     */
    public function setDoctrine(EntityManagerInterface $doctrine): void
    {
        $this->doctrine = $doctrine;
    }


    /**
     * @required
     * @param FichaTecnicaBusiness $fichaTecnicaBusiness
     */
    public function setFichaTecnicaBusiness(FichaTecnicaBusiness $fichaTecnicaBusiness): void
    {
        $this->fichaTecnicaBusiness = $fichaTecnicaBusiness;
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

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            /** @var FichaTecnica $fichaTecnica */
            $fichaTecnica = $event->getData();
            $form = $event->getForm();


            $form->add('id', IntegerType::class, [
                'label' => 'Id',
                'disabled' => true
            ]);

            $instituicaoChoices = [];
            if ($fichaTecnica->getInstituicao()) {
                $instituicaoChoices = [$fichaTecnica->getInstituicao()];
            }
            $form->add('instituicao', EntityType::class, [
                'class' => Pessoa::class,
                'choices' => $instituicaoChoices,
                'label' => 'Instituicao',
                'required' => false,
                'choice_label' => function (?Pessoa $pessoa) {
                    return $pessoa ? $pessoa->getNomeMontado() : '';
                },
                'attr' => [
                    'data-val' => $fichaTecnica->getInstituicao() ? $fichaTecnica->getInstituicao()->getId() : '',
                    'data-id-route-url' => $fichaTecnica->getInstituicao() ? '/base/pessoa/findById/?id=' . $fichaTecnica->getInstituicao()->getId() : '',
                    'data-route-url' => '/base/pessoa/findByStr/?categ=CLIENTE_PCP',
                    'data-text-format' => '%(nomeMontado)s',
                    'class' => 'autoSelect2 focusOnReady'
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
                'label' => 'Descrição',
                'attr' => ['autocomplete' => 'off']

            ]);

            $form->add('bloqueada', ChoiceType::class, [
                'choices' => [
                    'Sim' => true,
                    'Não' => false
                ],
                'attr' => ['class' => 'autoSelect2']
            ]);

            $form->add('oculta', ChoiceType::class, [
                'choices' => [
                    'Sim' => true,
                    'Não' => false
                ],
                'attr' => ['class' => 'autoSelect2']
            ]);

            $form->add('custoOperacionalPadrao', NumberType::class, [
                'label' => 'Cto Op Padrão',
                'scale' => 3,
                'required' => false,
                'attr' => [
                    'class' => 'crsr-dec3'
                ]
            ]);

            $form->add('margemPadrao', NumberType::class, [
                'label' => 'Margem Padrão',
                'scale' => 3,
                'required' => false,
                'attr' => [
                    'class' => 'crsr-dec3'
                ]
            ]);

            $form->add('prazoPadrao', IntegerType::class, [
                'label' => 'Prazo Padrão'
            ]);

            $form->add('custoFinanceiroPadrao', PercentType::class, [
                'label' => 'Cto Fin Padrão',
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
                ],
                'attr' => ['class' => 'autoSelect2']
            ]);

            $grades = array_flip($this->propBusiness->findGrades());
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
                /** @var FichaTecnica $fichaTecnica */
                $fichaTecnica = $event->getData();

                $form->remove('instituicao');

                $instituicaoChoices = [];
                if ($fichaTecnica['instituicao']) {
                    /** @var PessoaRepository $repoPessoa */
                    $repoPessoa = $this->doctrine->getRepository(Pessoa::class);
                    $instituicao = $repoPessoa->find($fichaTecnica['instituicao']);
                    $instituicaoChoices = [$instituicao];
                }
                $form->add('instituicao', EntityType::class, [
                    'class' => Pessoa::class,
                    'choices' => $instituicaoChoices,
                    'label' => 'Instituicao',
                    'required' => false,
                    'choice_label' => function (?Pessoa $pessoa) {
                        return $pessoa ? $pessoa->getNomeMontado() : '';
                    },
                    'attr' => [
                        'data-val' => $fichaTecnica['instituicao'] ?? null,
                        'data-id-route-url' => isset($fichaTecnica['instituicao']) ? '/base/pessoa/findById/?id=' . $fichaTecnica['instituicao'] : '',
                        'data-route-url' => '/base/pessoa/findByStr/?categ=CLIENTE_PCP',
                        'data-text-format' => '%(nomeMontado)s',
                        'class' => 'autoSelect2'
                    ]
                ]);
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