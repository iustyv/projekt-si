<?php
/**
 * Project type.
 */

namespace App\Form\Type;

use App\Entity\Project;
use App\Entity\User;
use App\Form\DataTransformer\MembersDataTransformer;
use App\Repository\ProjectRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ProjectType.
 */
class ProjectType extends AbstractType
{
    public function __construct(private readonly TranslatorInterface $translator, private readonly MembersDataTransformer $membersDataTransformer)
    {
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string, mixed> $options Form options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if($options['include_name']) {
            $builder
                ->add(
                    'name',
                    TextType::class,
                    [
                        'label' => 'label.project_name',
                        'required' => true,
                        'attr' => ['max_length' => 64],
                        'label_attr' => ['class' => 'fw-bold'],
                    ]);
        }

        if($options['include_members']) {
            $builder
                ->add(
                    'members',
                    TextType::class,
                    [
                        'required' => false,
                        'mapped' => false,
                        'label' => 'label.members'
                    ]
                );
        }

        $builder->get('members')->addModelTransformer(
            $this->membersDataTransformer
        );

        $builder->get('members')->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event){
            $membersFormField = $event->getForm();
            $membersFieldValue = $event->getData();

            if (!empty($membersFieldValue)) {
                if (!preg_match_all('/^[a-zA-Z0-9.]+(,\s*[a-zA-Z0-9.]+)*$/', $membersFieldValue)) {
                    $membersFormField->addError(new FormError($this->translator->trans('message.members_invalid_format')));
                }
            }
        });
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'include_name' => true,
            'include_members' => true,
        ]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return 'project';
    }
}
