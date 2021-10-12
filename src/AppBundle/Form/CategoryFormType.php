<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Model\Category;

class CategoryFormType extends AbstractType implements DataMapperInterface {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class,["label"=>"Name"])
            ->add('description', TextAreaType::class, ["attr" => ["cols" => "5", "rows" => "5"],"label"=>"Description"])
            ->add('save', SubmitType::class, array('label' => 'Save', 'attr' => array('class' => 'btn btn-default')))
        ;
        $builder->setDataMapper($this);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'empty_data' => null,
        ]);
    }

    public function mapDataToForms($data, $forms) {
        if (null !== $data) {
            $forms = iterator_to_array($forms);
            $forms['name']->setData($data->getName());
            $forms['description']->setData($data->getDescription());
        }
    }

    public function mapFormsToData($forms, &$data) {
        $forms = iterator_to_array($forms);
        $name = $forms['name']->getData();
        $description = $forms['description']->getData();
        if ($data == null) {
            $data = new Category;
            $data->setName($name);
            $data->setDescription($description);
        }else{
            $data->setName($name);
            $data->setDescription($description);
        }
    }

}