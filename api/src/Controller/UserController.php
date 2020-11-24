<?php

// src/Controller/DefaultController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\ApplicationService;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class UserController.
 */
class UserController extends AbstractController
{
    /**
     * @var FlashBagInterface
     */
    private $flash;
    private $translator;

    public function __construct(FlashBagInterface $flash)
    {
        $this->flash = $flash;
    }

    /**
     * @Route("/login")
     * @Route("/login/{loggedOut}", name="loggedOut")
     * @Template
     */
    public function login(
        Session $session,
        Request $request,
        AuthorizationCheckerInterface $authChecker,
        CommonGroundService $commonGroundService,
        ParameterBagInterface $params,
        EventDispatcherInterface $dispatcher,
        $loggedOut = false
    ) {
        $application = $commonGroundService->getResource(['component' => 'wrc', 'type' => 'applications', 'id' => $params->get('app_id')]);

        if ($loggedOut == 'loggedOut') {
            $text = 'U bent uitgelogd omdat de sessie is verlopen.';
            $this->flash->add('error', $text);

            $session->set('loggedOut', null);
        }

        // Dealing with backUrls
        if ($backUrl = $request->query->get('backUrl')) {
        } else {
            $backUrl = '/login';
        }
        $session->set('backUrl', $backUrl);

        if ($this->getUser()) {
            if (isset($application['defaultConfiguration']['configuration']['userPage'])) {
                return $this->redirect($application['defaultConfiguration']['configuration']['userPage']);
            } else {
                return $this->redirect($this->generateUrl('app_default_index'));
            }
        } else {
            return $this->render('login/index.html.twig', ['backUrl' => $backUrl]);
        }
    }

    /**
     * @Route("/auth/digispoof")
     * @Template
     */
    public function DigispoofAction(Request $request, CommonGroundService $commonGroundService, ParameterBagInterface $params, EventDispatcherInterface $dispatcher)
    {
        $redirect = $commonGroundService->cleanUrl(['component' => 'ds']);

        return $this->redirect($redirect.'?responceUrl='.$request->query->get('response').'&backUrl='.$request->query->get('back_url'));
    }

    /**
     * @Route("/auth/eherkenning")
     * @Template
     */
    public function EherkenningAction(Request $request, CommonGroundService $commonGroundService, ParameterBagInterface $params, EventDispatcherInterface $dispatcher)
    {
        $redirect = $commonGroundService->cleanUrl(['component' => 'eh']);

        return $this->redirect($redirect.'?responceUrl='.$request->query->get('response').'&backUrl='.$request->query->get('back_url'));
    }

    /**
     * @Route("/auth/idin/login")
     * @Template
     */
    public function IdinLoginAction(Session $session, Request $request, CommonGroundService $commonGroundService, ParameterBagInterface $params, EventDispatcherInterface $dispatcher)
    {
        $session->set('backUrl', $request->query->get('backUrl'));

        $redirect = str_replace('http:', 'https:', $request->getUri());
        if (strpos($redirect, '?') == true) {
            $redirect = substr($redirect, 0, strpos($redirect, '?'));
        }

        $provider = $commonGroundService->getResourceList(['component' => 'uc', 'type' => 'providers'], ['type' => 'idin', 'application' => $params->get('app_id')])['hydra:member'];
        $provider = $provider[0];

        if (isset($provider['configuration']['app_id']) && isset($provider['configuration']['secret']) && isset($provider['configuration']['endpoint'])) {
            $clientId = $provider['configuration']['app_id'];

            if ($params->get('app_env') == 'prod') {
                return $this->redirect('https://eu01.signicat.com/oidc/authorize?response_type=code&scope=openid+signicat.idin&client_id='.$clientId.'&redirect_uri='.$redirect.'&acr_values=urn:signicat:oidc:method:idin-login&state=123');
            } else {
                return $this->redirect('https://eu01.preprod.signicat.com/oidc/authorize?response_type=code&scope=openid+signicat.idin&client_id='.$clientId.'&redirect_uri='.$redirect.'&acr_values=urn:signicat:oidc:method:idin-login&state=123');
            }
        } else {
            return $this->render('500.html.twig');
        }
    }

