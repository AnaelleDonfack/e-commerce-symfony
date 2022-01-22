<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return[
            TextField::new('name'),
            SlugField::new('slug')
                ->setTargetFieldName('name'),
            ImageField::new('illustration')
                //Indique le dossier où easy admin met l'image
                ->setBasePath('uploads/products')
                //Ici on met le chemin complet d'emplacement des images
                ->setUploadDir('public/uploads/products')
                ->setFormType(FileUploadType::class)
                //Manière dont on veut encoder l'image : donne un nom aléatoire à l'image
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            TextField::new('subtitle'),
            TextareaField::new('description'),
            BooleanField::new('isBest','tendance'),
            MoneyField::new('price')->setCurrency('EUR'),
            AssociationField::new('category')
            //CollectionField::new('category'),
        ];

    }

}
