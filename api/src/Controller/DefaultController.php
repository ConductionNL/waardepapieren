<?php

// src/Controller/DefaultController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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
        $this->xmlEncoder = new XmlEncoder(['xml_root_node_name' => 'samlp:AuthnRequest']);
    }

    /**
     * @Route("/saml/test")
     * @Template
     */
    public function SamlTestAction(CommonGroundService $commonGroundService, Request $request, ParameterBagInterface $params)
    {
//        $authnRequest = new \LightSaml\Model\Protocol\AuthnRequest();
//        $authnRequest
//            ->setAssertionConsumerServiceURL('https://acc-waardepapieren.hoorn.nl/saml/SAML2/POST')
//            ->setProtocolBinding(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_POST)
//            ->setID(\LightSaml\Helper::generateID())
//            ->setIssueInstant(new \DateTime())
//            ->setDestination('https://preprod1.digid.nl/saml/idp/request_authentication')
//            ->setIssuer(new \LightSaml\Model\Assertion\Issuer('https://acc-waardepapieren.hoorn.nl/saml'))
//        ;
//
//        $certificate = \LightSaml\Credential\X509Certificate::fromFile(__DIR__.'/../cert/hoorn.cer');
//        $privateKey = \LightSaml\Credential\KeyHelper::createPrivateKey(__DIR__.'/../cert/hornKey.key', '', true);
//
//        $authnRequest->setSignature(new \LightSaml\Model\XmlDSig\SignatureWriter($certificate, $privateKey));
//
//        $serializationContext = new \LightSaml\Model\Context\SerializationContext();
//        $authnRequest->serialize($serializationContext->getDocument(), $serializationContext);
//
//                $response = new Response($serializationContext->getDocument()->saveXML());
//        // Create the disposition of the file
//        $disposition = $response->headers->makeDisposition(
//            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
//            'test.xml'
//        );
//
//        // Set the content disposition
//        $response->headers->set('Content-Disposition', $disposition);
//
//        return $response;


        $message = [
            '@xmlns:samlp' => "urn:oasis:names:tc:SAML:2.0:protocol",
            '@xmlns:saml' => "urn:oasis:names:tc:SAML:2.0:assertion",
            '@IssueInstant' => gmdate("Y-m-d H:i:s"),
            '@Destination' => "https://preprod1.digid.nl/saml/idp/request_authentication",
            '@ForceAuthn' => "false",
            '@ID' => Uuid::uuid4()->toString(),
            '@AssertionConsumerServiceURL' => "https://acc-waardepapieren.hoorn.nl/saml/SAML2/POST",
            '@ProviderName' => "Waardepapieren",
            'Issuer' => "https://acc-waardepapieren.hoorn.nl/saml",
            'ds:Signature' => [
                '@xmlns:ds' => "http://www.w3.org/2000/09/xmldsig#",
                'ds:SignedInfo' => [
                    'ds:CanonicalizationMethod' => [
                        '@Algorithm' => "http://www.w3.org/2001/10/xml-exc-c14n#"
                    ],
                    'ds:SignatureMethod' => [
                        '@Algorithm' => "http://www.w3.org/2000/09/xmldsig#rsa-sha1"
                    ],
                    'ds:Reference' => [
                        '@URI' => "#_991ff0bd453d5e8ec783ad87dec1871492bf952fac",
                        'ds:Transforms' => [
                            'ds:Transform' => [
                                '@Algorithm' => "http://www.w3.org/2000/09/xmldsig#enveloped-signature"
                            ],
                            'ds:Transform' => [
                                '@Algorithm' => "http://www.w3.org/2001/10/xml-exc-c14n#"
                            ]
                        ],
                        'ds:DigestMethod' => [
                            '@Algorithm' => "http://www.w3.org/2000/09/xmldsig#sha1"
                        ],
                        'ds:DigestValue' => "Zhwmfr2AqUh+LCq9TQWITXx+/aQ="
                    ]
                ],
                'ds:SignatureValue' => "LbnCFFOv2IXvST8cY1Hq5UndmOnRzMv+yPlAor2TE2+r+FElt1p1RxAxspLE0oXm7aP4Y34/HHVHsv+sYactJk79qDtfspJ7lnLfwwWxSshrTYNZeqJIr1YmXDc2sw3pOQxfaYak1MCe5F4ThQzBdxrsAxoTu0q1DOeFrJiN+bYogRrW44QwaifpoXmWkagN35LSiNBdNkOeA7l/mWDoTJ9Bqm9a5nO8x+mEnN7SI1qtL7jw9Xb9gjLfOfyZobrjIzolmksrKECM6i2v6SLqkPP8Aro88C2VSIr657Ik+PHxbNaGS5BSQYsh+0jRk8RhfBDtR4BPX24Tjsiwiyrxmg==",
                'ds:KeyInfo' => [
                    'ds:X509Data' => [
                        'ds:X509Certificate' => "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"
                    ]
                ]
            ],
            'samlp:RequestedAuthnContext' => [
                '@Comparison' => "minimum",
                'saml:AuthnContextClassRef' => "urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport"
            ]
        ];

        $xml = $this->xmlEncoder->encode($message, 'xml', ['remove_empty_tags' => true]);

        $response = new Response($xml);
        // Create the disposition of the file
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'test.xml'
        );

        // Set the content disposition
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(CommonGroundService $commonGroundService, Request $request, ParameterBagInterface $params)
    {
        // On an index route we might want to filter based on user input
        $variables['query'] = array_merge($request->query->all(), $variables['post'] = $request->request->all());

        $variables['types'][] = [
            'name'=> 'Akte van geboorte',
            'type'=> 'akte_van_geboorte',
        ];
//        $variables['types'][] = [
//            'name'=> 'Akte van overlijden',
//            'type'=> 'akte_van_overlijden',
//        ];
        $variables['types'][] = [
            'name'=> 'Verklaring van in leven zijn',
            'type'=> 'verklaring_van_in_leven_zijn',
        ];
//        $variables['types'][] = [
//            'name'=> 'Verklaring van Nederlanderschap',
//            'type'=> 'verklaring_van_nederlanderschap',
//        ];
//        $variables['types'][] = [
//            'name'=> 'Uittreksel basis registratie personen',
//            'type'=> 'uittreksel_basis_registratie_personen',
//        ];
        $variables['types'][] = [
            'name'=> 'Historisch uittreksel basis registratie personen',
            'type'=> 'historisch_uittreksel_basis_registratie_personen',
        ];

        if ($this->getUser() && $this->getUser()->getPerson()) {
            $variables['certificates'] = $commonGroundService->getResourceList(['component' => 'wari', 'type' => 'certificates'], ['person' => $this->getUser()->getPerson()])['hydra:member'];
//            $variables['certificates'][] = array('type' => 'geboorte akte', 'created' => '17-09-2020', 'id' => '1');
        }

        if ($request->isMethod('POST')) {
            $variables['certificate'] = $request->request->all();
            $variables['certificate']['organization'] = '001516814';
            $variables['certificate'] = $commonGroundService->createResource($variables['certificate'], ['component' => 'waar', 'type' => 'certificates']);
            $variables['certificate']['claim'] = base64_encode(json_encode($variables['certificate']['claim']));
        }

//        $variables['claim'] = base64_encode(json_encode(array("Peter"=>35, "Ben"=>37, "Joe"=>43)));

        return $variables;
    }
}