    /**
     * @Route("/auth/idin/ident")
     * @Template
     */
    public function IdinAction(Session $session, Request $request, CommonGroundService $commonGroundService, ParameterBagInterface $params, EventDispatcherInterface $dispatcher)
    {
        $session->set('backUrl', $request->query->get('backUrl'));

        $redirect = str_replace('http:', 'https:', $request->getUri());
        if (strpos($redirect, '?') == true) {
            $redirect = substr($redirect, 0, strpos($redirect, '?'));
        }

        $provider = $commonGroundService->getResourceList(['component' => 'uc', 'type' => 'providers'], ['type' => 'idin', 'application' => $params->get('app_id')])['hydra:member'];
        $provider = $provider[0];

        if (isset($provider['configuration']['app_id']) && isset($provider['configuration']['secret']) && isset($provider['configuration']['endpoint'])) {
            $clientId = $provider['configuration']['app_id'];

            if ($params->get('app_env') == 'prod') {
                return $this->redirect('https://eu01.signicat.com/oidc/authorize?response_type=code&scope=openid+signicat.idin&client_id='.$clientId.'&redirect_uri='.$redirect.'&acr_values=urn:signicat:oidc:method:idin-ident&state=123');
            } else {
                return $this->redirect('https://eu01.preprod.signicat.com/oidc/authorize?response_type=code&scope=openid+signicat.idin&client_id='.$clientId.'&redirect_uri='.$redirect.'&acr_values=urn:signicat:oidc:method:idin-ident&state=123');
            }
        } else {
            return $this->render('500.html.twig');
        }
    }

    /**
     * @Route("/auth/irma")
     * @Template
     */
    public function IrmaAction(Session $session, Request $request, CommonGroundService $commonGroundService, ParameterBagInterface $params, EventDispatcherInterface $dispatcher)
    {
    }

    /**
     * @Route("/auth/facebook")
     * @Template
     */
    public function FacebookAction(Session $session, Request $request, CommonGroundService $commonGroundService, ParameterBagInterface $params, EventDispatcherInterface $dispatcher)
    {
        $session->set('backUrl', $request->query->get('backUrl'));

        $provider = $commonGroundService->getResourceList(['component' => 'uc', 'type' => 'providers'], ['type' => 'facebook', 'application' => $params->get('app_id')])['hydra:member'];
        $provider = $provider[0];

        $redirect = $request->getUri();
        if (strpos($redirect, '?') == true) {
            $redirect = substr($redirect, 0, strpos($redirect, '?'));
        }

        if (isset($provider['configuration']['app_id']) && isset($provider['configuration']['secret'])) {
            return $this->redirect('https://www.facebook.com/v8.0/dialog/oauth?client_id='.str_replace('"', '', $provider['configuration']['app_id']).'&scope=email&redirect_uri='.$redirect.'&state={st=state123abc,ds=123456789}');
        } else {
            return $this->render('500.html.twig');
        }
    }

    /**
     * @Route("/auth/github")
     * @Template
     */
    public function githubAction(Session $session, Request $request, CommonGroundService $commonGroundService, ParameterBagInterface $params, EventDispatcherInterface $dispatcher)
    {
        $session->set('backUrl', $request->query->get('backUrl'));

        $providers = $commonGroundService->getResourceList(['component' => 'uc', 'type' => 'providers'], ['type' => 'github', 'application' => $params->get('app_id')])['hydra:member'];
        $provider = $providers[0];

        return $this->redirect('https://github.com/login/oauth/authorize?state='.$this->params->get('app_id').'&redirect_uri=https://checkin.dev.zuid-drecht.nl/github&client_id=0106127e5103f0e5af24');
    }

    /**
     * @Route("/auth/gmail")
     * @Template
     */
    public function gmailAction(Session $session, Request $request, CommonGroundService $commonGroundService, ParameterBagInterface $params, EventDispatcherInterface $dispatcher)
    {
        $session->set('backUrl', $request->query->get('backUrl'));

        $providers = $commonGroundService->getResourceList(['component' => 'uc', 'type' => 'providers'], ['type' => 'gmail', 'application' => $params->get('app_id')])['hydra:member'];
        $provider = $providers[0];

        $redirect = $request->getUri();

        if (strpos($redirect, '?') == true) {
            $redirect = substr($redirect, 0, strpos($redirect, '?'));
        }

        if (isset($provider['configuration']['app_id']) && isset($provider['configuration']['secret'])) {
            return $this->redirect('https://accounts.google.com/o/oauth2/v2/auth?response_type=code&client_id=46119456250-gad8g8342inudo8gp8v63ovokq21itt2.apps.googleusercontent.com&scope=openid%20email%20profile%20https://www.googleapis.com/auth/user.phonenumbers.read&redirect_uri='.$redirect);
        } else {
            return $this->render('500.html.twig');
        }
    }

