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
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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

    #[Route('/reports/tasks', name: 'report-tasks', methods: ['GET'])]
    public function tasks(Request $request, TaskRepository $taskRepository): Response
    {
        $from = $request->get('from');
        $to = $request->get('to');

        if (empty($from)) {
            $from = new \DateTime();
            $from->modify('-1 month');
        } else {
            $from = new \DateTime($from);
        }

        if (empty($to)) {
            $to = new \DateTime();
        } else {
            $to = new \DateTime($to);
        }

        $tasks = $taskRepository->createQueryBuilder('t')
            ->leftJoin('t.project', 'p')
            ->leftJoin('t.status', 's')
            ->leftJoin('t.responsible', 'r')
            ->select([
                'r.lastName as responsibleLastName',
                'r.firstName as responsibleFirstName',
                'r.secondName as responsibleSecondName',
                'p.name as project',
                's.name as status',
                't.id',
                't.name',
                't.createdAt',
                't.spenttime'
            ])
            ->andWhere('t.createdAt > :from')
            ->andWhere('t.createdAt < :to')
            ->setParameters([
                'from' => $from,
                'to' => $to
            ])->getQuery()->getArrayResult();

        $tasksResult = [];
        foreach ($tasks as $t) {
            $name = $t['responsibleLastName'] . " " . mb_substr($t['responsibleFirstName'], 0,
                    1) . ". " . mb_substr($t['responsibleSecondName'], 0, 1) . ".";

            $tasksResult[] = [
                'id' => $t['id'],
                'name' => $t['name'],
                'createdAt' => $t['createdAt'],
                'project' => $t['project'],
                'status' => $t['status'],
                'responsible' => $name,
                'spenttime' => $t['spenttime'],
            ];
        }

        return $this->render('reports/tasks.html.twig', [
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'tasks' => $tasksResult,
            'headers' => [
                'Номер',
                'Дата',
                'Проект',
                'Название',
                'Статус',
                'Исполнитель',
                'Время'
            ],
        ]);
    }

    #[Route('/reports/tasks-excel', name: 'report-tasks-excel', methods: ['GET'])]
    public function tasksExcel(Request $request, TaskRepository $taskRepository): Response
    {
        $from = $request->get('from');
        $to = $request->get('to');

        $fileName = 'tasks_report' . $from . '-' . $to . '.xlsx';
        if (empty($from)) {
            $from = new \DateTime();
            $from->modify('-1 month');
        } else {
            $from = new \DateTime($from);
        }

        if (empty($to)) {
            $to = new \DateTime();
        } else {
            $to = new \DateTime($to);
        }

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $headers = [
            'A' => 'Номер',
            'B' => 'Дата',
            'C' => 'Проект',
            'D' => 'Название',
            'E' => 'Статус',
            'F' => 'Исполнитель',
            'G' => 'Время'
        ];
        foreach ($headers as $letter => $header) {
            $sheet->setCellValue($letter . "1", $header);
        }

        $tasks = $taskRepository->createQueryBuilder('t')
            ->leftJoin('t.project', 'p')
            ->leftJoin('t.status', 's')
            ->leftJoin('t.responsible', 'r')
            ->select([
                'p.name as project',
                's.name as status',
                'r.lastName as responsibleLastName',
                'r.firstName as responsibleFirstName',
                'r.secondName as responsibleSecondName',
                't.id',
                't.name',
                't.createdAt',
                't.spenttime'
            ])
            ->andWhere('t.createdAt > :from')
            ->andWhere('t.createdAt < :to')
            ->setParameters([
                'from' => $from,
                'to' => $to
            ])->getQuery()->getArrayResult();

        $index = 2;

        foreach ($tasks as $task) {
            $name = $task['responsibleLastName'] . " " . mb_substr($task['responsibleFirstName'], 0,
                    1) . ". " . mb_substr($task['responsibleSecondName'], 0, 1) . ".";

            $sheet->setCellValue('A' . $index, $task['id']);
            $sheet->setCellValue('B' . $index, $task['createdAt']->format('d.m.Y H:i'));
            $sheet->setCellValue('C' . $index, $task['project']);
            $sheet->setCellValue('D' . $index, $task['name']);
            $sheet->setCellValue('E' . $index, $task['status']);
            $sheet->setCellValue('F' . $index, $name);
            $sheet->setCellValue('G' . $index, $task['spenttime']);

            $index++;
        }

        $sheet->setTitle("Отчет");

        for ($i = 'A'; $i != $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($temp_file);

        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    #[Route('/reports/users', name: 'report-users', methods: ['GET'])]
    public function users(Request $request, UserRepository $userRepository): Response
    {
        $from = $request->get('from');
        $to = $request->get('to');

        if (empty($from)) {
            $from = new \DateTime();
            $from->modify('-1 month');
        } else {
            $from = new \DateTime($from);
        }

        if (empty($to)) {
            $to = new \DateTime();
        } else {
            $to = new \DateTime($to);
        }

        $from->setTime(0, 0);
        $to->setTime(23, 59);

        $tasks = $userRepository->createQueryBuilder('u')
            ->leftJoin('u.tasks', 't')
            ->select(['u', 't'])
            ->andWhere('t.createdAt >= :from')
            ->andWhere('t.createdAt <= :to')
            ->setParameters([
                'from' => $from,
                'to' => $to
            ])->getQuery()->getArrayResult();

        $tasksResult = [];
        foreach ($tasks as $t) {
            $name = $t['lastName'] . " " . mb_substr($t['firstName'], 0,
                    1) . ". " . mb_substr($t['secondName'], 0, 1) . ".";

            $tasksResult[] = [
                'count' => count($t['tasks']),
                'name' => $name,
                'time' => array_reduce($t['tasks'], function ($carry, $item) {
                    return $carry + (float)$item['spenttime'];
                }, 0). ' ч.',
            ];
        }

        return $this->render('reports/users.html.twig', [
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'tasks' => $tasksResult,
            'headers' => [
                'Исполнитель',
                'Количество задач',
                'Затрачено за отчетный период',
            ],
        ]);
    }

    #[Route('/reports/users-excel', name: 'report-users-excel', methods: ['GET'])]
    public function usersExcel(Request $request, UserRepository $userRepository): Response
    {
        $from = $request->get('from');
        $to = $request->get('to');

        $fileName = 'users_report' . $from . '-' . $to . '.xlsx';
        if (empty($from)) {
            $from = new \DateTime();
            $from->modify('-1 month');
        } else {
            $from = new \DateTime($from);
        }

        if (empty($to)) {
            $to = new \DateTime();
        } else {
            $to = new \DateTime($to);
        }

        $from->setTime(0, 0);
        $to->setTime(23, 59);
//
//        dump($from, $to); die();

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $headers = [
            'A' => 'Исполнитель',
            'B' => 'Количество задач',
            'C' => 'Затрачено за отчетный период',
        ];
        foreach ($headers as $letter => $header) {
            $sheet->setCellValue($letter . "1", $header);
        }

        $tasks = $userRepository->createQueryBuilder('u')
            ->leftJoin('u.tasks', 't')
            ->select(['u', 't'])
            ->andWhere('t.createdAt >= :from')
            ->andWhere('t.createdAt <= :to')
            ->setParameters([
                'from' => $from,
                'to' => $to
            ])->getQuery()->getArrayResult();

        $tasksResult = [];
        foreach ($tasks as $t) {
            $name = $t['lastName'] . " " . mb_substr($t['firstName'], 0,
                    1) . ". " . mb_substr($t['secondName'], 0, 1) . ".";

            $tasksResult[] = [
                'count' => count($t['tasks']),
                'name' => $name,
                'time' => array_reduce($t['tasks'], function ($carry, $item) {
                        return $carry + (float)$item['spenttime'];
                    }, 0). ' ч.',
            ];
        }

        $index = 2;

        foreach ($tasksResult as $task) {
            $sheet->setCellValue('A' . $index, $task['name']);
            $sheet->setCellValue('B' . $index, $task['count']);
            $sheet->setCellValue('C' . $index, $task['time']);

            $index++;
        }

        $sheet->setTitle("Отчет");

        for ($i = 'A'; $i != $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($temp_file);

        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
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
            yield MenuItem::linkToRoute('Ресурсный учёт по задачам', 'fa-solid fa-align-justify', 'report-tasks');
            yield MenuItem::linkToRoute('Ресурсный учёт по исполнителям', 'fa-solid fa-align-justify', 'report-users');

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
