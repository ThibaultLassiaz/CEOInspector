<?php

namespace App\Controller;

use App\Message\FileMessage;
use App\Service\Utils\ApiService;
use App\Service\Utils\EntityService;
use App\Service\Utils\FileService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class ApiController extends AbstractController
{
    private ApiService $apiService;
    private FileService $fileService;
    private EntityService $entityService;
    private MessageBusInterface $bus;

    public function __construct(
        MessageBusInterface $bus,
        ApiService $apiService,
        FileService $fileService,
        EntityService $entityService,
    ) {
        $this->bus = $bus;
        $this->apiService = $apiService;
        $this->fileService = $fileService;
        $this->entityService = $entityService;
    }

    #[Route('/getCsv', name: 'getCsv')]
    public function getCsvFromFileId(Request $request): Response
    {
        $filePath = $request->request->get('filePath');
        $data = $this->apiService->findByFilePath($filePath);
        $file = $this->apiService->findFileByPath($filePath);

        $projectDir = $this->getParameter('kernel.project_dir');

        $output = fopen($projectDir.DIRECTORY_SEPARATOR.'public'.$filePath.'.csv', 'w');

        fwrite($output, 'Entreprise,Dirigeants');
        fwrite($output, PHP_EOL);

        foreach ($data['companies'] as $company) {
            fwrite($output, $company->getName());
            fwrite($output, ',');
            fwrite($output, $company->getLeader());
            fwrite($output, PHP_EOL);
        }

        fclose($output);

        $data = [
            'path' => $file->getPath(),
            'name' => $file->getName(),
        ];

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/deleteFile', name: 'deleteFile')]
    public function deleteFile(Request $request): Response
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $filePath = $request->request->get('filePath');

        $filePathInProject = $projectDir.DIRECTORY_SEPARATOR.'public'.$filePath;

        $this->apiService->deleteFileByPath($filePath);

        if (file_exists($filePathInProject.'.csv')) {
            unlink($filePathInProject.'.csv');
        }

        return new Response('Success', 200);
    }

    #[Route('/', name: 'index')]
    public function uploadAction(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('file', FileType::class, ['label' => false])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            $fileName = $file->getClientOriginalName();
            $fileContent = file_get_contents($file);

            $projectDir = $this->getParameter('kernel.project_dir');
            $filePath = $projectDir.'\src\Files\Input\\file.xlsx';

            $maxFileId = $this->apiService->findMaxFileId();
            $csvId = $maxFileId + 1;
            $parts = explode('.xlsx', $fileName);
            $csvPath = $projectDir."\src\Files\Input\\".$parts[0].'_'.$csvId.'.csv';

            $this->fileService->createXlsx($fileContent, $filePath);
            $this->fileService->createCsv($filePath, $csvPath);
            $csv = $this->fileService->openCsvFile($csvPath);
            $file = $this->entityService->createFile($maxFileId, count($csv), $fileName);

            $this->bus->dispatch(new FileMessage(
                $file->getFileId(),
                $csvPath
            ));
        }

        $files = $this->apiService->findFiles();

        return $this->render('upload.html.twig', [
            'form' => $form->createView(),
            'files' => $files,
        ]);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/checkFile', name: 'checkFile')]
    public function checkFile(Request $request): JsonResponse
    {
        $fileId = $request->request->get('fileId');

        $file = $this->apiService->findFileById($fileId);
        $count = $this->apiService->countTreated($file);

        $data = ['file' => $count];

        return new JsonResponse($data);
    }
}