    /**
     * @Route("/logout")
     * @Template
     */
    public function logoutAction(Session $session, Request $request)
    {
        $session->set('requestType', null);
        $session->set('request', null);
        $session->set('contact', null);
        $session->set('organisation', null);

        $text = $this->translator->trans('U bent uitgelogd');

        // Throw te actual flash
        $this->flash->add('error', $text);

        return $this->redirect($this->generateUrl('app_default_index'));
    }

    /**
     * @Route("/register")
     * @Template
     */
    public function registerAction(Session $session, Request $request, ApplicationService $applicationService, CommonGroundService $commonGroundService, ParameterBagInterface $params)
    {
        $content = false;
        $variables = $applicationService->getVariables();

        // Lets provide this data to the template
        $variables['query'] = $request->query->all();
        $variables['post'] = $request->request->all();

        // Get resource
        $application = $commonGroundService->getResource(['component' => 'wrc', 'type' => 'applications', 'id' => $params->get('app_id')]);
        $variables['userGroups'] = $commonGroundService->getResourceList(['component' => 'uc', 'type' => 'groups'], ['organization' => $application['organization']['@id'], 'canBeRegisteredFor' => true])['hydra:member'];
        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {
            $resource = $request->request->all();

            $email = [];
            $contact = [];
            $user = [];

            //create the email in CC
            $email['name'] = 'userEmail';
            $email['email'] = $resource['email'];
            $email = $commonGroundService->createResource($email, ['component' => 'cc', 'type' => 'emails']);

            //create the contact in CC
            if (array_key_exists('achternaam', $resource)) {
                if (array_key_exists('tussenvoegsel', $resource)) {
                    $contact['additionalName'] = $resource['tussenvoegsel'];
                }
                $contact['familyName'] = $resource['achternaam'];
            }
            foreach ($resource['userGroups'] as $userGroupUrl) { //check the selected group(s)
                $userGroup = $commonGroundService->getResource($userGroupUrl); //get the group resource
                if ($userGroup['name'] == 'Studenten') { //check if the group studenten is selected
                    $contact['name'] = 'studentUserContact';
                    if (array_key_exists('voornaam', $resource) && !empty($resource['voornaam'])) {
                        $contact['givenName'] = $resource['voornaam'];
                    } else {
                        $contact['givenName'] = 'studentUserContact';
                    }
                    $contact['emails'] = [];
                    $contact['emails'][0] = $email['@id'];
                    $contact = $commonGroundService->createResource($contact, ['component' => 'cc', 'type' => 'people']); //create a person in CC

                    //create the participant in EDU
                    $participant = [];
                    $participant['person'] = $contact['@id'];
                    $commonGroundService->createResource($participant, ['component' => 'edu', 'type' => 'participants']);

                    //create the employee in MRC
                    $employee = [];
                    $employee['person'] = $contact['@id'];
                    $employee['organization'] = $commonGroundService->cleanUrl(['component' => 'cc', 'type' => 'organizations']);
                    $commonGroundService->createResource($employee, ['component' => 'mrc', 'type' => 'employees']);
                } elseif ($userGroup['name'] == 'Bedrijven') { //check if the group bedrijven is selected
                    $contactPerson = [];
                    $contactPerson['name'] = 'bedrijfUserContact';
                    if (array_key_exists('voornaam', $resource) && !empty($resource['voornaam'])) {
                        $contactPerson['givenName'] = $resource['voornaam'];
                    } else {
                        $contactPerson['givenName'] = 'bedrijfUserContact';
                    }
                    $contactPerson['emails'] = [];
                    $contactPerson['emails'][0] = $email['@id'];
                    $contactPerson = $commonGroundService->createResource($contactPerson, ['component' => 'cc', 'type' => 'people']); //create a person in CC

                    //create an organization in CC
                    $contact['name'] = 'bedrijfUserContact';
                    $contact['description'] = 'Beschrijving van dit bedrijfUserContact';
                    $contact['type'] = 'Participant';
                    $contact['emails'] = [];
                    $contact['emails'][0] = $email['@id'];
                    $contact['persons'] = [];
                    $contact['persons'][0] = $contactPerson['@id'];
                    $contact = $commonGroundService->createResource($contact, ['component' => 'cc', 'type' => 'organizations']);

                    //create an organization in WRC
//                    $organization = [];
//                    $organization['name'] = 'bedrijfUserContact';
//                    $organization['description'] = 'Beschrijving van dit bedrijfUserContact';
//                    $organization['rsin'] = '999912345';
//                    $organization['contact'] = $contact['@id'];
//                    $commonGroundService->createResource($organization, ['component' => 'wrc', 'type' => 'organizations']);
                }
            }

            //create the user in UC
            $user['organization'] = $application['organization']['@id'];
            $user['username'] = $resource['email'];
            $user['password'] = $resource['wachtwoord'];
            $user['person'] = $contact['@id'];
            $user['userGroups'] = [];
            $user['userGroups'] = $resource['userGroups'];
            $commonGroundService->createResource($user, ['component' => 'uc', 'type' => 'users']);

            return $this->redirectToRoute('app_default_index');
        }

        return $variables;
    }

