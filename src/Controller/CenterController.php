<?php

namespace App\Controller;

use App\Entity\Center;
use App\Form\CenterType;
use App\Repository\CenterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/center')]
final class CenterController extends AbstractController
{
    #[Route(name: 'App_center_index', methods: ['GET'])]
    public function index(CenterRepository $centerRepository): Response
    {
        return $this->render('center/center.html.twig', [
            'centers' => $centerRepository->findAll(),
            'governorate' => "",
            'city' => "",
            'name' => "",
        ]);
    }
    #[Route('/filter', name: 'App_center_filter', methods: ['GET'])]
    public function filter(CenterRepository $centerRepository, Request $request): Response
    {
        // Get filter parameters from query string
        $governorate = $request->query->get('governorate');
        $city = $request->query->get('city');
        $name = $request->query->get('center-name');

        // Find filtered centers using the repository method
        $centers = $centerRepository->findByFilters($governorate, $city, $name);

        // Pass the filtered data to the template
        return $this->render('center/center.html.twig', [
            'centers' => $centers,
            'governorate' => $governorate,
            'city' => $city,
            'name' => $name,
        ]);
    }
    #[Route('/new', name: 'App_center_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $center = new Center();
        $form = $this->createForm(CenterType::class, $center);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($center);
            $entityManager->flush();

            return $this->redirectToRoute('App_center_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('center/new.html.twig', [
            'center' => $center,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'App_center_show', methods: ['GET'])]
    public function show(Center $center): Response
    {
        return $this->render('center/show.html.twig', [
            'center' => $center,
        ]);
    }

    #[Route('/{id}/edit', name: 'App_center_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Center $center, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CenterType::class, $center);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('App_center_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('center/edit.html.twig', [
            'center' => $center,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'App_center_delete', methods: ['POST'])]
    public function delete(Request $request, Center $center, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$center->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($center);
            $entityManager->flush();
        }

        return $this->redirectToRoute('App_center_index', [], Response::HTTP_SEE_OTHER);
    }
}
