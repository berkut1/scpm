<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\Edit;

use App\ReadModel\ControlPanel\Location\LocationFetcher;
use App\ReadModel\ControlPanel\Panel\SolidCP\EnterpriseServer\EnterpriseServerFetcher;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private EnterpriseServerFetcher $enterpriseServerFetcher;
    private LocationFetcher $locationFetcher;

    public function __construct(EnterpriseServerFetcher $enterpriseServerFetcher, LocationFetcher $locationFetcher)
    {
        $this->enterpriseServerFetcher = $enterpriseServerFetcher;
        $this->locationFetcher = $locationFetcher;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_enterprise', Type\ChoiceType::class,
                [
                    'choices' => array_flip($this->enterpriseServerFetcher->allList()),
                    'required' => true,
                    //'placeholder' => 'All Locations'
                ])
            ->add('id_location', Type\ChoiceType::class,
                [
                    'choices' => array_flip($this->locationFetcher->allList()),
                    'required' => true,
                    'placeholder' => 'Select a Location'
                ])
            ->add('name', Type\TextType::class,
                [
                    'label' => 'Name',
                    'required' => true
                ])
            ->add('cores', Type\IntegerType::class,
                [
                    'label' => 'Cores',
                    'required' => true
                ])
            ->add('threads', Type\IntegerType::class,
                [
                    'label' => 'Threads',
                    'required' => true
                ])
            ->add('ram_mb', Type\IntegerType::class,
                [
                    'label' => 'RAM (MB)',
                    'required' => true
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}