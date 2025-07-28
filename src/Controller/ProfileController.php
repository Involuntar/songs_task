<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ProfileController extends AbstractController
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('profile/index.html.twig', [
            'firstname' => $user->getFirstname(),
            "lastname" => $user->getLastname()
        ]);
    }

    #[Route("/callback_vk", name: "callback_vk")]
    public function callback_vk(UserRepository $userRepository, Request $request, EntityManagerInterface $em): Response
    {
        $code = $request->query->get('code');
        $deviceId = $request->query->get('device_id');

        $user = $this->getUser();
        $user = $userRepository->findOneBy(['id' => $user->getId()]);
        $user->setDeviceId($deviceId);
        $em->flush();

        $parameters = [
            'grant_type'    => 'authorization_code',
            'code_verifier' => "ve3BxJ2jXyCS8w8yu0hutqNGw_Ik1_zQQ8KYg2L-HIA",
            'redirect_uri'  => 'http://localhost/callback_vk', // URL, который вы зарегистрировали в VK
            'code'          => $code, // Полученный после первого шага OAuth (авторизация пользователя)
            'client_id'     => '53957545', // ID вашего приложения VK
            'device_id'     => $deviceId, // Опционально, но рекомендуется
            'state'         => 'XXXRandomZZZ', // Должен совпадать со значением, переданным на первом шаге
        ];
        $requestBody = http_build_query($parameters);

        $response = $this->httpClient->request(
            'POST',
            'https://id.vk.com/oauth2/auth', // Конечная точка для обмена кодом на токен
            [
                // 2. Передаем закодированную строку в опции 'body'
                'body' => $requestBody,
                // 3. Явно устанавливаем заголовок Content-Type
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]
        );

        $data = $response->toArray();
        $user->setAccessToken($data['access_token']);
        $em->flush();

        return $this->redirectToRoute('app_profile');
    }
}
