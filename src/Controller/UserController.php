<?php


namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Martin PAUCOT <contact@martin-paucot.fr>
 */
class UserController extends Controller
{

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Returns the current user.
     *
     * @Route(
     *     "user",
     *     methods={"GET"}
     * )
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @return Response
     */
    public function getUserAction(): Response
    {
        return new Response(
            $this->serializer->serialize(
                $this->getUser(),
                'json', array('groups' => array('default'))
            )
        );
    }

    /**
     * Returns the current user.
     *
     * @Route(
     *     "user/get/{id}",
     *     methods={"GET"}
     * )
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @return Response
     */
    public function getUserByIdAction(Request $request, $id): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        return new Response(
            $this->serializer->serialize(
                $user,
                'json', array('groups' => array('default'))
            )
        );
    }



    /**
     * Deletes the given user by id
     *
     * @Route(
     *     "user/delete/{id}",
     *     methods={"DELETE"}
     * )
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param int $id
     * @throws \Exception
     * @return Response
     */
    public function deleteUserAction(Request $request, $id) {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        if (null === $user) {
            throw new \Exception('No such user');
        }

        $entityManager->remove($user);
        $entityManager->flush();
        $response = new Response();
        $response->send();

        return $response;
    }

}