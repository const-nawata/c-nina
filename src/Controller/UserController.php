<?php

namespace App\Controller;

//use Symfony\Bridge\Twig\AppVariable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

//use Psr\Log\LoggerInterface;

use App\Entity\User;
use App\Form\UserForm;
use App\Security\LoginFormAuthAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController
 * @Route("/user")
 * @package App\Controller
 */
class UserController extends ControllerCore
{

    /**
     * @Route("/login", name="user_login")
	 * @param AuthenticationUtils $authenticationUtils
	 * @param TranslatorInterface $translator
	 * @param Request $request
	 * @return Response
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils, TranslatorInterface $translator): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->show($request, 'pages/user/login-form.twig', ['last_username' => $lastUsername, 'error' => $error, 'is_lgged' => 'Undef' ]);
    }
//______________________________________________________________________________
//
//	private function getFormError( $form ){
//		$fields = $form->all();
//		$error_field = '';
//
//		foreach ( $fields as $field ) {
//			$errs	= $field->getErrors(true)->__toString();;
//
//			if(!empty($errs)){
//				$error_field	= $field->getName();
//				break;
//			}
//		}
//
//		return [ $errs, $error_field ];
//	}
//______________________________________________________________________________

	/**
	 * @Route("/register", name="user_register")
	 *
	 * @param Request $request
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 * @param GuardAuthenticatorHandler $guardHandler
	 * @param LoginFormAuthAuthenticator $authenticator
	 * @return Response
	 */
	public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthAuthenticator $authenticator): Response
	{
		$user = new User();
		$form = $this->createForm(UserForm::class, $user, ['attr' => ['mode'=>'register']]);
		$form->handleRequest($request);

		$errs = '';

		if ($form->isSubmitted() && $form->isValid()) {
			$user->setPassword(
				$passwordEncoder->encodePassword(
					$user,
					$form->get('plainPassword')->getData()
				)
			);

			$user->setConfirmed(false);

//			$user->setRoles(['ADMIN']);
			$user->setRoles(['USER']);

			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($user);
			$entityManager->flush();

			return $guardHandler->authenticateUserAndHandleSuccess(
				$user,
				$request,
				$authenticator,
				'main'
			);
		}else{
			$error			= $this->getFormError( $form );
			$errs			= $error['message'];
		}

		return $this->show($request, 'pages/user/user-form.twig', [
			'userForm' => $form->createView(),
			'title' => 'title.registering',
			'errMessage'	=> $errs,
			'scsMessage'	=> ''
		]);
	}
//______________________________________________________________________________

	/**
	 * @Route("/edit", name="user_edit")
	 *
	 * @param Request $request
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 * @param TranslatorInterface $translator
	 * @return Response
	 *
	 */
	public function edit( Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator ): Response
	{
		$user = $this->getUser();
		$form = $this->createForm(UserForm::class, $user, ['attr' => ['mode'=>'edit']]);
		$form->handleRequest($request);

		$error_field = $errs = $scs_message = '';

		if ($form->isSubmitted() ) {
			$pass	 = $form->get('plainPassword')->getData();

			if( !$form->isValid())
				list($errs, $error_field)	= $this->getFormError( $form );

			$errs	= (($error_field != 'plainPassword') || (!empty($pass) && strlen($pass) < 6)) ? $errs : '' ;

			if(empty($errs)){

				!empty($pass)
					? $user->setPassword($passwordEncoder->encodePassword( $user, $pass))
					: $user->setPassword($user->getPassword());

				$entityManager = $this->getDoctrine()->getManager();
				$entityManager->persist($user);
				$entityManager->flush();
				$scs_message	= $translator->trans('message.savedsuccess',[],'prompts');
			}
		}

		return $this->show($request, 'pages/user/user-form.twig', [
			'userForm'		=> $form->createView(),
			'title'			=> 'title.edit',
			'errMessage'	=> $errs,
			'scsMessage'	=> $scs_message
		]);
	}
//______________________________________________________________________________

	/**
     * @Route("/logout", name="user_logout", methods={"GET"})
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
//______________________________________________________________________________
}
