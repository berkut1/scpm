<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\OsTemplate\Add\Collection;

use App\Model\ControlPanel\Service\SolidCP\VirtualizationServer2012Service;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
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
        if(empty($data)){
            return;
        }

        $this->disableElements($form, $data, $modelOriginalData);
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