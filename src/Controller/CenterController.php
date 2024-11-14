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
    #[Route(name: 'app_center_index', methods: ['GET'])]
    public function index(CenterRepository $centerRepository): Response
    {
        return $this->render('center/index.html.twig', [
            'centers' => $centerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_center_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $center = new Center();
        $form = $this->createForm(CenterType::class, $center);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($center);
            $entityManager->flush();

            return $this->redirectToRoute('app_center_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('center/new.html.twig', [
            'center' => $center,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_center_show', methods: ['GET'])]
    public function show(Center $center): Response
    {
        return $this->render('center/show.html.twig', [
            'center' => $center,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_center_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Center $center, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CenterType::class, $center);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_center_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('center/edit.html.twig', [
            'center' => $center,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_center_delete', methods: ['POST'])]
    public function delete(Request $request, Center $center, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$center->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($center);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_center_index', [], Response::HTTP_SEE_OTHER);
    }
}
