<?php

namespace App\Form;

use App\Entity\FichaTecnica;
use App\Entity\TipoArtigo;
use CrosierSource\CrosierLibBaseBundle\APIClient\Base\PessoaAPIClient;
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

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            /** @var FichaTecnica $fichaTecnica */
            $fichaTecnica = $event->getData();
            $form = $event->getForm();


            $form->add('descricao', TextType::class, [
                'label' => 'Descrição'
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
                    'MODO_1' => 'Modo 1',
                    'MODO_2' => 'Modo 2',
                    'MODO_3' => 'Modo 3',
                    'MODO_4' => 'Modo 4',
                ]
            ]);

            $form->add('gradeId', IntegerType::class, [
                'label' => 'Grade'
            ]);

            $form->add('pessoaId', ChoiceType::class, [
                'label' => 'Instituição',
                'required' => false,
                'choices' => $choices['pessoaId'] ?? null,
                'attr' => isset($choices['pessoaId']) ? ['class' => 'autoSelect2'] : [
                    'data-id-route-url' => $fichaTecnica && $fichaTecnica->getPessoaId() ? PessoaAPIClient::getBaseUri() . '/findById/' . $fichaTecnica->getPessoaId() : '',
                    'data-route-url' => PessoaAPIClient::getBaseUri() . '/findByStr/',
                    'data-text-format' => '%(nomeFantasia)s (%(nome)s)',
                    'data-val' => $fichaTecnica && $fichaTecnica->getPessoaId() ? $fichaTecnica->getPessoaId() : '',
                    'class' => 'autoSelect2'
                ]
            ]);

            $form->add('pessoaNome', TextType::class, [
                'disabled' => true,
                'required' => false,
                'label' => 'Instituição'
            ]);

            $form->add('obs', TextareaType::class, [
                'label' => 'Obs'
            ]);

        });

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();

                $instituicao = $event->getData()['pessoa'] ?: null;
                $form->remove('pessoa');
                $form->add('pessoa', ChoiceType::class, array(
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