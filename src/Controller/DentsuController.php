<?php
namespace App\Controller;

use App\Entity\Package;
use App\Message\PackageData;
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
     * @param MessageBusInterface $bus
     * @param Request             $request
     *
     * @return Response
     */
    public function index(MessageBusInterface $bus, Request $request): Response
    {
        $parameters = $request->toArray();

        $entityManager = $this->getDoctrine()->getManager();
        $package       = new Package();
        $package->setName($parameters['name']);
        $package->setNumber($parameters['number']);
        $package->setStatus(false);
        $entityManager->persist($package);
        $entityManager->flush();
        $parameters['id'] = $package->getId();
        $bus->dispatch(new PackageData($parameters));

        $status = 'processing';

        return new JsonResponse(['status' => $status]);
    }

    /**
     * @Route(path="/status/{id}", name="get_status", methods={"GET"})
     */
    public function getStatus($id): Response
    {
        $package = $this->getDoctrine()
            ->getRepository(Package::class)
            ->find($id);
        $status  = $package->getStatus() ? 'completed' : 'processing';

        return new JsonResponse(['status' => $status]);
    }
}
