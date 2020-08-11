<?php

namespace App\Controller;

use App\Entity\Document;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class LibraryController extends AbstractController
{
    /* 
     * @Route("/documents", name="documents_get")
     */
    public function list(Request $request, DocumentRepository $documentRepository)
    {
        $documents = $documentRepository->findAll();
        $documentsAsArray = [];
        
        foreach ($documents as $document)
        {
            $documentsAsArray[] = [
                'id'    => $document->getId(),
                'title' => $document->getTitle(),
                'image' => $document->getImage()
            ];
        }
        
        $response = new JsonResponse();
        $response->setData(
            [
                'sucess' => true,
                'data' => $documentsAsArray
            ]
        );

        return $response;
    }

    /* 
     * @Route("/document/create", name="document_create")
     */
    public function createDocument(Request $request, EntityManagerInterface $em)
    {
        $document = new Document();
        $response = new JsonResponse();
        $title = $request->get('title', null);
        
        if(empty($title))
        {
            $response->setData(
                [
                    'sucess' => false,
                    'error' => 'Title cannot be empty',
                    'data' => null
                ]
            );
            return $response;
        }

        $document->setTitle($title);
        // We tell to EntityManager to manage the new $document object. 
        $em->persist($document);
        // We tell to EntityManager that $document object should be sent to db. 
        $em->flush();
        
        $response->setData(
            [
                'sucess' => true,
                'data' => [
                    [
                        'id'    => $document->getId(),
                        'title' => $document->getTitle()
                    ]
                ]
            ]
        );
        return $response;
    }
}