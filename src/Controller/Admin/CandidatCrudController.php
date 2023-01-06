<?php

namespace App\Controller\Admin;

use App\Entity\Candidat;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CandidatCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Candidat::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('un candidat')
            ->setEntityLabelInPlural('Liste des candidats')
            ;
    }

//    public function configureActions(Actions $actions): Actions
//    {
//        return $actions
//            ->addBatchAction(Action::BATCH_DELETE)
//            ->add(Crud::PAGE_INDEX, Action::NEW)
//            ->add(Crud::PAGE_INDEX, Action::EDIT)
//            ->add(Crud::PAGE_INDEX, Action::DELETE)
//
//            ->add(Crud::PAGE_DETAIL, Action::EDIT)
//            ->add(Crud::PAGE_DETAIL, Action::DELETE)
//            ;
//    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'Identifiant')->onlyOnDetail();
        yield AssociationField::new('scrutin', 'Scrutin');
        yield ImageField::new('logo', 'Logo')
            ->setBasePath('uploads/candidats/logos/')
            ->setUploadDir('public/uploads/candidats/logos/')
        ;
        yield TextField::new('sigle', 'Sigle');
        yield TextField::new('nom', 'Nom');
        yield IntegerField::new('position', 'Position sur le bulletin');
    }
}
