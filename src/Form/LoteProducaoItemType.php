<?php

namespace App\Form;

use App\Entity\FichaTecnica;
use App\Entity\LoteProducaoItem;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\WhereBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LoteProducaoItemType
 *
 * @author Carlos Eduardo Pauluk
 */
class LoteProducaoItemType extends AbstractType
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


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var LoteProducaoItem $loteProducaoItem */
            $loteProducaoItem = $event->getData();
            $form = $event->getForm();

            $form->add('loteProducao', HiddenType::class);

            $form->add('fichaTecnica', EntityType::class, array(
                'class' => FichaTecnica::class,
                'choices' => $this->doctrine->getRepository(FichaTecnica::class)->findAll(WhereBuilder::buildOrderBy('descricao')),
                'placeholder' => '...',
                'required' => false,
                'choice_label' => function (?FichaTecnica $fichaTecnica) {
                    return $fichaTecnica && $fichaTecnica->getDescricao() ? $fichaTecnica->getDescricao() : '';
                },
                'attr' => ['class' => 'autoSelect2']
            ));

            $form->add('obs', TextType::class, array(
                'label' => 'Obs',
                'required' => true
            ));

        });

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => LoteProducaoItem::class
        ));
    }
}