<?php

namespace Manger;

use Manger\Controller\{
    CommunicationController,
    UserController,
    AdminController,
    NutritionistController,
    ResetPasswords
};


/**
 * Router
 * 
 * The routing part of the project, decide to which controller
 * go based on the sent request
 */
class Router
{
    private $userController;
    private $adminController;
    private $resetPasswordController;
    private $nutriController;
    private $commController;

    public function __construct()
    {
        $this->userController = new UserController();
        $this->adminController = new AdminController();
        $this->commController = new CommunicationController();

        $this->resetPasswordController = new ResetPasswords();
        $this->nutriController = new NutritionistController();
    }


    /**
     * Handles incoming HTTP requests and routes to appropriate controllers.
     *
     * This function is the main function of the project's router, adhering to
     * the MVC (Model-View-Controller) architecture. It analyzes the URL of the request,
     * determines the controller to use based on URL segments, and then calls
     * appropriate controller methods based on the request method (GET or POST) and
     * request parameters.
     *
     * @return void
     */
    public function manageRequest()
    {

        // Parse the path from the URL
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Explode the path into segments
        $uriSegments = explode('/', $path);

        // Assuming the action is always after the project base in the URL
        // and adjusting for unconventional query parameter format
        $requestedRaw = isset($uriSegments[2]) ? $uriSegments[2] : "";

        // Separate the action from any following query string that starts unusually with '&'
        list($requested,) = explode('&', $requestedRaw, 2);

        $controller = "user"; // Default controller

        // Check if the requested segment matches 'admin' or 'nutritionist'
        if ($requested === 'admin' || $requested === 'nutritionist') {
            $controller = $requested;
            $requested = isset($uriSegments[3]) ? $uriSegments[3] : "";
            // Again, separate the actual request from any unconventional query string
            list($requested,) = explode('&', $requested, 2);
        }

        // Fallback to "login" if no specific action is requested
        $requested = $requested !== "" ? $requested : "login";

        // Define pages that do not require redirect
        $no_redirect_pages = array('login', 'register', 'reset-password', 'create-new-password');


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            switch ($_POST['action']) {
                case 'register':
                    $this->userController->register();
                    break;
                case 'login':
                    $this->userController->login();
                    break;
                case 'resetPassword':
                    $this->resetPasswordController->sendEmail();
                    break;
                case 'newPassword':
                    $this->resetPasswordController->resetPassword();
                    break;
                case 'update-user-details':
                    $this->userController->updateUserDetails();
                    break;
                case 'update-user-credentials':
                    $this->userController->updateUserCredentials();
                    break;
                case 'first-login':
                    $this->userController->updateUserFirstLogin();
                    break;
                case 'showAllRecipes':
                    $this->userController->recipesList();
                    break;
                case 'addRecipe':
                    $this->userController->addNewRecipe();
                    break;
                case 'logout':
                    $this->userController->logout();
                    break;
                case 'deleteUser':
                    $this->adminController->deleteUser();
                    break;
                case 'deleteClient':
                    $this->nutriController->deleteClient();
                    break;
                case 'sendNotification':
                    $this->nutriController->sendNotification();
                    break;
                case 'updateNotification':
                    $this->userController->updateNotificationState();
                    break;
                case 'addNewUser':
                    $this->adminController->addNewUser();
                    break;
                case 'addNewRecipe':
                    $this->adminController->addNewRecipe();
                    break;
                case 'updateRecipe':
                    $this->adminController->updateRecipe();
                    break;
                case 'updateUser':
                    $this->adminController->updateUser();
                    break;
                case 'insertPlan':
                    if (isset($_POST['recipesData']) && isset($_POST['period']) && isset($_POST['duration'])) {
                        $_SESSION['etatPlan']= "show";
                        $recipesData = json_decode($_POST['recipesData'], true);
                        $period = $_POST['period'];
                        $duration = $_POST['duration'];
                        $planName = $_POST['planName'];
                        $this->userController->addPlan($recipesData, $period, $duration, $planName);
                    }
                    break;


                case 'UserHavePlan':
                    $this->userController->userHavePlan();
                    break;
                case 'ClientHavePlan':
                    $this->nutriController->clientHavePlan($_POST['additionalData']);
                    break;

                case 'deleteRecipe':
                    $this->adminController->deleteRecipe();
                    break;
                case 'sendMessage':
                    $this->commController->sendMessage();
                    break;

                case 'toggleRecipeConsumed':
                    if (isset($_POST['recipe_id'])) {
                        $recipe_id = json_decode($_POST['recipe_id'], true);
                        $this->userController->toggleRecipeConsumed($recipe_id);
                    }
                    break;
                case 'addClientPlan':
                    if (isset($_POST['recipesData']) && isset($_POST['period']) && isset($_POST['duration']) && isset($_POST['clientId'])) {
                        $recipesData = json_decode($_POST['recipesData'], true);
                        $period = $_POST['period'];
                        $duration = $_POST['duration'];
                        $planName = $_POST['planName'];
                        $clientId = $_POST['clientId'];
                        $this->nutriController->addPlan($clientId, $recipesData, $period, $duration, $planName);
                    }
                    break;
                case "requestPromotion":
                    $this->adminController->requestPromotion();
                    break;

                case "acceptRequest":
                    $this->adminController->acceptRequest();
                    break;

                case "deleteRequest":
                    $this->adminController->deleteRequest();
                    break;

                        case 'modifyPlan':
                           
                            $_SESSION['etatPlan']= "update";
                            echo json_encode(true);
                            break;
        
                default:
                    include __DIR__ . '/View/templates/user/login.php';
                    exit;
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {


            // Check if the session exists, and redirect if necessary
            if (!isset($_SESSION['id']) && !in_array($requested, $no_redirect_pages)) {
                $this->userController->GETPage("login");
                exit();
            }

            // Check for specific actions in the GET request
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'countRegularUsers':
                        $this->adminController->countRegularUsers();
                        break;
                    case 'countNutritionistUsers':
                        $this->adminController->countNutritionistUsers();
                        break;
                    case 'countRecipes':
                        $this->adminController->countRecipes();
                        break;
                    case 'getAllUsers':
                        $this->adminController->getAllUsers();
                        break;
                    case 'getUserDetails':
                        $this->adminController->getUserDetails();
                        break;
                    case 'loadUserDetails':
                        $this->adminController->getUserDetails();
                        break;
                    case 'getAllRecipes':
                        $this->adminController->getAllRecipes();
                        break;
                    case 'getRecipeDetails':
                        $this->adminController->getRecipeDetails();
                        break;
                    case 'loadRecipeDetails':
                        $this->adminController->getRecipeDetails();
                        break;
                    case 'planSearchForRecipe':
                        $this->userController->getRecipesByName();
                        break;
                    case 'clientSearch':
                        $this->nutriController->getClientList();
                        break;
                    case "countNotification":
                        $this->userController->countNotification();
                        break;
                    case "getNutriClients":
                        $this->nutriController->getUsersForNutritionist();
                        break;
                    case "getNutriRequests":
                        $this->adminController->getNutritionistRequests();
                        break;
                    case "nutriRecipesCount":
                        $this->nutriController->countRecipesForCreator();
                        break;
                    case "getUserProgress":
                        $this->nutriController->getUserProgressForNutritionist();
                        break;
                    case "getUsersFromNotifications":
                        $this->userController->getUsersFromNotifications();
                        break;
                    case "nutriCurrentClients":
                        $this->nutriController->countNutritionistClients();
                        break;
                    case "getDiscussion":
                        $this->commController->getDiscussion();
                        break;
                    case 'getMessagesFromAConvo':
                        $this->commController->getMessagesFromAConvo();
                        break;
                    
                    default:
                        // If no specific action, fallback to generic page handling
                        $this->userController->GETPage($requested);
                        break;
                }
            } else {
                // No action specified, handle as a page request
                $this->userController->GETPage($requested);
            }
        }
    }
}