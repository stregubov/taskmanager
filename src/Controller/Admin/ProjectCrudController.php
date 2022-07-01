<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProjectCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Project::class;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideWhenCreating()->setDisabled(true),
            TextField::new('name', 'Название'),
            TextField::new('code', 'Символьный код'),
            AssociationField::new('team', 'Команда')
                ->setCrudController(TeamCrudController::class)
                ->setFormTypeOption('by_reference', false)
        ];
    }

}
