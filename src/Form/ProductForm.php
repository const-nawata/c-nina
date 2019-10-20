<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options )
    {
		$widget_css	= 'form-control form-control-sm';

        $builder
			->add('name', TextType::class, ['attr' => ['class'=> $widget_css], 'empty_data' => '', 'required' => false ] )
			->add('article', TextType::class, ['attr' => ['class'=> $widget_css], 'empty_data' => '', 'required' => false ] )
			->add('tradePrice', MoneyType::class, ['currency'=>false, 'attr' => ['class'=> $widget_css], 'required' => false ] )
			->add('price', MoneyType::class, ['currency'=>false, 'attr' => ['class'=> $widget_css], 'required' => false ] )
			->add('inPack', IntegerType::class, ['attr' => ['class'=> $widget_css], 'required' => false, 'empty_data' => 0 ] )
			->add('packs', IntegerType::class, ['attr' => ['class'=> $widget_css], 'required' => false, 'empty_data' => 0 ] )
			->add('outPack', IntegerType::class, ['attr' => ['class'=> $widget_css], 'required' => false, 'empty_data' => 0 ] )
			->add('description', TextareaType::class, ['attr' => ['class'=> $widget_css], 'required' => false ] )

			->add('id', HiddenType::class, [ 'mapped' => false, 'data' => $options['attr']['product_id'] ] )

			->add('currency', ChoiceType::class, [
				'mapped'	=> false,
				'attr' => ['class'=> 'custom-select currency-select'],

    'choices'  => array(
        'Maybe' => null,
        'Yes' => true,
        'No' => false,
    )
    		])

			->add('formCategories', ChoiceType::class, [
				'mapped'	=> false, 'multiple' => true, 'expanded' => true,
				'choices'	=> unserialize($options['attr']['formCategories']),

				'choice_attr'=> function(  $category, $key, $value){
					return ['class' => 'form-control'];
				},
			])
		;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
