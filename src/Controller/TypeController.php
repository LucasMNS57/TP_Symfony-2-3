<?php

namespace App\Controller;

use App\Entity\Type;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class TypeController extends AbstractController
{
    #[Route('/types', name: 'app_types', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne tous les types.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Type::class, groups: ['type:read']))
        )
    )]
    #[OA\Tag(name: 'Types')]
    #[Security(name: 'Bearer')]
    public function index(TypeRepository $typeRepository): JsonResponse
    {
        $types = $typeRepository->findAll();

        return $this->json([
            'types' => $types,
        ], context: [
            'groups' => ['type:read']
        ]);
    }

    #[Route('/type/{id}', name: 'app_type_get', methods: ['GET'])]
    #[OA\Tag(name: 'Types')]
    public function get(Type $type): JsonResponse
    {
        return $this->json($type, context: [
            'groups' => ['type:read'],
        ]);
    }

    #[Route('/types', name: 'app_type_add', methods: ['POST'])]
    #[OA\Tag(name: 'Types')]
    public function add(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $type = new Type();
            $type->setName($data['name']);
            // Ajoutez d'autres propriétés du modèle Type ici

            $em->persist($type);
            $em->flush();

            return $this->json($type, context: [
                'groups' => ['type:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/type/{id}', name: 'app_type_update', methods: ['PUT', 'PATCH'])]
    #[OA\Tag(name: 'Types')]
    public function update(
        Type $type,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $type->setName($data['name']);
            // Mettez à jour d'autres propriétés du modèle Type ici

            $em->persist($type);
            $em->flush();

            return $this->json($type, context: [
                'groups' => ['type:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/type/{id}', name: 'app_type_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Types')]
    public function delete(Type $type, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($type);
        $em->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Type supprimé'
        ]);
    }
}
