<?php

namespace App\Controller\Admin;

use App\Entity\Task;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TaskCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Task::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideWhenCreating()->setDisabled(true);
        yield TextField::new('name', 'Название');
        yield TextEditorField::new('description', 'Описание');

        yield AssociationField::new('project', 'Проект')
            ->setCrudController(ProjectCrudController::class);

        yield AssociationField::new('status', 'Статус')
            ->setCrudController(StatusCrudController::class);

        yield AssociationField::new('type', 'Тип')
            ->setCrudController(TaskTypeCrudController::class);

        yield AssociationField::new('priority', 'Приоритет')
            ->setCrudController(TaskPriorityCrudController::class);

        yield AssociationField::new('responsible', 'Ответственный')
            ->setCrudController(UserCrudController::class);

        yield AssociationField::new('createdUser', 'Постановщик')
            ->setCrudController(UserCrudController::class);

        yield DateField::new('createdAt', 'Дата создания')->hideWhenCreating()->setDisabled(true);

        yield DateField::new('endDate', 'Дата окончания');

        yield TextField::new('spenttime', 'Затраченное время');
    }

}
