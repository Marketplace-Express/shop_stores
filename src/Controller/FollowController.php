<?php
/**
 * User: Wajdi Jurry
 * Date: ٢‏/٥‏/٢٠٢٠
 * Time: ٢:٣٥ ص
 */

namespace App\Controller;

use App\Controller\Validator\Follow\FollowConstraint;
use App\Controller\Validator\Follow\GetFollowedStoresConstraint;
use App\Controller\Validator\Follow\GetFollowersConstraint;
use App\Controller\Validator\Follow\UnFollowConstraint;
use App\Exception\DisabledEntityException;
use App\Exception\NotFound;
use App\Exception\ValidationFailed;
use App\Services\FollowService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FollowController
 * @package App\Controller
 *
 * @Route("/api/follow", name="api_follow_")
 */
class FollowController extends BaseController
{
    /** @var FollowService */
    private $service;

    /**
     * FollowerController constructor.
     * @param FollowService $service
     */
    public function __construct(FollowService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     *
     * @Route("/", methods={"POST"}, name="follow")
     */
    public function follow(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->validateRequest($data, new FollowConstraint());
            $this->service->follow($data['storeId'], $data['followerId']);
            $response = new Response(null, Response::HTTP_NO_CONTENT);
        } catch (ValidationFailed $exception) {
            $response = $this->prepareResponse($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (NotFound $exception) {
            $response = $this->prepareResponse($exception->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (DisabledEntityException $exception) {
            $response = $this->prepareResponse($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UniqueConstraintViolationException $exception) {
            $response = $this->prepareResponse('already followed', Response::HTTP_BAD_REQUEST);
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
     * @Route("/{storeId}/followers/", name="followers")
     */
    public function getFollowers(string $storeId, Request $request)
    {
        $data = [
            'storeId' => $storeId,
            'limit' => $request->get('limit', 10),
            'page' => $request->get('page', 1)
        ];

        try {
            $this->validateRequest($data, new GetFollowersConstraint());
            $followers = $this->service->getFollowers($data['storeId'], $data['limit'], $data['page']);
            $response = $this->prepareResponse($followers);
        } catch (ValidationFailed $exception) {
            $response = $this->prepareResponse($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            $response = $this->prepareResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/{followerId}/followed/", methods={"GET"}, name="get_followed")
     */
    public function getFollowedStores(Request $request)
    {
        $data = [
            'followerId' => $request->get('followerId'),
            'limit' => $request->get('limit', 10),
            'page' => $request->get('page', 0)
        ];

        try {
            $this->validateRequest($data, new GetFollowedStoresConstraint());
            $followedStores = $this->service->getFollowedStores($data['followerId'], $data['limit'], $data['page']);
            $response = $this->prepareResponse($followedStores);
        } catch (ValidationFailed $exception) {
            $response = $this->prepareResponse($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            $response = $this->prepareResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     *
     * @Route("/unfollow", methods={"DELETE"}, name="unfollow")
     */
    public function unFollow(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->validateRequest($data, new UnFollowConstraint());
            $this->service->unFollow($data['storeId'], $data['followerId']);
            $response = new Response(null, Response::HTTP_NO_CONTENT);
        } catch (ValidationFailed $exception) {
            $response = $this->prepareResponse($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            $response = $this->prepareResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }
}