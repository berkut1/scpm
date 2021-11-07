<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Package\VirtualMachine\Edit;

use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
                ])
            ->add('space_gb', Type\IntegerType::class,
                [
                    'label' => 'Space (GB)',
                    'required' => true
                ])
            ->add('iops_min', Type\IntegerType::class,
                [
                    'label' => 'Min IOPS',
                    'required' => true
                ])
            ->add('iops_max', Type\IntegerType::class,
                [
                    'label' => 'Max IOPS',
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