<?php

namespace App\Form;

use App\Entity\FichaTecnicaItem;
use App\Entity\Insumo;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\WhereBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FichaTecnicaItemType
 *
 * @author Carlos Eduardo Pauluk
 */
class FichaTecnicaItemType extends AbstractType
{

    /** @var EntityManagerInterface */
    private $doctrine;

    /**
     * @required
     * @param EntityManagerInterface $doctrine
     */
    public function setDoctrine(EntityManagerInterface $doctrine): void
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
            /** @var FichaTecnicaItem $fichaTecnicaItem */
            $fichaTecnicaItem = $event->getData();
            $form = $event->getForm();

            $form->add('insumo', EntityType::class, [
                'label' => 'Insumo',
                'class' => Insumo::class,
                'choices' => $this->doctrine->getRepository(Insumo::class)->findAll(WhereBuilder::buildOrderBy('descricao')),
                'placeholder' => '...',
                'required' => false,
                'choice_label' => function (?Insumo $insumo) {
                    return $insumo && $insumo->getDescricao() ? $insumo->getDescricao() : '';
                },
                'attr' => ['class' => 'autoSelect2']
            ]);

        });

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FichaTecnicaItem::class
        ]);
    }
}