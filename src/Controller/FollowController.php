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
use App\Entity\Interfaces\ApiArrayData;
use App\Exception\DisabledEntityException;
use App\Exception\NotFound;
use App\Exception\ValidationFailed;
use App\Repository\FollowerRepository;
use App\Repository\StoreRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
    /** @var FollowerRepository */
    private $followerRepository;

    /** @var StoreRepository */
    private $storeRepository;

    /**
     * FollowerController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->storeRepository = $entityManager->getRepository('App:Store');
        $this->followerRepository = $entityManager->getRepository('App:Follower');
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
            $store = $this->storeRepository->getById($data['storeId']);
            $this->followerRepository->follow($store, $data['followerId']);
            return new Response(null, Response::HTTP_NO_CONTENT);
        } catch (ValidationFailed $exception) {
            $response = $this->getResponseScheme($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (NotFound $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (DisabledEntityException $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (UniqueConstraintViolationException $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($response, $response['status']);
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

            $followers = $this->followerRepository->getFollowers($storeId, $data['limit'], $data['page']);
            $followers['followers'] = array_map(function ($follower) {
                return $follower->getFollowerId();
            }, $followers['followers']);
            $response = $this->getResponseScheme($followers);
        } catch (ValidationFailed $exception) {
            $response = $this->getResponseScheme($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($response, $response['status']);
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

            $followedStores = $this->followerRepository->followedStores($data['followerId'], $data['limit'], $data['page']);

            $followedStores['stores'] = array_map(function ($store) {
                return $store->getStore()->toApiArray();
            }, $followedStores['stores']);

            $response = $this->getResponseScheme($followedStores);
        } catch (ValidationFailed $exception) {
            $response = $this->getResponseScheme($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($response, $response['status']);
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
            $this->followerRepository->unFollow($data['storeId'], $data['followerId']);

            return new Response(null, Response::HTTP_NO_CONTENT);
        } catch (ValidationFailed $exception) {
            $response = $this->getResponseScheme($exception->errors, Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($response, $response['status']);
    }
}