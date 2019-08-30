<?php

namespace App\Form;

use App\Entity\Prodcategory;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProdcategoryForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options )
    {
		$widget_css	= 'form-control form-control-sm';

        $builder
			->add('id', HiddenType::class, [ 'mapped' => false, 'data' => $options['attr']['category_id'] ] )
			->add('name', TextType::class, ['attr' => ['class'=> $widget_css], 'required' => false ] )
			->add('description', TextareaType::class, ['attr' => ['class'=> $widget_css], 'required' => false ] )
			->add('isActive', CheckboxType::class, ['attr' => ['class'=> $widget_css], 'required' => false ] )
		;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Prodcategory::class,
        ]);
    }
}
