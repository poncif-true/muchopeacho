<?php


namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class UserForm
 * @package App\Form
 */
class UserForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('displayUsername', TextType::class, [
                'label' => 'The username that will be used for battles'
            ])
            ->add('style', TextType::class, [
                'label' => 'Choose a style that defines you (optional)',
                'required' => false,
            ])
        ;
    }
}
