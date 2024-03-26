<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\CreateVM;

use App\Model\ControlPanel\Service\SolidCP\VirtualizationServer2012Service;
use App\ReadModel\ControlPanel\Package\VirtualMachine\VirtualMachinePackageFetcher;
use App\ReadModel\ControlPanel\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Form extends AbstractType
{
    public function __construct(
        private readonly EnterpriseDispatcherFetcher     $enterpriseDispatcherFetcher,
        private readonly VirtualizationServer2012Service $virtualizationServer2012Service,
        private readonly VirtualMachinePackageFetcher    $virtualMachinePackageFetcher
    ) {}

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, $this->onPreSetData(...));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, $this->onPreSubmit(...)); //need to refill data to pass the form validation
    }

    protected function addElements(FormInterface $form, array $data, Command $modelOriginalData): void
    {
        $form
            ->add('id_enterprise_dispatcher', Type\ChoiceType::class,
                [
                    'label' => 'Enterprise server',
                    'placeholder' => 'Select an Enterprise server or use default',
                    'choices' => array_flip($this->enterpriseDispatcherFetcher->allList()),
                    'required' => false,
                    //'data' => isset($data['id_enterprise_dispatcher']) ?? $data['id_enterprise_dispatcher'],
                ])
            ->add('packageId', Type\IntegerType::class,
                [
                    'label' => 'PackageId',
                    'required' => true,
                ])
            ->add('hostname', Type\TextType::class,
                [
                    'label' => 'Host Name (empty - auto generate)',
                    'required' => false,
                ])
            ->add('password', Type\PasswordType::class,
                [
                    'label' => 'Password',
                    'required' => true,
                ])
            ->add('id_package_virtual_machines', Type\ChoiceType::class,
                [
                    'label' => 'Package',
                    'placeholder' => 'Select a Package',
                    'choices' => array_flip($this->virtualMachinePackageFetcher->allList()),
                    'required' => true,
                    //'data' => isset($data['id_enterprise_dispatcher']) ?? $data['id_enterprise_dispatcher'],
                ])
            ->add('snapshotsNumber', Type\IntegerType::class,
                [
                    'label' => 'snapshotsNumber',
                    'required' => true,
                ])
            ->add('dvdDriveInstalled', Type\CheckboxType::class,
                [
                    'label' => 'dvdDriveInstalled',
                    'required' => false,
                ])
            ->add('bootFromCD', Type\CheckboxType::class,
                [
                    'label' => 'bootFromCD',
                    'required' => false,
                ])
            ->add('numLockEnabled', Type\CheckboxType::class,
                [
                    'label' => 'numLockEnabled',
                    'required' => false,
                ])
            ->add('startTurnOffAllowed', Type\CheckboxType::class,
                [
                    'label' => 'startTurnOffAllowed',
                    'required' => false,
                ])
            ->add('pauseResumeAllowed', Type\CheckboxType::class,
                [
                    'label' => 'pauseResumeAllowed',
                    'required' => false,
                ])
            ->add('rebootAllowed', Type\CheckboxType::class,
                [
                    'label' => 'rebootAllowed',
                    'required' => false,
                ])
            ->add('resetAllowed', Type\CheckboxType::class,
                [
                    'label' => 'resetAllowed',
                    'required' => false,
                ])
            ->add('reinstallAllowed', Type\CheckboxType::class,
                [
                    'label' => 'reinstallAllowed',
                    'required' => false,
                ])
            ->add('externalNetworkEnabled', Type\CheckboxType::class,
                [
                    'label' => 'externalNetworkEnabled',
                    'required' => false,
                ])
            ->add('privateNetworkEnabled', Type\CheckboxType::class,
                [
                    'label' => 'privateNetworkEnabled',
                    'required' => false,
                ])
            ->add('defaultaccessvlan', Type\IntegerType::class,
                [
                    'label' => 'defaultaccessvlan',
                    'required' => true,
                ]);

        $templates = [];
        if (!empty($data['packageId'])) {
            if (empty($data['id_enterprise_dispatcher'])) {
                $data['id_enterprise_dispatcher'] = $this->enterpriseDispatcherFetcher->getDefault()->getId();
            }
            $templates = array_flip($this->virtualizationServer2012Service->allOsTemplateListFrom((int)$data['id_enterprise_dispatcher'], (int)$data['packageId']));
        }
        $form->add('osTemplateFile', Type\ChoiceType::class,
            [
                'label' => 'osTemplateFile',
                'required' => true,
                'placeholder' => 'Select an Enterprise and a packageId first...',
                'choices' => $templates,
                //'data' => isset($data['id_server']) ?? $data['id_server'],
            ])
            ->add('externalAddressesNumber', Type\IntegerType::class,
                [
                    'label' => 'externalAddressesNumber',
                    'required' => true,
                ])
            ->add('randomExternalAddresses', Type\CheckboxType::class,
                [
                    'label' => 'randomExternalAddresses',
                    'required' => false,
                ])
            ->add('privateAddressesNumber', Type\IntegerType::class,
                [
                    'label' => 'privateAddressesNumber',
                    'required' => true,
                ])
            ->add('randomPrivateAddresses', Type\CheckboxType::class,
                [
                    'label' => 'randomPrivateAddresses',
                    'required' => false,
                ])
            ->add('summaryLetterEmail', Type\CheckboxType::class,
                [
                    'label' => 'summaryLetterEmail',
                    'required' => false,
                ]);
    }

    function onPreSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData(); //array
        $modelOriginalData = $event->getForm()->getData(); //object

        $this->addElements($form, $data, $modelOriginalData);
    }

    function onPreSetData(FormEvent $event): void
    {
        $form = $event->getForm();
        $modelOriginalData = $event->getData(); //object
        $data = json_decode(json_encode($modelOriginalData), true); //convert object to assoc array

        $this->addElements($form, $data, $modelOriginalData);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Command::class]);
    }
}