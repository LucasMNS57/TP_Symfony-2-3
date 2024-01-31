<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class BrandController extends AbstractController
{
    #[Route('/brands', name: 'app_brands', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne toutes les marques.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['brand:read']))
        )
    )]
    #[OA\Tag(name: 'Marques')]
    #[Security(name: 'Bearer')]
    public function index(BrandRepository $brandRepository): JsonResponse
    {
        $brands = $brandRepository->findAll();

        return $this->json([
            'brands' => $brands,
        ], context: [
            'groups' => ['brand:read']
        ]);
    }

    #[Route('/brand/{id}', name: 'app_brand_get', methods: ['GET'])]
    #[OA\Tag(name: 'Marques')]
    public function get(Brand $brand): JsonResponse
    {
        return $this->json($brand, context: [
            'groups' => ['brand:read'],
        ]);
    }

    #[Route('/brands', name: 'app_brand_add', methods: ['POST'])]
    #[OA\Tag(name: 'Marques')]
    public function add(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $brand = new Brand();
            $brand->setName($data['name']);
            // Ajoutez d'autres propriétés du modèle Brand ici

            $em->persist($brand);
            $em->flush();

            return $this->json($brand, context: [
                'groups' => ['brand:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/brand/{id}', name: 'app_brand_update', methods: ['PUT', 'PATCH'])]
    #[OA\Tag(name: 'Marques')]
    public function update(
        Brand $brand,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $brand->setName($data['name']);
            // Mettez à jour d'autres propriétés du modèle Brand ici

            $em->persist($brand);
            $em->flush();

            return $this->json($brand, context: [
                'groups' => ['brand:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/brand/{id}', name: 'app_brand_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Marques')]
    public function delete(Brand $brand, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($brand);
        $em->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Marque supprimée'
        ]);
    }
}
