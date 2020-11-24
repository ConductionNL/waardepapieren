<?php

// src/Controller/ZuidDrechtController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\ApplicationService;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The Education controller handles any calls for education.
 *
 * Class EducationController
 *
 * @Route("/education")
 */
class EduController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(Session $session, Request $request, ApplicationService $applicationService, CommonGroundService $commonGroundService, ParameterBagInterface $params)
    {
        $content = false;
        $variables = $applicationService->getVariables();

        // Lets provide this data to the template
        $variables['query'] = $request->query->all();
        $variables['post'] = $request->request->all();

        // Get resource
        $variables['programs'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'programs'], $variables['query'])['hydra:member'];
        $variables['courses'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'courses'], $variables['query'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/programs")
     * @Template
     */
    public function programsAction(Session $session, Request $request, ApplicationService $applicationService, CommonGroundService $commonGroundService, ParameterBagInterface $params)
    {
        $content = false;
        $variables = $applicationService->getVariables();

        // Lets provide this data to the template
        $variables['query'] = $request->query->all();
        $variables['post'] = $request->request->all();

        // Get resource
        $variables['resources'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'programs'], $variables['query'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/programs/{id}")
     * @Template
     */
    public function programAction(Session $session, Request $request, ApplicationService $applicationService, CommonGroundService $commonGroundService, ParameterBagInterface $params, $id)
    {
        $content = false;
        $variables = $applicationService->getVariables();

        // Lets provide this data to the template
        $variables['id'] = $id;
        $variables['query'] = $request->query->all();
        $variables['post'] = $request->request->all();

        // Get resource
        $variables['program'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'programs', 'id' => $id], $variables['query']);
        $variables['resources'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'programs'], $variables['query'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {
            $resource = $request->request->all();

            //check if this user is already a participant
            if (array_key_exists('user', $variables)) {
                $userContact = $variables['user']['@id'];
            } elseif (array_key_exists('username', $variables['user'])) { //W.I.P.
                $users = $commonGroundService->getResourceList(['component' => 'edu', 'type' => 'participants'], ['person' => $variables['user']['username']])['hydra:member'];
                if (count($users) > 0) {
                    $userContact = $users[0]['person'];
                }
            }
            $participants = $commonGroundService->getResourceList(['component' => 'edu', 'type' => 'participants'], ['person' => $userContact])['hydra:member'];

            $oldParticipant = [];
            $participant = [];
            $participant['programs'] = [];
            if (count($participants) > 0) { //if this user is already a participant
                //get the programs from this participant
                $oldParticipant = $participants[0];
                foreach ($oldParticipant['programs'] as $program) {
                    array_push($participant['programs'], $program['@id']);
                }
            }

            //add this program to the participant
            array_push($participant['programs'], $variables['program']['@id']);

            //update the existing participant
            if (array_key_exists('@id', $oldParticipant)) {
                $commonGroundService->updateResource($participant, $oldParticipant['@id']);
            } else { //or if this user isn't a participant yet, create one
                $participant['person'] = $variables['user']['@id'];
                $commonGroundService->createResource($participant, ['component' => 'edu', 'type' => 'participants']);
            }

            return $this->redirectToRoute('app_education_program', ['id' => $variables['program']['id']]);
        }

        return $variables;
    }

    /**
     * @Route("/courses")
     * @Template
     */
    public function coursesAction(Session $session, Request $request, ApplicationService $applicationService, CommonGroundService $commonGroundService, ParameterBagInterface $params)
    {
        $content = false;
        $variables = $applicationService->getVariables();

        // Lets provide this data to the template
        $variables['query'] = $request->query->all();
        $variables['post'] = $request->request->all();

        // Get resource
        $variables['resources'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'courses'], $variables['query'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/courses/{id}")
     * @Template
     */
    public function courseAction(Session $session, Request $request, ApplicationService $applicationService, CommonGroundService $commonGroundService, ParameterBagInterface $params, $id)
    {
        $content = false;
        $variables = $applicationService->getVariables();

        // Lets provide this data to the template
        $variables['id'] = $id;
        $variables['query'] = $request->query->all();
        $variables['post'] = $request->request->all();

        // Get resource
        $variables['course'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'courses', 'id' => $id], $variables['query']);
        $variables['resources'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'courses'], $variables['query'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {
            $resource = $request->request->all();

            //check if this user is already a participant
            $participants = $commonGroundService->getResourceList(['component' => 'edu', 'type' => 'participants'], ['person' => $variables['user']['@id']])['hydra:member'];

            $oldParticipant = [];
            $participant = [];
            $participant['courses'] = [];
            if (count($participants) > 0) { //if this user is already a participant
                //get the courses from this participant
                $oldParticipant = $participants[0];
                foreach ($oldParticipant['courses'] as $course) {
                    array_push($participant['courses'], $course['@id']);
                }
            }

            //add this course to the participant
            array_push($participant['courses'], $variables['course']['@id']);

            //update the existing participant
            if (array_key_exists('@id', $oldParticipant)) {
                $commonGroundService->updateResource($participant, $oldParticipant['@id']);
            } else { //or if this user isn't a participant yet, create one
                $participant['person'] = $variables['user']['@id'];
                $commonGroundService->createResource($participant, ['component' => 'edu', 'type' => 'participants']);
            }

            return $this->redirectToRoute('app_education_course', ['id' => $variables['course']['id']]);
        }

        return $variables;
    }

    /**
     * @Route("/activities")
     * @Template
     */
    public function activitiesAction(Session $session, Request $request, ApplicationService $applicationService, CommonGroundService $commonGroundService, ParameterBagInterface $params)
    {
        $content = false;
        $variables = $applicationService->getVariables();

        // Lets provide this data to the template
        $variables['query'] = $request->query->all();
        $variables['post'] = $request->request->all();

        // Get resource
        $variables['resources'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'activities'], $variables['query'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/activities/{id}")
     * @Template
     */
    public function activityAction(Session $session, Request $request, ApplicationService $applicationService, CommonGroundService $commonGroundService, ParameterBagInterface $params, $id)
    {
        $content = false;
        $variables = $applicationService->getVariables();

        // Lets provide this data to the template
        $variables['id'] = $id;
        $variables['query'] = $request->query->all();
        $variables['post'] = $request->request->all();

        // Get resource
        $variables['activity'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'activities', 'id' => $id], $variables['query']);
        $variables['resources'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'activities'], $variables['query'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {
            $resource = $request->request->all();

            //check if this user is already a participant
            $participants = $commonGroundService->getResourceList(['component' => 'edu', 'type' => 'participants'], ['person' => $variables['user']['@id']])['hydra:member'];

            $participant = [];
            if (count($participants) > 0) { //if this user is already a participant
                $participant = $participants[0];

                //add name, activity and participant to the new result resource
                $resource['name'] = $variables['activity']['name'];
                $resource['activity'] = $variables['activity']['@id'];
                $resource['participant'] = $participant['@id'];

                //create the result for this participant
                $commonGroundService->createResource($resource, ['component' => 'edu', 'type' => 'results']);
            }

            return $this->redirectToRoute('app_education_activity', ['id' => $variables['activity']['id']]);
        }

        return $variables;
    }

    /**
     * @Route("/students")
     * @Template
     */
    public function studentsAction(Session $session, Request $request, ApplicationService $applicationService, CommonGroundService $commonGroundService, ParameterBagInterface $params)
    {
        $content = false;
        $variables = $applicationService->getVariables();

        // Lets provide this data to the template
        $variables['query'] = $request->query->all();
        $variables['post'] = $request->request->all();

        // Get resource
        $variables['resources'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'participants'], $variables['query'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/students/{id}")
     * @Template
     */
    public function studentAction(Session $session, Request $request, ApplicationService $applicationService, CommonGroundService $commonGroundService, ParameterBagInterface $params, $id)
    {
        $content = false;
        $variables = $applicationService->getVariables();

        // Lets provide this data to the template
        $variables['query'] = $request->query->all();
        $variables['post'] = $request->request->all();

        // Get Resource
        $variables['resource'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'participants', 'id' => $id]);

        return $variables;
    }

    /**
     * @Route("/organizations")
     * @Template
     */
    public function organizationsAction(Session $session, Request $request, ApplicationService $applicationService, CommonGroundService $commonGroundService, ParameterBagInterface $params)
    {
        $content = false;
        $variables = $applicationService->getVariables();

        // Lets provide this data to the template
        $variables['query'] = $request->query->all();
        $variables['post'] = $request->request->all();

        // Get resource
        $variables['resources'] = $commonGroundService->getResource(['component' => 'wrc', 'type' => 'organizations'], $variables['query'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/organizations/{id}")
     * @Template
     */
    public function organizationAction(Session $session, Request $request, ApplicationService $applicationService, CommonGroundService $commonGroundService, ParameterBagInterface $params, $id)
    {
        $content = false;
        $variables = $applicationService->getVariables();

        // Lets provide this data to the template
        $variables['query'] = $request->query->all();
        $variables['post'] = $request->request->all();

        // Get Resource
        $variables['resource'] = $commonGroundService->getResource(['component' => 'wrc', 'type' => 'organizations', 'id' => $id]);

        return $variables;
    }

    /**
     * @Route("/overview")
     * @Template
     */
    public function overviewAction(Session $session, Request $request, ApplicationService $applicationService, CommonGroundService $commonGroundService, ParameterBagInterface $params)
    {
        $content = false;
        $variables = $applicationService->getVariables();

        // Lets provide this data to the template
        $variables['query'] = $request->query->all();
        $variables['post'] = $request->request->all();

        return $variables;
    }

    /**
     * @Route("/tests")
     * @Template
     */
    public function testsAction(Session $session, Request $request, ApplicationService $applicationService, CommonGroundService $commonGroundService, ParameterBagInterface $params)
    {
        $content = false;
        $variables = $applicationService->getVariables();

        // Lets provide this data to the template
        $variables['query'] = $request->query->all();
        $variables['post'] = $request->request->all();

        // Get resource
        $variables['resources'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'tests'], $variables['query'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/tests/{id}")
     * @Template
     */
    public function testAction(Session $session, Request $request, ApplicationService $applicationService, CommonGroundService $commonGroundService, ParameterBagInterface $params, $id)
    {
        $content = false;
        $variables = $applicationService->getVariables();

        // Lets provide this data to the template
        $variables['id'] = $id;
        $variables['query'] = $request->query->all();
        $variables['post'] = $request->request->all();

        // Get resource
        $variables['test'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'tests', 'id' => $id], $variables['query']);
        $variables['resources'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'tests'], $variables['query'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {
            $resource = $request->request->all();

            //check if this user is already a participant
            $participants = $commonGroundService->getResourceList(['component' => 'edu', 'type' => 'participants'], ['person' => $variables['user']['@id']])['hydra:member'];

            $participant = [];
            if (count($participants) > 0) { //if this user is already a participant
                $participant = $participants[0];

                $testResult = [];
                //add name, activity and participant to the new testResult resource
                $testResult['name'] = $variables['test']['name'];
                $testResult['description'] = 'Dit is een test testResult waarvan de ingevoerde antwoorden nog niet zijn opgeslagen.';
                $testResult['test'] = $variables['test']['@id'];
                $testResult['participant'] = $participant['@id'];

                //create the testResult for this participant
                $commonGroundService->createResource($testResult, ['component' => 'edu', 'type' => 'test_results']);
            }

            return $this->redirectToRoute('app_education_test', ['id' => $variables['test']['id']]);
        }

        return $variables;
    }

    /**
     * @Route("/teams")
     * @Template
     */
    public function teamsAction(Session $session, Request $request, ApplicationService $applicationService, CommonGroundService $commonGroundService, ParameterBagInterface $params)
    {
        $content = false;
        $variables = $applicationService->getVariables();

        // Lets provide this data to the template
        $variables['query'] = $request->query->all();
        $variables['post'] = $request->request->all();

        // Get resource
        $variables['resources'] = $commonGroundService->getResource(['component' => 'cc', 'type' => 'organizations'], $variables['query'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/teams/{id}")
     * @Template
     */
    public function teamAction(Session $session, Request $request, ApplicationService $applicationService, CommonGroundService $commonGroundService, ParameterBagInterface $params, $id)
    {
        $content = false;
        $variables = $applicationService->getVariables();

        // Lets provide this data to the template
        $variables['query'] = $request->query->all();
        $variables['post'] = $request->request->all();

        // Get Resource
        $variables['team'] = $commonGroundService->getResource(['component' => 'cc', 'type' => 'organizations', 'id' => $id]);

        return $variables;
    }
}
