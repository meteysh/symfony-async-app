<?php
namespace App\Controller;

use App\Message\PackageData;
use JSend\JSendResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\DBAL\Connection;

class DentsuController extends AbstractController
{
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    /**
     * @Route(path="/start", name="handle_data", methods={"POST"})
     */
    public function index(MessageBusInterface $bus, Request $request): Response
    {
        $parameters = $request->toArray();
        $bus->dispatch(new PackageData($parameters));
        $status = 'processing';

        return new JsonResponse(['status' => $status]);
    }

    /**
     * @Route(path="/status", name="get_status", methods={"GET"})
     */
    public function getStatus(): Response
    {
        $status = 'processing';

        return new JsonResponse(['status' => $status]);
    }
}
