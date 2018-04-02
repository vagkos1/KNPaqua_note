<?php

namespace AppBundle\Security;


use AppBundle\Entity\User;
use AppBundle\Form\LoginForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    private $formFactory;
    private $em;
    private $router;

    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        RouterInterface $router
    ) {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * is the request a login form submit? If not, the authenticator doesn't have to do anything
     * Since LoginForm::class is not tied to a class (no binding), then $data is just an associative array
     *
     * @param Request $request
     * @return array|null
     */
    public function getCredentials(Request $request)
    {
        $isLoginSubmit = $request->getPathInfo() == '/login' && $request->isMethod('POST');
        if (!$isLoginSubmit) {
            return;
        }

        $form = $this->formFactory->create(LoginForm::class);
        $form->handleRequest($request);
        $data = $form->getData();

        // behind the scenes, the authenticator communicates with the SecurityController by storing things in the session
        // that's what the security.authentication_utils service helps us with
        $request->getSession()->set(
          Security::LAST_USERNAME,
          $data['_username']
        );

        return $data;
    }

    // Called if $this->getCredentials returns something other than null
    public function getUser($credentials, UserProviderInterface $userProvider) : User
    {
        $username = $credentials['_username'];

        return $this->em->getRepository('AppBundle:User')
            ->findOneBy(['email' => $username]);
    }

    // called if $this->getUser returns a User
    public function checkCredentials($credentials, UserInterface $user) : bool
    {
        $password = $credentials['_password'];

        if ($password == 'iliketurtles') {
            return true;
        }

        return false;
    }

    // in case the credentials failed, user gets redirected via this method
    protected function getLoginUrl()
    {
        return $this->router->generate('security_login');
    }

    // in case the user succeeds, they get redirected via this method
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // if the user hits a secure page and start() was called, this was
        // the URL they were on, and probably where you want to redirect to
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);

        if (!$targetPath) {
            $targetPath = $this->router->generate('homepage');
        }

        return new RedirectResponse($targetPath);
    }
}
