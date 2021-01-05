<?php

namespace App\Form;

use App\Business\FichaTecnicaBusiness;
use App\Entity\FichaTecnica;
use App\Entity\TipoArtigo;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\ExceptionUtils\ExceptionUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\WhereBuilder;
use CrosierSource\CrosierLibRadxBundle\Entity\CRM\Cliente;
use CrosierSource\CrosierLibRadxBundle\Repository\CRM\ClienteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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

    private EntityManagerInterface $doctrine;

    private FichaTecnicaBusiness $fichaTecnicaBusiness;

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


            if ($fichaTecnica->getInstituicao()) {
                $instituicaoChoices = [$fichaTecnica->getInstituicao()];
            } else {
                try {
                    /** @var ClienteRepository $repoCliente */
                    $repoCliente = $this->doctrine->getRepository(Cliente::class);
                    $sql = 'SELECT id FROM crm_cliente WHERE json_data->>"$.cliente_pcp" = \'S\' ORDER BY nome';
                    $rs = $this->doctrine->getConnection()->fetchAllAssociative($sql);
                    if (!$rs || count($rs) < 1) {
                        return null;
                    }
                    $instituicaoChoices = [];
                    foreach ($rs as $r) {
                        $instituicaoChoices[] = $repoCliente->find($r['id']);
                    }

                } catch (\Throwable $e) {
                    $msg = ExceptionUtils::treatException($e);
                    throw new ViewException('Erro ao pesquisar clientes cliente_pcp (' . $msg . ')', 0, $e);
                }

            }
            $form->add('instituicao', EntityType::class, [
                'label' => 'Instituição',
                'class' => Cliente::class,
                'choices' => $instituicaoChoices,
                'required' => false,
                'choice_label' => function (?Cliente $cliente) {
                    return $cliente ? $cliente->nome : '';
                },
                'attr' => [
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

            $form->add('custoOperacionalPadrao', PercentType::class, [
                'label' => 'Cto Op Padrão',
                'scale' => 2,
                'required' => false,
                'attr' => [
                    'class' => 'crsr-dec3'
                ]
            ]);

            $form->add('margemPadrao', PercentType::class, [
                'label' => 'Margem Padrão',
                'scale' => 2,
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
                'scale' => 2,
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

            $grades = array_flip($this->fichaTecnicaBusiness->findGrades());
            $form->add('gradeId', ChoiceType::class, [
                'label' => 'Grade',
                'required' => true,
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
                    /** @var ClienteRepository $repoCliente */
                    $repoCliente = $this->doctrine->getRepository(Cliente::class);
                    $instituicao = $repoCliente->find($fichaTecnica['instituicao']);
                    $instituicaoChoices = [$instituicao];
                }
                $form->add('instituicao', EntityType::class, [
                    'class' => Cliente::class,
                    'choices' => $instituicaoChoices,
                    'label' => 'Instituicao',
                    'required' => false,
                    'choice_label' => function (?Cliente $cliente) {
                        return $cliente ? $cliente->nome : '';
                    },
                    'attr' => [
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
