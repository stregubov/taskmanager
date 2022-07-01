<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use App\Entity\Role;
use App\Entity\Status;
use App\Entity\Task;
use App\Entity\TaskPriority;
use App\Entity\TaskType;
use App\Entity\Team;
use App\Entity\User;
use App\Repository\TaskRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private Security $security)
    {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->redirectToRoute('list');
    }

    #[Route('/list', name: 'list')]
    public function kanban(TaskRepository $taskRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $taskRepository->createQueryBuilder('t')->orderBy('t.id', 'desc')->getQuery();
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('list.html.twig', ['pagination' => $pagination, 'title' => 'Актуальные задачи']);
    }

    #[Route('/detail/{id}', name: 'detail')]
    public function detailTask(TaskRepository $taskRepository, int $id): Response
    {
        $task = $taskRepository->find($id);

        return $this->render('detail.html.twig', ['task' => $task]);
    }

    #[Route('/my-tasks', name: 'my-tasks')]
    public function myTasks(
        TaskRepository $taskRepository,
        Security $security,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $userId = $security->getUser()->getId();
        $query = $taskRepository->createQueryBuilder('t')->
        where('t.responsible = :userId')->setParameter('userId',
            $userId)->orderBy('t.id', 'desc')->getQuery();
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('list.html.twig', ['pagination' => $pagination, 'title' => 'Мои задачи']);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('TaskManager');
    }

    public function configureMenuItems(): iterable
    {
        //yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        if ($this->security->isGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToRoute('Актуальные задачи', 'fa-solid fa-list-check', 'list');
            yield MenuItem::linkToCrud('Задачи', 'fa-solid fa-bars-progress', Task::class);

            yield MenuItem::linkToCrud('Пользователи', 'fas fa-users', User::class);

            yield MenuItem::section('Отчеты');
            yield MenuItem::linkToCrud('Ресурсный учёт по задачам', 'fas fa-users', User::class);
            yield MenuItem::linkToCrud('Ресурсный учёт по исполнителям', 'fas fa-user-secret', Role::class);

            yield MenuItem::section('Справочники');
            yield MenuItem::linkToCrud('Проекты', 'fa-solid fa-sitemap', Project::class);
            yield MenuItem::linkToCrud('Команды', 'fa-solid fa-people-group', Team::class);
            yield MenuItem::linkToCrud('Статусы', 'fa-solid fa-crosshairs', Status::class);
            yield MenuItem::linkToCrud('Типы задач', 'fa-solid fa-text-height', TaskType::class);
            yield MenuItem::linkToCrud('Приоритеты задач', 'fa-solid fa-exclamation', TaskPriority::class);
            yield MenuItem::linkToCrud('Роли', 'fas fa-user-secret', Role::class);
        } else {
            yield MenuItem::linkToRoute('Мои задачи', 'fa-solid fa-list-check', 'list');
        }
    }
}
