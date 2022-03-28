<?php

namespace App\Controller;

use App\Entity\Crud;
use App\Repository\CrudRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/crud")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/", name="api",  methods={"GET"})
     */
    public function index(CrudRepository $crudrepo): Response
    {
        return $this->json($crudrepo->findAll(), 200, []);
    }

    /**
     * @Route("/{id}", name="api_item",  methods={"GET"})
     */
    public function item(Crud $id): Response
    {
        return $this->json($id, 200, []);
    }

    /**
     * @Route("/post", name="post_api", methods={"POST"})
     */
    public function post( Request $request, SerializerInterface $serializer, EntityManagerInterface $em): Response
    {
        $jsonRecu = $request->getContent();

        try{
            $crud = $serializer->deserialize($jsonRecu, Crud::class, 'json');

            $em->persist($crud);

            $em->flush();

            return $this->json($crud, 201, []);

        }catch(NotEncodableValueException $e){

            return $this->json(
            [
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @Route("/update/{id}", name="update_api", methods={"PUT"})
     */
    public function update( CrudRepository $crudrepo, Request $request, $id, EntityManagerInterface $em): Response
    {
        $data = $crudrepo->find($id);
        
        try{
            $crud = json_decode($request->getContent(), true);

            $data->setTitle($crud['title']);
            $data->setContent($crud['content']);

            $em->persist($data);

            $em->flush();

            return $this->json($crud, 200, []);

        }catch(NotEncodableValueException $e){

            return $this->json(
            [
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * @Route("/edit/{id}", name="edit_api", methods={"PATCH"})
     */
    public function edit( CrudRepository $crudrepo, Request $request, $id, EntityManagerInterface $em): Response
    {
        $data = $crudrepo->find($id);
        
        try{
            $crud = json_decode($request->getContent(), true);

            $data->setTitle($crud['title']);
            $data->setContent($crud['content']);

            $em->persist($data);

            $em->flush();

            return $this->json($crud, 200, []);

        }catch(NotEncodableValueException $e){

            return $this->json(
            [
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @Route("/delete/{id}", name="delete_api", methods={"DELETE"})
     */
    public function delete( CrudRepository $crudrepo, $id ,EntityManagerInterface $em): Response
    {
        $data = $crudrepo->find($id);
        
        try{         
            $em->remove($data);

            $em->flush();

            return $this->json('Deleted successfully !');

        }catch(NotEncodableValueException $e){

            return $this->json(
            [
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
