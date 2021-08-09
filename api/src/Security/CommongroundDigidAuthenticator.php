<?php

// src/Security/TokenAuthenticator.php

/*
 * This authenticator authenticates against DigiSpoof
 *
 */

namespace App\Security;

use Conduction\CommonGroundBundle\Security\User\CommongroundUser;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class CommongroundDigidAuthenticator extends AbstractGuardAuthenticator
{
    private $em;
    private $params;
    private $commonGroundService;
    private $csrfTokenManager;
    private $router;
    private $urlGenerator;
    private $session;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params, CommonGroundService $commonGroundService, CsrfTokenManagerInterface $csrfTokenManager, RouterInterface $router, UrlGeneratorInterface $urlGenerator, SessionInterface $session)
    {
        $this->em = $em;
        $this->params = $params;
        $this->commonGroundService = $commonGroundService;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->router = $router;
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request)
    {
        return 'app_default_samltest' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request)
    {
        $credentials = [
            'bsn'   => $request->request->get('bsn'),

        ];

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // Aan de hand van BSN persoon ophalen uit haal centraal
        try {
            $user = $this->commonGroundService->getResource(['component' => 'brp', 'type' => 'ingeschrevenpersonen', 'id' => $credentials['bsn']]);
        } catch (\Throwable $e) {
            return;
        }

        if (!isset($user['roles'])) {
            $user['roles'] = [];
        }

        if (!in_array('ROLE_USER', $user['roles'])) {
            $user['roles'][] = 'ROLE_USER';
        }

        array_push($user['roles'], 'scope.vrc.requests.read');
        array_push($user['roles'], 'scope.orc.orders.read');
        array_push($user['roles'], 'scope.cmc.messages.read');
        array_push($user['roles'], 'scope.bc.invoices.read');
        array_push($user['roles'], 'scope.arc.events.read');
        array_push($user['roles'], 'scope.irc.assents.read');

        return new CommongroundUser($user['naam']['voornamen'], $user['naam']['voornamen'], $user['naam']['voornamen'], null, $user['roles'], $this->commonGroundService->cleanUrl(['component'=>'brp', 'type'=>'ingeschrevenpersonen', 'id' => $user['burgerservicenummer']]), null, 'person', false);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        try {
            $user = $this->commonGroundService->getResource(['component' => 'brp', 'type' => 'ingeschrevenpersonen', 'id' => $credentials['bsn']]);
        } catch (\Throwable $e) {
            return;
        }

        // no adtional credential check is needed in this case so return true to cause authentication success
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $backUrl = $request->request->get('back_url');
        $bsn = $request->request->get('bsn');
        $user = $this->commonGroundService->getResource(['component' => 'brp', 'type' => 'ingeschrevenpersonen', 'id' => $bsn]);

        $this->session->set('user', $user);

        return new RedirectResponse($backUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($this->params->get('app_subpath') && $this->params->get('app_subpath') != 'false') {
            return new RedirectResponse('/'.$this->params->get('app_subpath').$this->router->generate('app_user_digispoof', []));
        }

        return new RedirectResponse($this->router->generate('app_user_digispoof', ['response' => $request->request->get('back_url'), 'back_url' => $request->request->get('back_url')]));
    }

    /**
     * Called when authentication is needed, but it's not sent.
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        if ($this->params->get('app_subpath') && $this->params->get('app_subpath') != 'false') {
            return new RedirectResponse('/'.$this->params->get('app_subpath').$this->router->generate('app_user_digispoof', []));
        } else {
            return new RedirectResponse($this->router->generate('app_user_digispoof', ['response' => $request->request->get('back_url'), 'back_url' => $request->request->get('back_url')]));
        }
    }

    public function supportsRememberMe()
    {
        return true;
    }

    protected function getLoginUrl()
    {
        if ($this->params->get('app_subpath') && $this->params->get('app_subpath') != 'false') {
            return '/'.$this->params->get('app_subpath').$this->router->generate('app_user_digispoof', [], UrlGeneratorInterface::RELATIVE_PATH);
        } else {
            return $this->router->generate('app_user_digispoof', [], UrlGeneratorInterface::RELATIVE_PATH);
        }
    }
}
