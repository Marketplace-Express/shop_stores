<?php
/**
 * User: Wajdi Jurry
 * Date: ٢‏/٥‏/٢٠٢٠
 * Time: ٢:٣٥ ص
 */

namespace App\Controller;

use App\Controller\Validator\Follow\FollowConstraint;
use App\Controller\Validator\Follow\GetFollowersConstraint;
use App\Entity\Interfaces\ApiArrayData;
use App\Exception\DisabledEntity;
use App\Exception\NotFound;
use App\Exception\ValidationFailed;
use App\Repository\FollowerRepository;
use App\Repository\StoreRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("/", methods={"POST"})
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
        } catch (DisabledEntity $exception) {
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
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/{storeId}/followers/")
     */
    public function getFollowers(string $storeId)
    {
        $data = ['storeId' => $storeId];

        try {
            $this->validateRequest($data, new GetFollowersConstraint());
            $followers = array_map(function ($follower) {
                if ($follower instanceof ApiArrayData) {
                    return $follower->toApiArray();
                }
            }, $this->storeRepository->getFollowers($storeId)->getValues());
            $response = $this->getResponseScheme(array_column($followers, 'followerId'));
        } catch (\Throwable $exception) {
            $response = $this->getResponseScheme($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($response, $response['status']);
    }
}