    /**
     * @Route("/userinfo")
     * @Template
     */
    public function userInfoAction(Session $session, Request $request, ApplicationService $applicationService, CommonGroundService $commonGroundService, ParameterBagInterface $params)
    {
        $variables = [];

        $variables['person'] = $commonGroundService->getResource($this->getUser()->getPerson());

        if ($request->isMethod('POST') && $request->get('info')) {
            $resource = $request->request->all();
            $person = [];
            $person['@id'] = $commonGroundService->cleanUrl(['component' => 'cc', 'type' => 'people', 'id' => $variables['person']['id']]);
            $person['id'] = $variables['person']['id'];

            if (isset($resource['firstName'])) {
                $person['givenName'] = $resource['firstName'];
            }
            if (isset($resource['lastName'])) {
                $person['familyName'] = $resource['lastName'];
            }
            if (isset($resource['birthday']) && $resource['birthday'] !== '') {
                $person['birthday'] = $resource['birthday'];
            }
            if (isset($resource['email'])) {
                $person['emails'][0]['email'] = $resource['email'];
            }
            if (isset($resource['telephone'])) {
                $person['telephones'][0]['telephone'] = $resource['telephone'];
            }
            if (isset($resource['street'])) {
                $person['adresses'][0]['street'] = $resource['street'];
            }
            if (isset($resource['houseNumber'])) {
                $person['adresses'][0]['houseNumber'] = $resource['houseNumber'];
            }
            if (isset($resource['houseNumberSuffix'])) {
                $person['adresses'][0]['houseNumberSuffix'] = $resource['houseNumberSuffix'];
            }
            if (isset($resource['postalCode'])) {
                $person['adresses'][0]['postalCode'] = $resource['postalCode'];
            }
            if (isset($resource['locality'])) {
                $person['adresses'][0]['locality'] = $resource['locality'];
            }

            $variables['person'] = $commonGroundService->saveResource($person, ['component' => 'cc', 'type' => 'people']);
        } elseif ($request->isMethod('POST') && $request->get('password')) {
            $newPassword = $request->get('newPassword');
            $repeatPassword = $request->get('repeatPassword');

            if ($newPassword !== $repeatPassword) {
                $variables['error'] = true;

                return $variables;
            } else {
                $credentials = [
                    'username'   => $this->getUser()->getUsername(),
                    'password'   => $request->request->get('currentPassword'),
                    'csrf_token' => $request->request->get('_csrf_token'),
                ];

                $user = $commonGroundService->createResource($credentials, ['component'=>'uc', 'type'=>'login'], false, true, false, false);

                if (!$user) {
                    $variables['wrongPassword'] = true;

                    return $variables;
                }

                $users = $commonGroundService->getResourceList(['component'=>'uc', 'type'=>'users'], ['username'=> $this->getUser()->getUsername()], true, false, true, false, false)['hydra:member'];
                $user = $users[0];

                $user['password'] = $newPassword;

                $this->addFlash('success', 'wachtwoord aangepast');
                $commonGroundService->updateResource($user);

                $message = [];

                if ($params->get('app_env') == 'prod') {
                    $message['service'] = '/services/eb7ffa01-4803-44ce-91dc-d4e3da7917da';
                } else {
                    $message['service'] = '/services/1541d15b-7de3-4a1a-a437-80079e4a14e0';
                }
                $message['status'] = 'queued';
                $message['data'] = ['receiver' => $variables['person']['name']];
                $message['content'] = $commonGroundService->cleanUrl(['component'=>'wrc', 'type'=>'templates', 'id'=>'4125221c-74e0-46f9-97c9-3825a2011012']);
                $message['reciever'] = $user['username'];
                $message['sender'] = 'no-reply@conduction.nl';

                $commonGroundService->createResource($message, ['component'=>'bs', 'type'=>'messages']);
            }
        }

        return $variables;
    }
}
