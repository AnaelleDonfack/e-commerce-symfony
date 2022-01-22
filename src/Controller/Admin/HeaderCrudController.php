<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;

class HeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title','Titre du header'),
            TextareaField::new('content', 'Contenu du Header'),
            TextField::new('btnTitle','Titre du bouton'),
            TextField::new('btnUrl','Url de destination du bouton'),
            ImageField::new('illustration')
                //Indique le dossier où easy admin met l'image
                ->setBasePath('uploads/headers')
                //Ici on met le chemin complet d'emplacement des images
                ->setUploadDir('public/uploads/headers')
                ->setFormType(FileUploadType::class)
                //Manière dont on veut encoder l'image : donne un nom aléatoire à l'image
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),

        ];
    }

}
