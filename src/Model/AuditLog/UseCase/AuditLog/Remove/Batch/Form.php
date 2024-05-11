<?php
declare(strict_types=1);

namespace App\Model\AuditLog\UseCase\AuditLog\Remove\Batch;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Form extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', Type\DateType::class, ['required' => true,
                'widget' => 'single_text',
                'input' => 'datetime_immutable'])
            ->add('endDate', Type\DateType::class, ['required' => true,
                'widget' => 'single_text',
                'input' => 'datetime_immutable']);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Command::class]);
    }
}