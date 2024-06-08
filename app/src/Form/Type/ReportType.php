<?php
/**
 * Report type.
 */

namespace App\Form\Type;

use App\Entity\Category;
use App\Entity\Enum\ReportStatus;
use App\Entity\Project;
use App\Entity\Report;
use App\Form\DataTransformer\MembersDataTransformer;
use App\Form\DataTransformer\TagsDataTransformer;
use App\Form\DataTransformer\UserDataTransformer;
use App\Service\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ReportType.
 */
class ReportType extends AbstractType
{
    public function __construct(private readonly TranslatorInterface $translator, private readonly TagsDataTransformer $tagsDataTransformer,  private readonly Security $security, private readonly UserServiceInterface $userService, private readonly UserDataTransformer $userDataTransformer)
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
        $title = $this->translator->trans('label.title').' *';
        $description = $this->translator->trans('label.description').' *';
        $category = $this->translator->trans('label.category').' *';

        $builder
            ->add(
            'title',
            TextType::class,
            [
                'label' => $title,
                'required' => true,
                'attr' => ['max_length' => 64],
                'label_attr' => ['class' => 'fw-bold'],
            ])
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => $description,
                    'required' => true,
                    'attr' => [
                        'max_length' => 500,
                        'rows' => 6
                    ],
                    'label_attr' => ['class' => 'fw-bold']
                ])
            ->add(
                'category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'choice_label' => function ($category): string {
                        return $category->getTitle();
                    },
                    'label' => $category,
                    'required' => true,
                    'placeholder' => 'label.choose_category',
                    'label_attr' => ['class' => 'fw-bold']
                ]
            )
            ->add(
                'status',
                ChoiceType::class,
                [
                    'required' => true,
                    'choices' => [
                        ReportStatus::STATUS_PENDING->label() => ReportStatus::STATUS_PENDING,
                        ReportStatus::STATUS_IN_PROGRESS->label() => ReportStatus::STATUS_IN_PROGRESS,
                        ReportStatus::STATUS_COMPLETED->label() => ReportStatus::STATUS_COMPLETED,
                    ]
                ]
            )
            ->add(
                'project',
                EntityType::class,
                [
                    'class' => Project::class,
                    'choices' => $options['projects'],
                    'choice_label' => 'name',
                    'placeholder' => 'label.choose_project',
                    'required' => false
                ]
            )
            ->add(
                'assignedTo',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'label.assigned_to',
                    'attr' => ['placeholder' => 'label.assigned_to'],
                ]
            )
        ;
        $builder
            ->add(
                'tags',
                TextType::class,
                [
                    'label' => 'label.tags',
                    'required' => false,
                    'attr' => ['max_length' => 128]
                ]
            )
            ->add(
                'file',
                FileType::class,
                [
                    'mapped' => false,
                    'label' => 'label.attachment',
                    'required' => false,
                    'constraints' => new Image(
                        [
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/png',
                                'image/jpeg',
                                'image/pjpeg',
                                'image/jpeg',
                                'image/pjpeg',
                            ],
                        ]
                    ),
                ]
            );

        $builder->get('tags')->addModelTransformer(
            $this->tagsDataTransformer
        );

        $builder->get('tags')->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event){
            $tagsFormField = $event->getForm();
            $tagsFieldValue = $event->getData();

            if (!empty($tagsFieldValue)) {
                if (!preg_match_all('/^[a-zA-Z0-9]+(,\s*[a-zA-Z0-9]+)*$/', $tagsFieldValue)) {
                    $tagsFormField->addError(new FormError($this->translator->trans('message.tag_invalid_format')));
                }
            }
        });

        $builder->get('assignedTo')->addModelTransformer(
            $this->userDataTransformer
        );

        $builder->get('assignedTo')->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
            $assignedToFormField = $event->getForm();
            $assignedToFieldValue = $event->getData();
            $project = $assignedToFormField->getParent()->get('project')->getData();

            if (!empty($assignedToFieldValue)) {
                if (!preg_match_all('/^[a-zA-Z0-9.]+$/', $assignedToFieldValue)) {
                    $assignedToFormField->addError(new FormError($this->translator->trans('message.assigned_to_invalid_format')));
                }
                else {
                    $user = $this->userService->findOneByUsername($assignedToFieldValue);
                    if (!in_array($user, $project->getMembers()->toArray())) {
                        $assignedToFormField->addError(new FormError($this->translator->trans('message.cannot_assign_report')));
                    }
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
            'data_class' => Report::class,
            'projects' => [],
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
        return 'report';
    }
}
