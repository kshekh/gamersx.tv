<?php


namespace App\Admin;


use App\Entity\UserGroup;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\UserBundle\Admin\Model\UserAdmin;
use Sonata\UserBundle\Form\Type\RolesMatrixType;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Sonata\AdminBundle\Form\Type\ModelType;
use App\Model\SecurityRolesType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use App\Entity\Group;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class CustomUserAdmin extends AbstractAdmin
{
    protected function configureFormOptions(array &$formOptions): void
    {
        $formOptions['validation_groups'] = ['Default'];

        if (!$this->hasSubject() || null === $this->getSubject()->getId()) {
            $formOptions['validation_groups'][] = 'Registration';
        } else {
            $formOptions['validation_groups'][] = 'Profile';
        }
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('username', null, [
                'route' => ['name' => 'edit'],
            ])
            ->add('email', null, ['label' => 'E-mail Address'])
            ->add('groups')
            ->add('enabled', null, ['editable' => true])
            ->add('createdAt');

        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $list
                ->add('impersonating', FieldDescriptionInterface::TYPE_STRING, [
                    'virtual_field' => true,
                    'template' => '@SonataUser/Admin/Field/impersonating.html.twig',
                ]);
        }
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('username')
            ->add('email')
            ->add('groups');
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('username')
            ->add('email');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        // define group zoning
        $form
            ->tab('User')
            ->with('Profile', ['class' => 'col-md-6'])->end()
            ->with('General', ['class' => 'col-md-6'])->end()
            ->with('Social', ['class' => 'col-md-6'])->end()
            ->end()
            ->tab('Security')
            ->with('Status', ['class' => 'col-md-4'])->end()
            ->with('Group', ['class' => 'col-md-4'])->end()
            ->with('Keys', ['class' => 'col-md-4'])->end()
            ->with('Roles', ['class' => 'col-md-12'])->end()
            ->end();

        $now = new \DateTime();

        $genderOptions = [
            'choices' => \call_user_func([$this->getClass(), 'getGenderList']),
            'required' => true,
            'translation_domain' => $this->getTranslationDomain(),
        ];

        $form
            ->tab('User')
            ->with('General')
            ->add('username')
//            ->add('email')
            ->add('email', null, ['label' => 'E-Mail-Address'])
            ->add('plainPassword', TextType::class, [
                'required' => (!$this->getSubject() || null === $this->getSubject()->getId()),
            ])
            ->end()
            ->with('Profile')
            ->add('dateOfBirth', DateType::class, [
                'label' => 'Date of birth',
                'years' => range(1900, $now->format('Y')),
//                'datepicker_use_button' => true, // Ensure date picker uses buttons
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => '1900-01-01',
                        'message' => 'This value is not valid.',
                    ]),
                    new LessThanOrEqual([
                        'value' => $now->format('Y-m-d'),
                        'message' => 'This value is not valid.',
                    ]),
                ],
            ])
            ->add('firstname', null, ['required' => false])
            ->add('lastname', null, ['required' => false])
            ->add('website', UrlType::class, ['required' => false])
            ->add('biography', TextType::class, ['required' => false])
            ->add('gender', ChoiceType::class, $genderOptions)
            ->add('locale', LocaleType::class, ['required' => false])
            ->add('timezone', TimezoneType::class, ['required' => false])
            ->add('phone', null, ['required' => false])
            ->end()
            ->with('Social')
            ->add('facebookUid', null, ['required' => false])
            ->add('facebookName', null, ['required' => false])
            ->add('twitterUid', null, ['required' => false])
            ->add('twitterName', null, ['required' => false])
            ->add('gplusUid', null, ['required' => false, 'label' => "Google+ Uid"])
            ->add('gplusName', null, ['required' => false, 'label' => "Google+ Name"])
            ->end()
            ->end()
            ->tab('Security')
            ->with('Status')
            ->add('enabled', null, ['required' => false])
            ->end()
            ->with('Group')
            ->add('groups', ModelType::class, [
                'class' => Group::class,
                'property' => 'name',
                'expanded' => true,
                'multiple' => true,
            ])
            ->end()
            ->with('Roles')
            ->add('roles', SecurityRolesType::class, [
                'label' => 'Roles',
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ])
            ->end()
            ->with('Keys')
            ->add('token', null, ['required' => false])
            ->add('twoStepCode', null, ['required' => false, 'label' => "Two Step Verification Code"])
            ->end()
            ->end();
    }

    private function getRolesBuilder()
    {
        return $this->getConfigurationPool()->getInstance('sonata.user.roles_builder');
//        return $this->getConfigurationPool()->getContainer()->get('sonata.user.roles_builder');
    }

    protected function configureExportFields(): array
    {
        // Avoid sensitive properties to be exported.
        return array_filter(
            parent::configureExportFields(),
            static fn (string $v): bool => !\in_array($v, ['password', 'salt'], true)
        );
    }
}