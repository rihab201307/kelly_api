<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Groups;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation as Doc;

class UserController extends FosRestController
{


    /**
     * @Rest\Post("/users")
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("user", converter="fos_rest.request_body")
     */
    public function createUserAction(User $user)
    {

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * @Rest\Get("/users/{id}", name="users")
     * @Rest\View()
     *
     * @Doc\ApiDoc(
     *     resource=true,
     *     description="Get a user."
     * )
     */

    public function userAction($id,Request $request)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($request->get('id'));

        if (empty($user)) {
            return new JsonResponse(['message' => "user n $id n'existe pas"], Response::HTTP_NOT_FOUND);
        }


        return $user;
    }

    /**
     * @Rest\Get("/users", name="list_users")
     * @Rest\View()
     * @Doc\ApiDoc(
     *     resource=true,
     *     description="Get the list of all users."
     * )
     */

    public function listuserAction(Request $request)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $user;
    }

    /**
     * @Rest\Put("/users/{id}", name="update_users")
     * @Rest\View()
     *
     * @Doc\ApiDoc(
     *     resource=true,
     *     description="Update a users."
     * )
     */

    public function updateUserpAction( Request $request)
    {
        return $this->updateuser($request, true);
    }

    /**
     * @Rest\Patch("/users/{id}", name="patch_users")
     * @Rest\View()
     *
     * @Doc\ApiDoc(
     *     resource=true,
     *     description="Patch a users."
     * )
     */
    public function patchUserAction( Request $request)
    {
        return $this->updateuser($request, false);
    }

    public function updateuser(Request $request, $clearMissing)
    {

        $user = $this->getDoctrine()->getRepository(User::class)->find($request->get('id'));

        /* @var $user User*/

        if (empty($user))
        {
            return new JsonResponse(['message'=>"user not found"],Response::HTTP_NOT_FOUND);
        }


        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->request->all(), $clearMissing);

        $em = $this->getDoctrine()->getManager();
        $em->merge($user);
        $em->flush();

        return $user;
    }

    /**
     * @Rest\Delete("/users/{id}", name="delete_user")
     * @Rest\View()
     *
     * @Doc\ApiDoc(
     *     resource= true,
     *     description="delete a user"
     * )
     *
     */

    public function deleteUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository(User::class)->find($request->get('id'));

        /* @var user User*/

        if($user)
        {
            $em->remove($user);
            $em->flush();
        }
    }



}
