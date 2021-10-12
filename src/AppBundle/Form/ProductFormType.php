<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Model\Product;
use AppBundle\Model\Category;
use AppBundle\Model\Brand;
use AppBundle\Repository\BrandRepository;
use AppBundle\Repository\CategoryRepository;


class ProductFormType extends AbstractType implements DataMapperInterface {

    protected $container;

    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $newCode = "P".rand(1000,1999)."".substr(time(), 0,3);
        $builder
            ->add('code', TextType::class,[
                "label"=>"Product Number",
                "data" => isset($options["data"]) ? $options["data"]->getCode() : $newCode,
                "attr"=>[
                    "readonly"=>"true"
                ],
            ])
            ->add('name', TextType::class,["label"=>"Name"])
            ->add('price', MoneyType::class,["label"=>"Price",'divisor' => 100])
            ->add('expired', DateType::class,["label"=>"Expired", 'format' => 'yyyy-MM-dd'])
            ->add('brand', EntityType::class, array(
                'class' => Brand::class,
                'query_builder' => function (BrandRepository $er) {
                    return $er->createQueryBuilder('x')->orderBy('x.name', 'ASC');
                },
                'multiple' => false,
                'placeholder' => 'Choose an option',
            ))
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'query_builder' => function (CategoryRepository $er) {
                    return $er->createQueryBuilder('x')->orderBy('x.name', 'ASC');
                },
                'multiple' => true,
                'expanded' => true,
            ])
           ->add('image',FileType::class,array('required'=>false))
           ->add('save', SubmitType::class, array('label' => 'Save', 'attr' => array('class' => 'btn btn-default')))
        ;
        $builder->setDataMapper($this);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'empty_data' => null,
        ]);
    }

    public function mapDataToForms($data, $forms) {
        if (null !== $data) {
            $forms = iterator_to_array($forms);
            $forms['code']->setData($data->getName());
            $forms['name']->setData($data->getCode());
            $forms['price']->setData($data->getPrice());
            $forms['expired']->setData($data->getExpired());
            $forms['brand']->setData($data->getBrand());
            $forms['categories']->setData($data->getCategories());
        }
    }

    public function mapFormsToData($forms, &$data) {
        $forms = iterator_to_array($forms);
        $code = $forms['code']->getData();
        $name = $forms['name']->getData();
        $price = $forms['price']->getData();
        $expired = $forms['expired']->getData();
        $brand = $forms['brand']->getData();
        $categories = $forms['categories']->getData();
        $file = $forms["image"]->getData();

        if ($data == null) {
            $data = new Product;
            $data->setCode($code);
            $data->setName($name);
            $data->setPrice($price);
            $data->setExpired($expired);
            $data->setBrand($brand);
            $data->setCategories($categories);
            if($file){
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
                $file->move(
                    $this->container->getParameter('upload_dir'),
                    $fileName
                );
                $data->setImage($fileName);     
            }
        }else{
            $data->setName($name);
            $data->setPrice($price);
            $data->setExpired($expired);
            $data->setBrand($brand);
            $data->setCategories($categories);

            if($file){

                if($data->getImage()){
                    if(file_exists($this->container->getParameter("upload_dir").'/'.$data->getImage())){
                        unlink($this->container->getParameter("upload_dir").'/'.$data->getImage());
                    }
                }
                
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
                $file->move(
                    $this->container->getParameter('upload_dir'),
                    $fileName
                );
                $data->setImage($fileName);     
            }


        }
    }

    private function generateUniqueFileName(){
        return md5(uniqid());
    }

}