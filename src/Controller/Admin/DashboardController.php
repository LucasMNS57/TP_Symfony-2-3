<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    #[Security("is_granted('ROLE_ADMIN')")]
    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }


    #[Route('/admin/color/add', name: 'app_admin_color_add')]
    public function addColor(Request $request): Response
{
    $color = new Color();
    $form = $this->createForm(ColorType::class, $color);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($color);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_color_list');
    }

    return $this->render('admin/color/add.html.twig', [
        'form' => $form->createView(),
    ]);
}
}