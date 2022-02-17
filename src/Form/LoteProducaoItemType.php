<?php

namespace App\Form;

use App\Entity\FichaTecnica;
use App\Entity\LoteProducaoItem;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\WhereBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            /** @var LoteProducaoItem $loteProducaoItem */
            $loteProducaoItem = $event->getData();
            $form = $event->getForm();

            $ultimoMes = DateTimeUtils::incMes(new \DateTime(), -1);
            $repoFichaTecnica = $this->doctrine->getRepository(FichaTecnica::class);
            $choices = $repoFichaTecnica->findByFiltersSimpl([['updated', 'GT', $ultimoMes]], ['descricao' => 'ASC'], 0, null);
            
            $form->add('fichaTecnica', EntityType::class, [
                'label' => 'Ficha Técnica',
                'class' => FichaTecnica::class,
                'choices' => $choices,
                'placeholder' => '...',
                'required' => false,
                'choice_label' => function (?FichaTecnica $fichaTecnica) {
                    return $fichaTecnica && $fichaTecnica->getDescricaoMontada() ? $fichaTecnica->getDescricaoMontada() : '';
                },
                'attr' => ['class' => 'autoSelect2']
            ]);

            $form->add('pedido', TextType::class, [
                'label' => 'Pedido',
                'required' => false
            ]);

            $form->add('obs', TextType::class, [
                'label' => 'Obs',
                'required' => false
            ]);

        });

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LoteProducaoItem::class
        ]);
    }
}