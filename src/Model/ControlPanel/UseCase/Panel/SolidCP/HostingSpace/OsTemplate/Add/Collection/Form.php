<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\OsTemplate\Add\Collection;

use App\Model\ControlPanel\Service\SolidCP\VirtualizationServer2012Service;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private VirtualizationServer2012Service $virtualizationServer2012Service;

    public function __construct(VirtualizationServer2012Service $virtualizationServer2012Service)
    {
        $this->virtualizationServer2012Service = $virtualizationServer2012Service;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
//        dump($options);
        $builder
            ->add('path', Type\ChoiceType::class,
                [
                    'label' => 'Os',
                    'choices' => array_flip($this->virtualizationServer2012Service->allOsTemplateListFrom((int)$options['id_enterprise_dispatcher'], (int)$options['packageId'])),
                    'placeholder' => 'Select Os',
                    'required' => true,
                ])
            ->add('name', Type\TextType::class, [
//                'label' => 'Name',
                'attr' => ['placeholder' => 'Os Name'],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
            'id_enterprise_dispatcher' => 0,
            'packageId' => 0,
        ));
    }
}