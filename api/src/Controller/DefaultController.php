<?php

// src/Controller/DefaultController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use GuzzleHttp\Client;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

/**
 * The Procces test handles any calls that have not been picked up by another test, and wel try to handle the slug based against the wrc.
 *
 * Class DefaultController
 *
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    private XmlEncoder $xmlEncoder;

    public function __construct()
    {
        $this->xmlEncoder = new XmlEncoder(['xml_root_node_name' => 'md:EntityDescriptor']);
    }

    /**
     * @Route("/saml")
     * @Template
     */
    public function SamlAction()
    {
        $message = [
            '@xmlns:md'     => 'urn:oasis:names:tc:SAML:2.0:metadata',
            '@ID'           => '_bacb1a87766c004a6bfc18375f52425e',
            '@entityID'     => 'https://acc-waardepapieren.hoorn.nl/saml',
            'md:Extensions' => [
                '@xmlns:alg'       => 'urn:oasis:names:tc:SAML:metadata:algsupport',
                'alg:DigestMethod' => [
                    [
                        '@Algorithm' => 'http://www.w3.org/2001/04/xmlenc#sha512',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2009/xmlenc11#aes192-gcm',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#sha224',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2000/09/xmldsig#sha1',
                    ],
                ],
                'alg:SigningMethod' => [
                    [
                        '@Algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#ecdsa-sha512',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#ecdsa-sha384',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#ecdsa-sha256',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#ecdsa-sha224',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha512',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha384',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2009/xmldsig11#dsa-sha256',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#ecdsa-sha1',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2000/09/xmldsig#rsa-sha1',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2000/09/xmldsig#dsa-sha1',
                    ],
                ],
            ],
            'md:SPSSODescriptor' => [
                '@AuthnRequestsSigned'        => '1',
                '@protocolSupportEnumeration' => 'urn:oasis:names:tc:SAML:2.0:protocol',
                'md:Extensions'               => [
                    'init:RequestInitiator' => [
                        '@xmlns:init' => 'urn:oasis:names:tc:SAML:profiles:SSO:request-init',
                        '@Binding'    => 'urn:oasis:names:tc:SAML:profiles:SSO:request-init',
                        '@Location'   => 'https://acc-waardepapieren.hoorn.nl/saml/Login',
                    ],
                ],
            ],
            'md:KeyDescriptor' => [
                '@use'       => 'signing',
                'ds:KeyInfo' => [
                    '@xmlns:ds' => 'http://www.w3.org/2000/09/xmldsig#',
                    'ds:KeyName',
                    'ds:X509Data' => [
                        'ds:X509SubjectName' => ' CN = acc-waardepapieren.hoorn.nl, O = Cooperatie Dimpact U.A., L = Enschede,subject=C = NL',
                        'ds:X509Certificate' => 'MIIIizCCBnOgAwIBAgIUaEqzF2dEWOw7D1HxLCll79mdYc4wDQYJKoZIhvcNAQEL
BQAwSTELMAkGA1UEBhMCTkwxETAPBgNVBAoMCEtQTiBCLlYuMScwJQYDVQQDDB5L
UE4gUEtJb3ZlcmhlaWQgU2VydmVyIENBIDIwMjAwHhcNMjEwNjIyMTEzMDEzWhcN
MjIwNjIyMTEzMDEzWjB7MQswCQYDVQQGEwJOTDEOMAwGA1UEBwwFSG9vcm4xFzAV
BgNVBAoMDkdlbWVlbnRlIEhvb3JuMR0wGwYDVQQFExQwMDAwMDAwMTAwMTUxNjgx
NDAwMDEkMCIGA1UEAwwbYWNjLXdhYXJkZXBhcGllcmVuLmhvb3JuLm5sMIIBIjAN
BgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA3ASVxYpp6Zlj0zKZz6itMVkrWPMI
gETP5TOUp9m/X2ZgfwoGNvbFzWbOtYEHEVQFOR58UfjWPAWuJbBM05IRacYgml7x
Uqp5nKVM2TiCb4rkR8DfoRsgJMAE0tM8WlA0JH5WfSRxdzDvzXEok8zOd0hbcYEo
5fLTR+6Wn61wWmKd2/ZS2cLoqtjkDNyHsTYN7/PxILy/dde9bEY0Sz9LWqrDzCr1
KNu9lM4bzJy48enXuKKJZe+613F5fUlzNiu2msSgc8nP/vRyLdiznn8kqalX1o+M
qC/Zijp71HGr+grc4aCWTd6xWZH7CXYIMA+6+xKZEIZ8vmurPNjwkk4YHwIDAQAB
o4IENzCCBDMwDAYDVR0TAQH/BAIwADAfBgNVHSMEGDAWgBQISqq7mSRvvlsH8aWK
mVstR++5PDCBiQYIKwYBBQUHAQEEfTB7ME0GCCsGAQUFBzAChkFodHRwOi8vY2Vy
dC5tYW5hZ2VkcGtpLmNvbS9DQWNlcnRzL0tQTlBLSW92ZXJoZWlkU2VydmVyQ0Ey
MDIwLmNlcjAqBggrBgEFBQcwAYYeaHR0cDovL29jc3AyMDIwLm1hbmFnZWRwa2ku
Y29tMCYGA1UdEQQfMB2CG2FjYy13YWFyZGVwYXBpZXJlbi5ob29ybi5ubDCBsQYD
VR0gBIGpMIGmMAgGBmeBDAECAjCBmQYKYIQQAYdrAQIFCTCBijA3BggrBgEFBQcC
ARYraHR0cHM6Ly9jZXJ0aWZpY2FhdC5rcG4uY29tL3BraW92ZXJoZWlkL2NwczBP
BggrBgEFBQcCAjBDDEFPcCBkaXQgY2VydGlmaWNhYXQgaXMgaGV0IENQUyBQS0lv
dmVyaGVpZCB2YW4gS1BOIHZhbiB0b2VwYXNzaW5nLjAdBgNVHSUEFjAUBggrBgEF
BQcDAgYIKwYBBQUHAwEwUwYDVR0fBEwwSjBIoEagRIZCaHR0cDovL2NybC5tYW5h
Z2VkcGtpLmNvbS9LUE5QS0lvdmVyaGVpZFNlcnZlckNBMjAyMC9MYXRlc3RDUkwu
Y3JsMB0GA1UdDgQWBBS2PPXoTdf6E7sDMcdeSJ65/ID75zAOBgNVHQ8BAf8EBAMC
BaAwggH1BgorBgEEAdZ5AgQCBIIB5QSCAeEB3wB2ACl5vvCeOTkh8FZzn2Old+W+
V32cYAr4+U1dJlwlXceEAAABejN8Qa4AAAQDAEcwRQIhAMdQFbzPUaObtOFNcUHK
h/w3ekX/6BI8XLVXuI33XTpGAiAjRGJO9nDuUqNIDchSq7Tvf9ck79fGAgFgKWCU
+bzowgB2AEalVet1+pEgMLWiiWn0830RLEF0vv1JuIWr8vxw/m1HAAABejN8QXMA
AAQDAEcwRQIhAKm7jrObWtp5RyPgqzMCJjXu4jaF4sAslTocOaoeVlDOAiA7k0ue
BHcumPQ6HjcsajvGCqS51WSOmzxp4gHHYOu8tAB2AFWB1MIWkDYBSuoLm1c8U/DA
5Dh4cCUIFy+jqh0HE9MMAAABejN8QsEAAAQDAEcwRQIgTLAgwhrkF7lMhsdu1Y5I
Yav3zIQHpvkoG4MKnEWzQxwCIQC/xJvZuDoG3LS1QzjK3O4TQBKmH/g61Rvylx5l
Yv/ruAB1AG9Tdqwx8DEZ2JkApFEV/3cVHBHZAsEAKQaNsgiaN9kTAAABejN8QawA
AAQDAEYwRAIgVEdxVqPAO0f6LIhFQBUFiW8ESSiB2Lfy5bU1yPX/peACIFGHlguH
r197DwYt/ihZPRuoJf3fLnf5mCCzEq2jKcgLMA0GCSqGSIb3DQEBCwUAA4ICAQCZ
z0pv8+DQN9sFUwhE0quhpcoxuOkjbROJ79noEf72DzrpLHF4x6+Pj6gFGOYkHAkL
n5QJRFp/u7LHCbdCUSuohvQX2/d/Qc0nb6wSAARJRt8RVJ8/AgJ8RTv/D32T5XK+
IIRSRv/+bwKidOpNLoNgyOOAKbcdvX1YNJiC2v5AAiiGqzs8urFTbfyzDH0sc5Jm
Pt0bnpcRuFT9rrbfi/jO/wWae952Iha3Be5m8QiyRSFH5gNR9Bnw/G05ZxLcwbmr
PIjfaCnSICOS3SUW79DDOEGwmPuh3Wbru2yLY3tYKNSdmQe3YUha8KmddXrPPGv2
UgLAyX9d9aEhSVlKTR6yu0w9ATYEOBbh5QI9+o44pXRiEOErGqOH3MZXEK5hTlZq
rn7lfGd5KtYN1vLwnrYA3QVfTUUv5qaRGa1LPeJvXsxhmcL4YtC9jsxNi5tr52K4
XmWv9/oaNDddGtaalXANsuani41jJ/bTChHby2zkSQQsWDcB7E2wV6q0A36BH6tj
QLAsxLWJHW1KVQAYI1dry+iikdD9K2TvoeEQRnY7ReZLmZAmdPsLvXhRL7CeefK7
ALWC5KxuRcC/41u30ZLkAvtIOJ/+d0YaWW4YhW/QMbaf7mGjHzIV3w9lRLciCBJC
hbLnCGV7d+nY520FypigadljbcU/siU8VnQPQkgUVw==',
                    ],
                ],
                'md:EncryptionMethod' => [
                    [
                        '@Algorithm' => 'http://www.w3.org/2009/xmlenc11#aes128-gcm',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2009/xmlenc11#aes192-gcm',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2009/xmlenc11#aes256-gcm',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2001/04/xmlenc#aes128-cbc',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2001/04/xmlenc#aes192-cbc',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2001/04/xmlenc#aes256-cbc',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2001/04/xmlenc#tripledes-cbc',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2009/xmlenc11#rsa-oaep',
                    ],
                    [
                        '@Algorithm' => 'http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p',
                    ],
                ],
            ],
            'md:ArtifactResolutionService' => [
                '@Binding'  => 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP',
                '@Location' => 'https://acc-waardepapieren.hoorn.nl/saml/Artifact/SOAP',
                '@index'    => '0',
            ],
            'md:SingleLogoutService' => [
                [
                    '@Binding'  => 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP',
                    '@Location' => 'https://acc-waardepapieren.hoorn.nl/saml/SLO/SOAP',
                ],
                [
                    '@Binding'  => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                    '@Location' => 'https://acc-waardepapieren.hoorn.nl/saml/SLO/Redirect',
                ],
                [
                    '@Binding'  => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
                    '@Location' => 'https://acc-waardepapieren.hoorn.nl/saml/SLO/POST',
                ],
                [
                    '@Binding'  => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact',
                    '@Location' => 'https://acc-waardepapieren.hoorn.nl/saml/SLO/Artifact',
                ],
            ],
            'md:AssertionConsumerService' => [
                [
                    '@Binding'  => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
                    '@Location' => 'https://acc-waardepapieren.hoorn.nl/saml/SAML2/POST',
                    '@index'    => '0',
                ],
                [
                    '@Binding'  => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST-SimpleSign',
                    '@Location' => 'https://acc-waardepapieren.hoorn.nl/saml/SAML2/POST-SimpleSign',
                    '@index'    => '1',
                ],
                [
                    '@Binding'  => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact',
                    '@Location' => 'https://acc-waardepapieren.hoorn.nl/saml/SAML2/Artifact',
                    '@index'    => '2',
                ],
            ],
        ];

        $xml = $this->xmlEncoder->encode($message, 'xml', ['remove_empty_tags' => true]);

        $response = new Response($xml);

        $response->headers->set('Content-Type', 'xml');

        return $response;
    }

    /**
     * @Route("/saml/SLO/Artifact")
     * @Template
     */
    public function SamlTestAction(Request $request, \OneLogin\Saml2\Auth $samlAuth, ParameterBagInterface $params)
    {
        $date = date('Y-m-d\TH:i:s\Z');
        $artifact = $request->query->get('SAMLart');
        $config = $samlAuth->getSettings()->getSPData();

        $xml = '
                <samlp:ArtifactResolve
        xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
        xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
        xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
        xmlns:ec="http://www.w3.org/2001/10/xml-exc-c14n#"
        ID="_1330416073" Version="2.0" IssueInstant="'.$date.'">
        <saml:Issuer>'.$config['entityId'].'</saml:Issuer>
        <samlp:Artifact>'.$artifact.'</samlp:Artifact>
        </samlp:ArtifactResolve>';

        $signed = \OneLogin\Saml2\Utils::addSign($xml, $config['privateKey'], $config['x509cert']);
        $signed = str_replace('<?xml version="1.0"?>', '', $signed);

        $soap = '
        <soapenv:Envelope
        xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
        <soapenv:Body>
        '.$signed.'
        </soapenv:Body>
        </soapenv:Envelope>';


//        echo "<pre>".htmlentities($soap)."</pre>";
//        die;

        $client = new Client([
            'base_uri' => 'https://was-preprod1.digid.nl',
            'timeout'  => 5.0,
            'ssl_key'  => $params->get('app_ssl_key'),
            'cert'     => $params->get('app_certificate'),
        ]);

        $response = $client->request('POST', '/saml/idp/resolve_artifact', [
            'headers' => [
                'Content-Type' => 'text/xml',
                'SOAPAction'   => $config['entityId'],
            ],
            'body' => $soap,
        ]);
        echo "<pre>";
        var_dump($response->getStatusCode());

        var_dump($response->getBody()->getContents());
        exit;

        return $artifact;
    }

    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(CommonGroundService $commonGroundService, Request $request, ParameterBagInterface $params, Session $session)
    {
        // On an index route we might want to filter based on user input
        $variables['query'] = array_merge($request->query->all(), $variables['post'] = $request->request->all());

        $variables['types'][] = [
            'name'  => 'Akte van geboorte',
            'type'  => 'akte_van_geboorte',
            'price' => '14',
        ];
//        $variables['types'][] = [
//            'name'=> 'Akte van overlijden',
//            'type'=> 'akte_van_overlijden',
//        ];
        $variables['types'][] = [
            'name'  => 'Verklaring van in leven zijn',
            'type'  => 'verklaring_van_in_leven_zijn',
            'price' => '14',
        ];
//        $variables['types'][] = [
//            'name'=> 'Verklaring van Nederlanderschap',
//            'type'=> 'verklaring_van_nederlanderschap',
//        ];
        $variables['types'][] = [
            'name'  => 'Uittreksel basis registratie personen',
            'type'  => 'uittreksel_basis_registratie_personen',
            'price' => '14',
        ];
//        $variables['types'][] = [
//            'name'=> 'Historisch uittreksel basis registratie personen',
//            'type'=> 'historisch_uittreksel_basis_registratie_personen',
//        ];

        if ($this->getUser() && $this->getUser()->getPerson()) {
            $variables['certificates'] = $commonGroundService->getResourceList(['component' => 'wari', 'type' => 'certificates'], ['person' => $this->getUser()->getPerson()])['hydra:member'];
//            $variables['certificates'][] = array('type' => 'geboorte akte', 'created' => '17-09-2020', 'id' => '1');
        }

        if ($session->get('type') && (!$params->has('app_shasign') || !$this->getParameter('app_shasign'))) {
            $variables['certificate']['type'] = $session->get('type');
            $variables['certificate']['organization'] = '001516814';
            $variables['certificate']['person'] = $this->getUser()->getPerson();
            $variables['certificate'] = $commonGroundService->createResource($variables['certificate'], ['component' => 'waar', 'type' => 'certificates']);
            $variables['certificate']['claim'] = base64_encode(json_encode($variables['certificate']['claim']));

            $variables['certificates'] = $commonGroundService->getResourceList(['component' => 'wari', 'type' => 'certificates'], ['person' => $this->getUser()->getPerson()])['hydra:member'];
        }
        if($params->has('app_shasign') && $this->getParameter('app_shasign')){
            var_Dump($params->get('app_shasign'));
            $variables['paymentEnabled'] = true;
        } else {
            $variables['paymentEnabled'] = false;
        }

        return $variables;
    }

    /**
     * @Route("/pay-certificate")
     * @Template
     */
    public function payCertificateAction(
        CommonGroundService $commonGroundService,
        Request $request,
        ParameterBagInterface $params,
        UrlHelper $urlHelper,
        Session $session
    ) {
        $variables = [];

        if(!$params->has('app_shasign') || !$this->getParameter('app_shasign')){
            $variables['values'] = $request->request->all();
            $typeinfo = json_decode($variables['values']['typeinfo']);

            $orderId = (string) Uuid::uuid4();
            if (isset($typeinfo)) {
                $session->set('type', $typeinfo->type);
                $session->set('orderId', $orderId);
            }
            return $this->redirectToRoute('app_default_index');
        }

        $shaSignature = $params->get('app_shasign');

        if (isset($shaSignature) && $request->query->get('orderID') && $request->query->get('PAYID') && $request->query->get('SHASIGN') && $this->getUser()) {
            $variables['paramsArray'] = $request->query->all();

            $keyArray = [];
            foreach ($variables['paramsArray'] as $key => $value) {
                // Dont take the hashed shasign
                if ($key !== 'SHASIGN') {
                    $keyArray[strtoupper($key)] = $value;
                }
            }
            ksort($keyArray);

            $signature = [];
            foreach ($keyArray as $key => $value) {
                $signature[] = $key.'='.$value.$shaSignature;
            }
            $hashedSign = strtoupper(hash('sha256', implode('', $signature)));

            if ($hashedSign === $request->query->get('SHASIGN')) {
                $variables['hashResult'] = 'success';
            } else {
                $variables['hashResult'] = 'failed';
            }

            $receivedOrderId = $request->query->get('orderID');
            $orderId = $session->get('orderId');
            if (isset($variables['paramsArray']['STATUS']) && ($variables['paramsArray']['STATUS'] == '5' ||
                    $variables['paramsArray']['STATUS'] == '9' || $variables['paramsArray']['STATUS'] == '51' ||
                    $variables['paramsArray']['STATUS'] == '91') && isset($orderId) && isset($receivedOrderId) && $orderId == $receivedOrderId) {

//                Create certificate if type is in session
                if ($session->get('type')) {
                    $variables['certificate']['type'] = $session->get('type');
                    $variables['certificate']['organization'] = '001516814';
                    $variables['certificate']['person'] = $this->getUser()->getPerson();
                    $variables['certificate'] = $commonGroundService->createResource($variables['certificate'], ['component' => 'waar', 'type' => 'certificates']);
                    $variables['certificate']['claim'] = base64_encode(json_encode($variables['certificate']['claim']));

                    $variables['certificates'] = $commonGroundService->getResourceList(['component' => 'wari', 'type' => 'certificates'], ['person' => $this->getUser()->getPerson()])['hydra:member'];
                }
            } elseif (isset($variables['paramsArray']['STATUS'])) {
                $session->set('type', null);
                $session->set('orderId', null);
            }
        } elseif (isset($shaSignature) && $request->isMethod('POST') && $this->getUser()) {
            $variables['values'] = $request->request->all();
            $typeinfo = json_decode($variables['values']['typeinfo']);

            $orderId = (string) Uuid::uuid4();
            if (isset($typeinfo)) {
                $session->set('type', $typeinfo->type);
                $session->set('orderId', $orderId);
            }

            $variables['paymentArray'] = [];
            $variables['paymentArray'] = [
                'PSPID'          => 'gemhoorn',
                'orderid'        => $orderId,
                'amount'         => $typeinfo->price * 100,
                'currency'       => 'EUR',
                'language'       => 'nl_NL',
                'CN'             => $this->getUser()->getName(),
                'TITLE'          => 'Certificate',
                'BGCOLOR'        => 'white',
                'TXTCOLOR'       => 'black',
                'TBLBGCOLOR'     => 'white',
                'TBLTXTCOLOR'    => 'black',
                'BUTTONBGCOLOR'  => 'white',
                'BUTTONTXTCOLOR' => 'black',
                'FONTTYPE'       => 'Verdana',
                'ACCEPTURL'      => $urlHelper->getAbsoluteUrl('/pay-certificate'),
                'EXCEPTIONURL'   => $urlHelper->getAbsoluteUrl('/pay-certificate'),
                'DECLINEURL'     => $urlHelper->getAbsoluteUrl('/pay-certificate'),
                'CANCELURL'      => $urlHelper->getAbsoluteUrl('/pay-certificate'),
            ];
            $variables['keyArray'] = [];

            foreach ($variables['paymentArray'] as $key => $value) {
                $variables['keyArray'][strtoupper($key)] = $value;
            }

            ksort($variables['keyArray']);
//            var_dump($variables['keyArray']);
            $variables['signature'] = [];

            foreach ($variables['keyArray'] as $key => $value) {
                $variables['signature'][] = $key.'='.$value.$shaSignature;
            }
//            var_dump(implode('?', $variables['signature']));
//            var_dump($variables['signature']);

            $variables['paymentArray']['SHASign'] = hash('sha256', implode('', $variables['signature']));
            /*            var_dump($variables['paymentArray']['SHASign']);
                        die;*/
            $variables['status'] = 'test';
        } else {
            return $this->redirectToRoute('app_default_index');
        }

        return $variables;
    }

    /**
     * @Route("/certificate/{id}")
     * @Template
     */
    public function certificateAction(CommonGroundService $commonGroundService, Request $request, ParameterBagInterface $params, $id)
    {
        $variables['certificate'] = $commonGroundService->getResource(['component' => 'waar', 'type' => 'certificates', 'id' => $id]);

        return $variables;
    }
}
