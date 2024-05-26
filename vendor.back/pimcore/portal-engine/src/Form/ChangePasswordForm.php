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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ChangePasswordForm
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Form
 */
class ChangePasswordForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'password',
                PasswordType::class,
                [
                    'required' => true,
                    'label' => 'portal-engine.user-data.form_label_password',
                ]
            )
            ->add(
                'passwordRepeat',
                PasswordType::class,
                [
                    'required' => true,
                    'label' => 'portal-engine.user-data.form_label_password_repeat',
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'portal-engine.user-data.change_password_button',
                ]
            );
    }
}
