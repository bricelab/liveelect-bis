<?php

namespace App\Controller\Admin;

use App\Entity\Candidat;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Bundle\SecurityBundle\Security;

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
            ->showEntityActionsInlined(!$this->isGranted('ROLE_SUPER_ADMIN'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setPermission(Action::BATCH_DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::EDIT, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->hideOnForm();
        yield AssociationField::new('scrutin', 'Scrutin')->setPermission('ROLE_SUPER_ADMIN');
        yield ImageField::new('logo', 'Logo')
            ->setBasePath('uploads/candidats/logos/')
            ->setUploadDir('public/uploads/candidats/logos/')
        ;
        yield TextField::new('sigle', 'Sigle');
        yield TextField::new('nom', 'Nom');
        yield IntegerField::new('position', 'Position sur le bulletin');
    }

    public function configureFilters(Filters $filters): Filters
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $filters->add(EntityFilter::new('scrutin', 'Scrutin'));
        }
        return $filters
            ->add('sigle')
            ->add('nom')
            ->add('position')
        ;
    }
}
