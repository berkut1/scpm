<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\Check\VpsProvisioningStatus;

use App\ReadModel\ControlPanel\Panel\SolidCP\EnterpriseServer\EnterpriseServerFetcher;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private EnterpriseServerFetcher $enterpriseServerFetcher;

    public function __construct(EnterpriseServerFetcher $enterpriseServerFetcher)
    {
        $this->enterpriseServerFetcher = $enterpriseServerFetcher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_enterprise', Type\ChoiceType::class,
                [
                    'choices' => array_flip($this->enterpriseServerFetcher->allList()),
                    'placeholder' => 'Default',
                    'required' => false
                ])
            ->add('solidcp_item_id', Type\IntegerType::class,
                [
                    'label' => 'itemId',
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