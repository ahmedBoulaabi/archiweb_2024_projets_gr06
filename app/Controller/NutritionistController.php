<?php

namespace Manger\Controller;

use Manger\Model\NutritionistModel;

/**
 * Controller for Nutritionist-related things.
 * 
 * Handle actions such as adding clients, sending them notifications.
 */
class NutritionistController
{
    /**
     * nutriModel
     *
     * @var NutritionistModel
     */
    private $nutriModel;
    /**
     * Constructor
     *
     * Initializes the Nutritionists Controller with the Nutritionist Model.
     */
    public function __construct()
    {
        $this->nutriModel = new NutritionistModel();
    }


    /**
     * Fetch and display users
     *
     * Fetch and display all users with a fullnamem matching the parameter from the GET request
     * @return void Outputs the user data in JSON format.
     */
    public function getClientList()
    {
        header('APPJSON');
        $searchValue = isset($_GET['searchValue']) ? $_GET['searchValue'] : '';

        if (!empty($searchValue)) {
            $data = $this->nutriModel->getUserByFullname($searchValue, $_SESSION['role']);

            if ($data) {
                ob_start();
                include VIEWSDIR . DS . 'components' . DS . 'user' . DS . 'planning' . DS . 'searchResultsUser.php';
                $output = ob_get_clean();
                echo json_encode(['success' => true, 'data' => $output]);
            } else {
                echo json_encode(['success' => false, 'message' => 'User not found.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No user ID provided.']);
        }
        exit;
    }


    /**
     * sendNotification
     * 
     * Check if there's an ID in the POST request. If so, send it with the session ID so the Model can use them with the database
     *
     * @return void
     */
    public function sendNotification()
    {
        header('APPJSON');
        $idReceiver = isset($_POST['receiverId']) ? $_POST['receiverId'] : '';

        if (!empty($idReceiver)) {
            $data = $this->nutriModel->checkNotifThenSend($idReceiver, $_SESSION['id']);

            if ($data) {
                // envoi du mail avec $data['email']
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'User not found.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No user ID provided for notification.']);
        }
    }


    /**
     * Show All Cients for a Nutritionist
     *
     * Retrieves all clients for a given nutritionist from the model and display them through <strong>clients-table.php</strong>.
     *
     * @param int $nutritionistId The ID of the nutritionist
     * @return void
     */
    public function getUsersForNutritionist()
    {
        header('APPJSON');
        $nutritionistId = isset($_GET['nutri_id']) ? $_GET['nutri_id'] : '';

        // Call the model method to get users for the nutritionist
        $data = $this->nutriModel->getUserProgressForNutritionist($nutritionistId);

        if ($data) {
            $usersProgress = [];
            foreach ($data as $row) {
                $progressPercentage = min(100, max(0, $row->progress * 100));
                $usersProgress[] = [
                    'user_id' => $row->id,
                    'fullname' => $row->fullname,
                    'email' => $row->email,
                    'goal' => $row->goal,
                    'img' => $row->img,
                    'plan_progress' => number_format($progressPercentage, 2) . '%',
                    'plan_creation_date' => $row->creation_date,
                ];
            }

            $response = [
                'total_users' => $data[0]->total_users,
                'not_completed' => $data[0]->not_completed,
                'completed' => $data[0]->completed,
                'users_progress' => $usersProgress,
            ];

            // Output buffering to capture the included file's content
            ob_start();
            include VIEWSDIR . DS . 'components' . DS . 'nutritionist' . DS . 'list-client-element.php';
            $output = ob_get_clean();

            // Echo the content captured, which now includes $data being used in usersList.php
            echo json_encode(['message' => $output]);
        } else {
            echo json_encode(['message' => '<h3 class="text-center text-secondary mt-5">No clients present for this nutritionist!</h3>']);
        }
        exit;
    }
    /**
     * Count Nutritionist's Clients
     * 
     * Retrieves and returns the count of clients for a given nutritionist.
     *
     * @param int $nutritionistId The ID of the nutritionist.
     * @return void
     */
    public function countNutritionistClients()
    {

        $nutritionistId = isset($_GET['nutri_id']) ? $_GET['nutri_id'] : '';

        try {
            $clientsCount = $this->nutriModel->getClientsCountForNutritionist($nutritionistId);

            // Assuming the count is successfully retrieved, send a JSON response
            echo json_encode(['success' => true, 'count' => $clientsCount]);
        } catch (\PDOException $e) {
            // If an error occurs, send a JSON response with the error message
            echo json_encode(['success' => false, 'message' => 'An error occurred while fetching the client count for the nutritionist.']);
        }
        exit;
    }

