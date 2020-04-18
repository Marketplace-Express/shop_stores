<?php

namespace App\Controller;


use App\Controller\Validator\Vendor\CreateConstraints;
use App\Controller\Validator\Vendor\DeleteConstraints;
use App\Exception\ValidationFailed;
use App\Repository\VendorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class VendorController
 * @package App\Controller
 * @Route("/api/vendor", name="api_vendor_")
 */
class VendorController extends BaseController
{
    /** @var VendorRepository */
    private $repository;

    /**
     * VendorController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository('App:Vendor');
    }

    /**
     * @Route("/", name="Create", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function create(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->validateRequest($data, new CreateConstraints());
            $vendor = $this->repository->create($data['ownerId']);
            $response = $this->getSuccessResponseScheme($vendor->toApiArray());
        } catch (ValidationFailed $exception) {
            $response = $this->getErrorResponseScheme($exception->errors, 400);
        } catch (\Throwable $exception) {
            $response = $this->getErrorResponseScheme($exception->getMessage(), 500);
        }

        return $this->json($response);
    }

    /**
     * @Route("/{vendorId}", methods={"DELETE"})
     * @param string $vendorId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete(string $vendorId)
    {
        try {
            $this->validateRequest(['vendorId' => $vendorId], new DeleteConstraints());
            $response = $this->getSuccessResponseScheme($this->repository->delete($vendorId));
        } catch (ValidationFailed $exception) {
            $response = $this->getErrorResponseScheme($exception->errors, 400);
        } catch (\Throwable $exception) {
            $response = $this->getErrorResponseScheme($exception->getMessage(), 500);
        }

        return $this->json($response);
    }
}
