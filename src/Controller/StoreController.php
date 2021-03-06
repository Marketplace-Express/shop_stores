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
use App\Controller\Validator\Store\IsStoreOwnerConstraint;
use App\Controller\Validator\Store\UpdateConstraint;
use App\Entity\Sort\SortStore;
use App\Exception\DisabledEntityException;
use App\Exception\NotFound;
use App\Exception\ValidationFailed;
use App\Services\StoreService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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
    /** @var StoreService */
    private $service;

    /**
     * StoreController constructor.
     * @param StoreService $service
     */
    public function __construct(StoreService $service)
    {
        $this->service = $service;
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
            $response = $this->prepareResponse($this->service->create(
                $data['ownerId'],
                $data['name'],
                $data['description'],
                $data['type'],
                $data['photo'],
                $data['coverPhoto'],
                $data['location']
            ));
        } catch (ValidationFailed $exception) {
            $response = $this->prepareResponse($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (UniqueConstraintViolationException $exception) {
            $response = $this->prepareResponse('Duplicate entry', Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $exception) {
            $response = $this->prepareResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
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
            $this->service->disable($storeId, $data['disableReason'], $data['disableComment']);
            $response = new Response(null, 204);
        } catch (ValidationFailed $exception) {
            $response = $this->prepareResponse($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (NotFound $exception) {
            $response = $this->prepareResponse($exception->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (DisabledEntityException $exception) {
            $response = $this->prepareResponse($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $exception) {
            $response = $this->prepareResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
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
            $response = $this->prepareResponse($this->service->getById($storeId));
        } catch (ValidationFailed $exception) {
            $response = $this->prepareResponse($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (NotFound $exception) {
            $response = $this->prepareResponse($exception->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (DisabledEntityException $exception) {
            $response = $this->prepareResponse($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $exception) {
            $response = $this->prepareResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * @param string $storeId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/{storeId}", methods={"PUT"}, name="update_one")
     */
    public function update(string $storeId, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->validateRequest(array_merge(['storeId' => $storeId], $data), new UpdateConstraint());
            $response = $this->prepareResponse($this->service->update(
                $storeId,
                $data['name'],
                $data['description'],
                $data['photo'],
                $data['coverPhoto'],
                $data['location']
            ));
        } catch (ValidationFailed $exception) {
            $response = $this->prepareResponse($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (NotFound $exception) {
            $response = $this->prepareResponse($exception->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (DisabledEntityException $exception) {
            $response = $this->prepareResponse($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $exception) {
            $response = $this->prepareResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
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
            $response = $this->prepareResponse($this->service->getAll($page, $limit, new SortStore($sort)));
        } catch (ValidationFailed $exception) {
            $response = $this->prepareResponse($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            $response = $this->prepareResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
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
            $this->service->delete($storeId);
            $response = new Response(null, 204);
        } catch (ValidationFailed $exception) {
            $response = $this->prepareResponse($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (NotFound $exception) {
            $response = $this->prepareResponse($exception->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Throwable $exception) {
            $response = $this->prepareResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * @Route("/{storeId}/isOwner", methods={"GET"}, name="is_store_owner")
     *
     * @param string $storeId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function isStoreOwner(string $storeId, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->validateRequest(array_merge(['storeId' => $storeId], $data), new IsStoreOwnerConstraint());
            $isStoreOwner = $this->service->isStoreOwner($data['user_id'], $storeId);
            $response = $this->prepareResponse($isStoreOwner);
        } catch (ValidationFailed $exception) {
            $response = $this->prepareResponse($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            $response = $this->prepareResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }
}