    /**
     * Count Recipes for a Creator
     * 
     * Retrieves and returns the count of recipes for a given creator.
     *
     * @param int $creatorId The ID of the creator.
     * @return void
     */
    public function countRecipesForCreator()
    {

        $creatorId = isset($_GET['nutri_id']) ? $_GET['nutri_id'] : '';

        try {
            $recipesCount = $this->nutriModel->getRecipesCountForCreator($creatorId);

            echo json_encode(['success' => true, 'count' => $recipesCount]);
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'An error occurred while fetching the recipe count for the creator.']);
        }
        exit;
    }
    /**
     * 
     * Retrieves and processes progress data for users associated with a specific nutritionist.
     * 
     * Expects the nutritionist ID as a GET parameter 'nutri_id'.
     * Prepares a response array with total users, not completed, completed, and user progress data.
     * Echoes the response as a JSON-encoded array with a success or error message.
     *
     * @return void
     **/
    public function getUserProgressForNutritionist()
    {
        header(APPJSON);

        $nutritionistId = isset($_GET['nutri_id']) ? $_GET['nutri_id'] : '';

        $data = $this->nutriModel->getUserProgressForNutritionist($nutritionistId);

        if ($data) {
            // Prepare the data for JSON encoding
            $usersProgress = [];
            foreach ($data as $row) {
                $progressPercentage = min(100, max(0, $row->progress * 100));
                $usersProgress[] = [
                    'user_id' => $row->id,
                    'fullname' => $row->fullname,
                    'email' => $row->email,
                    'goal' => $row->goal,
                    'img' => $row->img,
                    'plan_progress' => number_format($progressPercentage, 2) . '%',
                    'plan_creation_date' => $row->creation_date,
                ];
            }
            // Trie le tableau des utilisateurs en fonction du pourcentage de progression
            usort($usersProgress, function ($a, $b) {
                return (float) $b['plan_progress'] - (float) $a['plan_progress'];
            });


            // Limite le tableau à 6 éléments
            $limitedUsersProgress = array_slice($usersProgress, 0, 6);

            $response = [
                'total_users' => $data[0]->total_users,
                'not_completed' => $data[0]->not_completed,
                'completed' => $data[0]->completed,
                'users_progress' => $limitedUsersProgress,
            ];


            echo json_encode(['message' => 'Success', 'data' => $response]);
        } else {
            echo json_encode(['message' => 'No progress data found for the specified nutritionist.']);
        }
        exit;
    }

    /**
     * Handles the deletion of a client.
     *
     * This method is called when a request to delete a client is received.
     * It retrieves the client ID from the POST data, calls the model to delete the client,
     * and then returns a JSON response indicating the success or failure of the operation.
     *
     * @return void Outputs a JSON response with the operation result.
     */
    public function deleteClient()
    {
        if (isset($_POST['del_id'])) {
            $del_id = $_POST['del_id'];
            $result = $this->nutriModel->deleteClientById($del_id);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'User deleted successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete user.']);
            }
            exit;
        }
    }

            /**
         * Checks if a client has an active plan and retrieves plan details if available.
         *
         * This method is responsible for determining whether a client has an active plan. If the client has a plan,
         * it retrieves the plan recipes details and plan information from the model and returns them in a JSON response.
         * If the client doesn't have a plan, it returns a JSON response indicating the absence of a plan.
         *
         * @param int $clientId The ID of the client to check for an active plan.
         * @return void Outputs a JSON response containing the result of the operation,
         *               including the plan details if a plan exists for the client.
         */
    public function clientHavePlan($clientId)
    {

        if ($this->nutriModel->ifClientHavePlan($clientId)) {
            $result = $this->nutriModel->getPlanRecipesDetail($clientId);
            $data = $result['planRecipesDetails'];
            $planInfo = $result['planData'];
            echo json_encode(['success' => true, 'message' => 'PlanExist', 'data' => $data, 'planInfo' => $planInfo]);
        } else {
            echo json_encode(['success' => true, 'message' => 'noPlanExist']);
        }
    }

        /**
         * Adds a new plan for a client.
         *
         * This method is responsible for adding a new plan for a client in the system.
         * It takes the client ID, recipes data, period, duration, and plan name as parameters,
         * calls the model to insert the plan into the database, and returns a JSON response
         * indicating the success or failure of the operation.
         *
         * @param int $clientId The ID of the client for whom the plan is being added.
         * @param array $recipesData An array containing the details of the recipes included in the plan.
         * @param string $period The period for which the plan is valid.
         * @param int $duration The duration of the plan in days.
         * @param string $planName The name of the plan.
         * @return void Outputs a JSON response indicating the result of the operation.
         */

    public function addPlan($clientId, $recipesData, $period, $duration, $planName)
    {
        if ($this->nutriModel->addClientPlan($clientId, $recipesData, $period, $duration, $planName)) {
            echo json_encode(['success' => true, 'message' => "plan added"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'failed to insert plan']);
        }
    }
}
