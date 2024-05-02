<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\OsTemplate\Add\Collection;

use App\Event\FormErrorEvent;
use App\Model\ControlPanel\Service\SolidCP\VirtualizationServer2012Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class Form extends AbstractType
{
    public function __construct(
        private readonly VirtualizationServer2012Service $virtualizationServer2012Service,
        private readonly EventDispatcherInterface        $dispatcher
    ) {}

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = [];

        try {
            $choices = array_flip($this->virtualizationServer2012Service->allOsTemplateListFrom((int)$options['id_enterprise_dispatcher'], (int)$options['packageId']));
        } catch (\Exception $e) {
            $this->dispatcher->dispatch(new FormErrorEvent($e, 'Error fetching OS templates'));
        }

        $builder
            ->add('path', Type\ChoiceType::class,
                [
                    'label' => 'Os',
                    'choices' => $choices,
                    'placeholder' => 'Select Os',
                    'required' => true,
                ])
            ->add('name', Type\TextType::class, [
//                'label' => 'Name',
                'attr' => ['placeholder' => 'Os Name'],
                'required' => true,
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, $this->onPreSetData(...));
    }

    protected function disableElements(FormInterface $form, array $data, Command $modelOriginalData): void
    {
        $this->disableAndOverrideFormField($form, 'path');
        $this->disableAndOverrideFormField($form, 'name');
    }

    private function disableAndOverrideFormField(FormInterface $form, string $fieldName): void
    {
        $field = $form->get($fieldName);
        $options = $field->getConfig()->getOptions();
        $options['disabled'] = true;
        $type = get_class($field->getConfig()->getType()->getInnerType()); //type of field
        $form->add($fieldName, $type, $options);
    }

    public function onPreSetData(FormEvent $event): void
    {
        $form = $event->getForm();
        $modelOriginalData = $event->getData(); //object
        $data = json_decode(json_encode($modelOriginalData), true); //convert object to assoc array
        if (empty($data)) {
            return;
        }

        $this->disableElements($form, $data, $modelOriginalData);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Command::class, 'id_enterprise_dispatcher' => 0, 'packageId' => 0]);
    }
}