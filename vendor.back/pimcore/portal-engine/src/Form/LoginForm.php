<?php

/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */

namespace Pimcore\Bundle\PortalEngineBundle\Form;

use Pimcore\Bundle\PortalEngineBundle\Event\Auth\LoginFieldTypeEvent;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\PortalUser;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginForm extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher, TranslatorInterface $translator, array $fields)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->fields = $fields;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $event = new LoginFieldTypeEvent();
        $this->eventDispatcher->dispatch($event);

        if ($event->getUseEmailField() && $this->fields === ['email']) {
            $builder
                ->add(
                    'username',
                    EmailType::class,
                    [
                        'required' => true,
                        'label' => 'portal-engine.auth.form_label_email',
                        'attr' => [
                            'autofocus' => true
                        ]
                    ]
                );
        } else {
            $classDefinition = ClassDefinition::getById(PortalUser::classId());
            $fieldLabels = [];
            foreach ($this->fields as $fieldName) {
                $fieldDefinition = $classDefinition->getFieldDefinition($fieldName);
                $fieldTitle = $fieldDefinition->getTitle();
                $fieldLabels[] = $this->translator->trans($fieldTitle, [], 'admin');
            }

            $builder
                ->add(
                    'username',
                    TextType::class,
                    [
                        'required' => true,
                        'label' => implode(' / ', $fieldLabels),
                        'attr' => [
                            'autofocus' => true
                        ]
                    ]
                );
        }

        $builder
            ->add(
                'password',
                PasswordType::class,
                [
                    'required' => true,
                    'label' => 'portal-engine.auth.form_label_password',

                ]
            )->add(
                '_remember_me',
                CheckboxType::class,
                [
                    'label' => 'portal-engine.auth.form_remember_me',
                    'required' => false
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'portal-engine.auth.login_button',
                ]
            );
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        // we need to set this to an empty string as we want _username as input name
        // instead of login_form[_username] to work with the form authenticator out
        // of the box
        return '';
    }
}
