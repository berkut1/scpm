<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\Package\Create;

use App\ReadModel\ControlPanel\Panel\SolidCP\EnterpriseServer\EnterpriseServerFetcher;
use App\ReadModel\ControlPanel\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlanFetcher;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private EnterpriseServerFetcher $enterpriseServerFetcher;
    private SolidcpHostingPlanFetcher $solidcpHostingPlanFetcher;

    public function __construct(EnterpriseServerFetcher $enterpriseServerFetcher, SolidcpHostingPlanFetcher $solidcpHostingPlanFetcher)
    {
        $this->enterpriseServerFetcher = $enterpriseServerFetcher;
        $this->solidcpHostingPlanFetcher = $solidcpHostingPlanFetcher;
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
            ->add('planId', Type\ChoiceType::class,
                [
                    'choices' => array_flip($this->solidcpHostingPlanFetcher->allListWithSolidCpIdPlan()),
                    'required' => true
                ])
            ->add('userId', Type\IntegerType::class,
                [
                    'label' => 'UserID',
                    'required' => true
                ])
            ->add('spaceName', Type\TextType::class,
                [
                    'label' => 'Space Name',
                    'required' => false
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}