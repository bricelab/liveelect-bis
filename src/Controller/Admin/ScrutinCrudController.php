<?php

namespace App\Controller\Admin;

use App\EasyAdmin\Field\TypeScrutinField;
use App\Entity\Scrutin;
use App\Enum\TypeScrutin;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ScrutinCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Scrutin::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('un scrutin')
            ->setEntityLabelInPlural('Liste des scrutins')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'Identifiant')->onlyOnDetail();
        yield TextField::new('name', 'Libellé');
        yield ChoiceField::new('year', 'Année')
            ->setChoices(static function () {
                $todayYear = intval(date('Y'));
                $years = [];
                for ($year = $todayYear - 1; $year < $todayYear + 4; $year++) {
                    $years[$year] = $year;
                }
                return $years;
            })
        ;
        yield ChoiceField::new('type', 'Type de scrutin')
            ->setChoices(array_combine(
                [
                    TypeScrutin::Presidentiel->name,
                    TypeScrutin::Legislative->name,
                    TypeScrutin::Communale->name,
                ],
                [
                    TypeScrutin::Presidentiel->value,
                    TypeScrutin::Legislative->value,
                    TypeScrutin::Communale->value,
                ]
            ))
            ->renderAsBadges()
        ;
        yield BooleanField::new('published', 'Publié ?');
//        yield DateTimeField::new('publishedAt', 'Publié le');
    }
}
