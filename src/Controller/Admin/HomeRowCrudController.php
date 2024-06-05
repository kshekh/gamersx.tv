<?php

namespace App\Controller\Admin;

use App\Entity\HomeRow;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class HomeRowCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return HomeRow::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            TextField::new('title'),
            NumberField::new('sort_index'),
            TextField::new('layout'),
            ArrayField::new('options'),
            DateTimeField::new('is_published_start')
                ->onlyOnForms(),
            DateTimeField::new('is_published_end')
                ->onlyOnForms(),
            NumberField::new('row_padding_top')
                ->onlyOnForms(),
            NumberField::new('row_padding_bottom')
                ->onlyOnForms(),
            TextField::new('timezone'),
            BooleanField::new('is_published')
                ->renderAsSwitch(false),
            BooleanField::new('is_glow_styling')
                ->renderAsSwitch(false),
            BooleanField::new('is_corner_cut')
                ->renderAsSwitch(false),
            BooleanField::new('on_gamers_xtv')
                ->renderAsSwitch(false),
        ];
    }
}
