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

use Pimcore\Bundle\PortalEngineBundle\Service\Document\LanguageVariantService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ChangeUserDataForm
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Form
 */
class ChangeUserDataForm extends AbstractType
{
    /**
     * @var LanguageVariantService
     */
    protected $languageVariantService;

    /**
     * ChangeUserDataForm constructor.
     *
     * @param LanguageVariantService $languageVariantService
     */
    public function __construct(LanguageVariantService $languageVariantService)
    {
        $this->languageVariantService = $languageVariantService;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => true,
                    'label' => 'portal-engine.user-data.form_label_email',
                    'empty_data' => $options['data']['email'],
                    'attr' => [
                        'disabled' => 'disabled'
                    ]
                ]
            )
            ->add(
                'firstname',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'portal-engine.user-data.form_label_firstname',
                    'empty_data' => $options['data']['firstname']
                ]
            )
            ->add(
                'lastname',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'portal-engine.user-data.form_label_lastname',
                    'empty_data' => $options['data']['lastname']
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'portal-engine.user-data.change_user_data_button',
                ]
            );

        $preferredLanguageChoices = $this->languageVariantService->getPreferredLanguageChoices();
        if (sizeof($preferredLanguageChoices)) {
            $builder->add(
                'preferredLanguage',
                ChoiceType::class,
                [
                    'required' => true,
                    'label' => 'portal-engine.user-data.form_label_preferred_language',
                    'empty_data' => $options['data']['preferredLanguage'],
                    'choices' => $preferredLanguageChoices
                ]
            );
        }
    }
}
