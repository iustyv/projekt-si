<?php
/**
 * Report type.
 */

namespace App\Form\Type;

use App\Entity\Category;
use App\Entity\Enum\ReportStatus;
use App\Entity\Report;
use App\Form\DataTransformer\TagsDataTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
    public function __construct(private readonly TranslatorInterface $translator, private readonly TagsDataTransformer $tagsDataTransformer)
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
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Report::class]);
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
