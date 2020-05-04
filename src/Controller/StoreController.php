<?php
/**
 * User: Wajdi Jurry
 * Date: 17/04/2020
 * Time: ٥:٤٣ م
 */

namespace App\Controller;


use App\Controller\Validator\Store\CreateConstraints;
use App\Controller\Validator\Store\DeleteConstraint;
use App\Controller\Validator\Store\DisableConstraint;
use App\Controller\Validator\Store\GetAllConstraint;
use App\Controller\Validator\Store\GetByIdConstraint;
use App\Controller\Validator\Store\UpdateConstraint;
use App\Entity\Sort\SortStore;
use App\Exception\DisabledEntity;
use App\Exception\NotFound;
use App\Exception\ValidationFailed;
use App\Repository\StoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StoreController
 * @package App\Controller
 * @Route("/api/store", name="api_store_")
 */
class StoreController extends BaseController
{
    /** @var StoreRepository */
    private $repository;

    /**
     * StoreController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository('App:Store');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/", name="Create", methods={"POST"}, name="create")
     */
    public function create(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->validateRequest($data, new CreateConstraints());
            $store = $this->repository->create(
                $data['ownerId'],
                $data['name'],
                $data['description'],
                $data['type'],
                $data['photo'],
                $data['coverPhoto'],
                $data['location']
            );
            $response = $this->getResponseScheme($store->toApiArray());
        } catch (ValidationFailed $exception) {
            $response = $this->getResponseScheme($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($response, $response['status']);
    }

    /**
     * @param string $storeId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|void
     *
     * @Route("/{storeId}/disable", methods={"PUT"}, name="disable")
     */
    public function disable(string $storeId, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->validateRequest(array_merge($data, ['storeId' => $storeId]), new DisableConstraint());
            $this->repository->disable($storeId, $data['disableReason'], $data['disableComment']);
            return new Response(null, 204);
        } catch (ValidationFailed $exception) {
            $response = $this->getResponseScheme($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (NotFound $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (DisabledEntity $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($response, $response['status']);
    }

    /**
     * @param string $storeId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/{storeId}", methods={"GET"}, name="get_one")
     */
    public function getById(string $storeId)
    {
        try {
            $this->validateRequest(['storeId' => $storeId], new GetByIdConstraint());
            $response = $this->getResponseScheme($this->repository->getById($storeId)->toApiArray());
        } catch (ValidationFailed $exception) {
            $response = $this->getResponseScheme($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (NotFound $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (DisabledEntity $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($response, $response['status']);
    }

    /**
     * @param string $storeId
     * @param Request $request
     *
     * @Route("/{storeId}", methods={"PUT"}, name="update_one")
     */
    public function update(string $storeId, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->validateRequest($data, new UpdateConstraint());
            $store = $this->repository->update(
                $storeId,
                $data['name'],
                $data['description'],
                $data['location'],
                $data['photo'],
                $data['coverPhoto']
            );
            $response = $this->getResponseScheme($store->toApiArray());
        } catch (ValidationFailed $exception) {
            $response = $this->getResponseScheme($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (NotFound $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (DisabledEntity $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($response, $response['status']);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/", methods={"GET"}, name="get_all")
     */
    public function getAll(Request $request)
    {
        $sort = $request->get('sort');
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);

        $data = [
            'sort' => $sort,
            'page' => $page,
            'limit' => $limit
        ];

        try {
            $this->validateRequest($data, new GetAllConstraint());
            $result = $this->repository->getAll($page, $limit, new SortStore($sort));
            $result['stores'] = array_map(function ($store) {
                return $store->toApiArray();
            }, $result['stores']);
            $response = $this->getResponseScheme($result);
        } catch (ValidationFailed $exception) {
            $response = $this->getResponseScheme($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($response, $response['status']);
    }

    /**
     * @Route("/{storeId}", methods={"DELETE"}, name="delete_one")
     * @param string $storeId
     * @return \Symfony\Component\HttpFoundation\Response|void
     */
    public function delete(string $storeId)
    {
        try {
            $this->validateRequest(['storeId' => $storeId], new DeleteConstraint());
            $this->repository->delete($storeId);
            return new Response(null, 204);
        } catch (ValidationFailed $exception) {
            $response = $this->getResponseScheme($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (NotFound $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Throwable $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($response);
    }
}
