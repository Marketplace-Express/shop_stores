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
     * @Route("/", name="Create", methods={"POST"})
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
            $response = $this->getSuccessResponseScheme($store->toApiArray());
        } catch (ValidationFailed $exception) {
            $response = $this->getErrorResponseScheme($exception->errors, 400);
        } catch (\Throwable $exception) {
            $response = $this->getErrorResponseScheme($exception->getMessage(), 500);
        }

        return $this->json($response, $response['status']);
    }

    /**
     * @param string $storeId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|void
     *
     * @Route("/{storeId}/disable", methods={"PUT"})
     */
    public function disable(string $storeId, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->validateRequest(array_merge($data, ['storeId' => $storeId]), new DisableConstraint());
            $this->repository->disable($storeId, $data['disableReason'], $data['disableComment']);
            return new Response(null, 204);
        } catch (ValidationFailed $exception) {
            $response = $this->getErrorResponseScheme($exception->errors, 400);
        } catch (NotFound $exception) {
            $response = $this->getErrorResponseScheme($exception->getMessage(), 404);
        } catch (DisabledEntity $exception) {
            $response = $this->getErrorResponseScheme($exception->getMessage(), 422);
        } catch (\Throwable $exception) {
            $response = $this->getErrorResponseScheme($exception->getMessage(), 500);
        }

        return $this->json($response, $response['status']);
    }

    /**
     * @param string $storeId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/{storeId}", methods={"GET"})
     */
    public function getById(string $storeId)
    {
        try {
            $this->validateRequest(['storeId' => $storeId], new GetByIdConstraint());
            $response = $this->getSuccessResponseScheme($this->repository->getById($storeId)->toApiArray());
        } catch (ValidationFailed $exception) {
            $response = $this->getErrorResponseScheme($exception->getMessage(), 400);
        } catch (NotFound $exception) {
            $response = $this->getErrorResponseScheme($exception->getMessage(), 404);
        } catch (DisabledEntity $exception) {
            $response = $this->getErrorResponseScheme($exception->getMessage(), 422);
        } catch (\Throwable $exception) {
            $response = $this->getErrorResponseScheme($exception->getMessage(), 500);
        }

        return $this->json($response, $response['status']);
    }

    /**
     * @param string $storeId
     * @param Request $request
     *
     * @Route("/{storeId}", methods={"PUT"})
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
            $response = $this->getSuccessResponseScheme($store->toApiArray());
        } catch (ValidationFailed $exception) {
            $response = $this->getErrorResponseScheme($exception->errors, 400);
        } catch (NotFound $exception) {
            $response = $this->getErrorResponseScheme($exception->getMessage(), 404);
        } catch (DisabledEntity $exception) {
            $response = $this->getErrorResponseScheme($exception->getMessage(), 422);
        } catch (\Throwable $exception) {
            $response = $this->getErrorResponseScheme($exception->getMessage(), 500);
        }

        return $this->json($response, $response['status']);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/", methods={"GET"})
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
            $stores = $result['stores'];
            $more = $result['more'];
            $stores = array_map(function ($store) {
                return $store->toApiArray();
            }, $stores);
            $response = $this->getSuccessResponseScheme(['stores' => $stores, 'more' => $more]);
        } catch (ValidationFailed $exception) {
            $response = $this->getErrorResponseScheme($exception->errors, 400);
        } catch (\Throwable $exception) {
            $response = $this->getErrorResponseScheme($exception->getMessage(), 500);
        }

        return $this->json($response, $response['status']);
    }

    /**
     * @Route("/{storeId}", methods={"DELETE"})
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
            $response = $this->getErrorResponseScheme($exception->errors, 400);
        } catch (NotFound $exception) {
            $response = $this->getErrorResponseScheme($exception->getMessage(), 404);
        } catch (\Throwable $exception) {
            $response = $this->getErrorResponseScheme($exception->getMessage(), 500);
        }

        return $this->json($response);
    }
}
