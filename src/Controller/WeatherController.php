<?php

namespace App\Controller;

use App\Form\WeatherFormType;
use App\Service\WeatherService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WeatherController extends AbstractController
{
    /**
     * @Route("/", name="weather")
     */
    public function index(Request $request, WeatherService $service): Response
    {
        $form = $this->createForm(WeatherFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tab = $service->getWeather($form);
            if ($tab == false) {
                $this->addFlash('erreur', 'Lieu introuvable !');
                return $this->redirectToRoute('weather');
            }
            return $this->render('weather/weather.html.twig', [
                'tab' => $service->getWeather($form),
            ]);
        }

        return $this->render('weather/index.html.twig', [
            'controller_name' => 'WeatherController',
            'form' => $form->createView(),
        ]);
    }
}
