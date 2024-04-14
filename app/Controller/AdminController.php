<?php

namespace Manger\Controller;

use Manger\Model\{
    AdminModel,
    UserModel
};

/**
 * Controller for Admin-related things.
 * 
 * Handle actions such as registration, login, logout, and all modifications of attributes.
 */
class AdminController
{

    /**
     * adminModel
     *
     * @var AdminModel
     */
    private $adminModel;
    /**
     * userModel
     *
     * @var UserModel
     */
    private $userModel;



    /**
     * Constructor
     *
     * Initializes the Admins Controller with the Admin Model.
     */
    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->userModel = new UserModel();
    }


    /**
     * Show All Users
     *
     * Retrieves all users from the model and display them through <strong>users-table.php</strong>.
     *
     * @return void
     */
    public function getAllUsers()
    {
        header('APPJSON');
        $data = $this->adminModel->getAllUsers();

        if ($data) {
            // Output buffering to capture the included file's content
            ob_start();
            include VIEWSDIR . DS . 'components' . DS . 'admin' . DS . 'users-table.php';
            $output = ob_get_clean();

            // Echo the content captured, which now includes $data being used in usersList.php
            echo json_encode(['message' => $output]);
        } else {
            echo json_encode(['message' => '<h3 class="text-center text-secondary mt-5">No users present in the database!</h3>']);
        }
        exit;
    }


    /**
     * Show All Requests from nutritionists
     *
     * Retrieves all requests from the model and display them through <strong>nutri-requests-table.php</strong>.
     *
     * @return void
     */
    public function getNutritionistRequests()
    {
        header('APPJSON');
        $data = $this->adminModel->getAllRequests();

        if ($data) {
            // Output buffering to capture the included file's content
            ob_start();
            include VIEWSDIR . DS . 'components' . DS . 'admin' . DS . 'nutri-requests-table.php';
            $output = ob_get_clean();

            // Echo the content captured, which now includes $data being used in usersList.php
            echo json_encode(['message' => $output]);
        } else {
            echo json_encode(['message' => '<h3 class="text-center text-secondary mt-5">No requests present in the database!</h3>']);
        }
        exit;
    }


    /**
     * Fetch and display user information by ID.
     *
     * Responds to an AJAX request by fetching a user's details based on the provided ID.
     * The user's information is returned as a JSON object for use in the frontend.
     *
     * @return void Outputs the user data in JSON format.
     */
    public function getUserDetails()
    {
        header('APPJSON');
        $userId = isset($_GET['info_id']) ? $_GET['info_id'] : '';

        if (!empty($userId)) {
            $data = $this->adminModel->getUserById($userId);

            if ($data) {
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'User not found.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No user ID provided.']);
        }
        exit;
    }

    /**
     * Handles the deletion of a user.
     *
     * This method is called when a request to delete a user is received.
     * It retrieves the user ID from the POST data, calls the model to delete the user,
     * and then returns a JSON response indicating the success or failure of the operation.
     *
     * @return void Outputs a JSON response with the operation result.
     */
    public function deleteUser()
    {
        if (isset($_POST['del_id'])) {
            $del_id = $_POST['del_id'];
            $result = $this->adminModel->deleteUserById($del_id);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'User deleted successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete user.']);
            }
            exit;
        }
    }

    /**
     * Get All Recipes
     * 
     * Retrieves all the recipes from the database and returns them in a JSON format.
     * If recipes are found, the function dynamically includes a PHP view file (for example, a table of recipes),
     * captures its output using output buffering, and returns this HTML as part of the JSON response.
     * If no recipes are found, a JSON response with a message indicating the absence of recipes is returned.
     *
     * @return void Outputs a JSON-encoded string. On success, this string includes HTML content generated 
     *              by including a view file that displays the recipes in a user-friendly format.
     *              On failure, it includes a simple message indicating that no recipes are available.
     */

    public function getAllRecipes()
    {
        header('APPJSON');
        $data = $this->adminModel->getAllRecipes();
        if ($data) {
            // Output buffering to capture the included file's content
            ob_start();
            include VIEWSDIR . DS . 'components' . DS . 'admin' . DS . 'recipes-table.php';
            $output = ob_get_clean();

            // Echo the content captured, which now includes $data being used in recipesList.php
            echo json_encode(['message' => $output]);
        } else {
            echo json_encode(['message' => '<h3 class="text-center text-secondary mt-5">No recipes present in the database!</h3>']);
        }
        exit;
    }

    /**
     * Get Recipe Details
     * 
     * Retrieves and returns the details of a recipe based on the provided recipe ID.
     * The recipe ID is expected to be passed via the 'info_id' query parameter.
     * If a recipe is found with the provided ID, the function returns a JSON response 
     * with the recipe details. If no recipe is found, or no ID is provided, 
     * it returns a JSON response indicating failure.
     *
     * @return void
     */
    public function getRecipeDetails()
    {
        header('APPJSON');
        $recipeId = isset($_GET['info_id']) ? $_GET['info_id'] : '';

        if (!empty($recipeId)) {
            $data = $this->adminModel->getRecipeById($recipeId);

            if ($data) {
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'User not found.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No user ID provided.']);
        }
        exit;
    }

    /**
     * Count Regular Users
     * 
     * Retrieves and returns the count of users with a role of 'regular'.
     *
     * @return void
     */
    public function countRegularUsers()
    {
        try {
            $regularUsersCount = $this->adminModel->getRegularUsersCount();

            // Assuming the count is successfully retrieved, send a JSON response
            echo json_encode(['success' => true, 'count' => $regularUsersCount]);
        } catch (\PDOException $e) {
            // If an error occurs, send a JSON response with the error message
            echo json_encode(['success' => false, 'message' => 'An error occurred while fetching the user count.']);
        }
        exit; // Ensure no further script execution
    }
    /**
     * Count Regular Users
     * 
     * Retrieves and returns the count of users with a role of 'regular'.
     *
     * @return void
     */
    public function countNutritionistUsers()
    {
        try {
            $nutritionistCount = $this->adminModel->getNutritionistCount();


            // Assuming the count is successfully retrieved, send a JSON response
            echo json_encode(['success' => true, 'count' => $nutritionistCount]);
        } catch (\PDOException $e) {
            // If an error occurs, send a JSON response with the error message
            echo json_encode(['success' => false, 'message' => 'An error occurred while fetching the user count.']);
        }
        exit; // Ensure no further script execution
    }


    /**
     * Count Recipes
     * 
     * Retrieves and returns the count of Recipes.
     *
     * @return void
     */
    public function countRecipes()
    {
        try {
            $recipesCount = $this->adminModel->getRecipesCount();


            // Assuming the count is successfully retrieved, send a JSON response
            echo json_encode(['success' => true, 'count' => $recipesCount]);
        } catch (\PDOException $e) {
            // If an error occurs, send a JSON response with the error message
            echo json_encode(['success' => false, 'message' => 'An error occurred while fetching the user count.']);
        }
        exit; // Ensure no further script execution
    }


    /**
     * Add a new user with profile image.
     *
     * Processes the form submission, sanitizes input, handles profile image upload,
     * and add a new user in the database. It checks for an existing user with the same
     * email, handles password hashing, and includes the profile image's filename in the database.
     * Responds with JSON indicating success or failure.
     *
     * @return void Outputs JSON response.
     */
    public function addNewUser()
    {
        $imageUploadPath = ''; 
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["imageUpload"])) {
            $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/archiweb_2024_projets_gr06/public/images/profile-images/';
            $fileName = basename($_FILES["imageUpload"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            if (move_uploaded_file($_FILES["imageUpload"]["tmp_name"], $targetFilePath)) {
                $imageUploadPath = '/public/images/profile-images/' . $fileName;
            } 
        }

        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $fullname = trim($_POST['fullname'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $data = [
            'fullname' => $fullname,
            'password' => $password,
            'email' => $email,
            'image' => $imageUploadPath
        ];

        if ($this->userModel->findUserByEmail($data['email'])) {
            header(APPJSON);
            echo json_encode(['success' => false, 'message' => 'Email already exists']);
            return;
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        if ($this->adminModel->addNewUser($data)) {
            header(APPJSON);
            echo json_encode(['success' => true, 'redirect' => 'login.php']);
        } else {
            header(APPJSON);
            echo json_encode(['success' => false, 'message' => 'Something went wrong']);
        }
    }
    /**
     * Update a user's profile information.
     *
     * Processes the form submission, sanitizes input, handles profile image upload (if provided),
     * and updates the user's information in the database. It checks for existing user data, sanitizes
     * input fields, and includes optional profile image file upload handling. Responds with JSON
     * indicating success or failure of the update operation.
     *
     * @return void Outputs JSON response.
     */
    public function updateUser()
    {
        // Check if a new file was uploaded and handle the file upload first
        $newImageUploadPath = ''; // Default value if no new file is uploaded
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["edit_imageUpload"])) {
            $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/archiweb_2024_projets_gr06/public/images/profile-images/';
            $fileName = basename($_FILES["edit_imageUpload"]["name"]);
            $targetFilePath = $targetDir . $fileName;

            // Optional: Validate file size and type here before proceeding with the upload

            // Create the target directory if it doesn't exist
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["edit_imageUpload"]["tmp_name"], $targetFilePath)) {
                $newImageUploadPath = '/public/images/profile-images/' . $fileName;
            } else {
                // Handle file upload failure
                header(APPJSON);
                echo json_encode(['success' => false, 'message' => 'File upload failed']);
                exit; // Stop execution if file upload fails
            }
        }

        // Sanitize input data for user update
        $id = trim($_POST['edit_user_id'] ?? '');
        $firstname = trim($_POST['edit_fname'] ?? '');
        $email = trim($_POST['edit_email'] ?? '');
        $gender = trim($_POST['edit_gender'] ?? '');
        $goal = trim($_POST['edit_goal'] ?? '');
        $age = trim($_POST['edit_age'] ?? '');
        $role = trim($_POST['edit_role'] ?? '');
        $height = trim($_POST['edit_height'] ?? '');
        $weight = trim($_POST['edit_weight'] ?? '');
        $caloriesgoal = trim($_POST['edit_caloriesgoal'] ?? '');

        // Assumption: $newImageUploadPath is set elsewhere in your script, after processing any uploaded file
        // For example, after checking and moving the uploaded file to a permanent location

        $data = [
            'id' => $id,
            'firstname' => $firstname,
            'email' => $email,
            'gender' => $gender,
            'goal' => $goal,
            'age' => (int) $age, // Casting to int for safety
            'role' => $role,
            'height' => (int) $height, // Casting to int for safety
            'weight' => (int) $weight, // Casting to int for safety
            'caloriesgoal' => (int) $caloriesgoal, // Casting to int for safety
            'image' => $newImageUploadPath ?? ''
        ];

        // Attempt to update the recipe
        if ($this->adminModel->updateUser($data)) {
            header(APPJSON);
            echo json_encode(['success' => true, 'message' => 'User updated successfully']);
        } else {
            header(APPJSON);
            echo json_encode(['success' => false, 'message' => 'Something went wrong']);
        }
    }
    /**
     * Request Promotion
     *
     * This method handles the creation of a promotion request for the currently logged-in user.
     * It retrieves the user's ID from the session, submits a promotion request, and returns a JSON response.
     * If the request is successfully added, it responds with success; otherwise, it indicates failure.
     *
     * @return void Outputs a JSON response with the result.
     */
    public function requestPromotion()
    {
        $clientid = $_SESSION['id']; // Get the user ID from session.

        // Attempt to submit a promotion request for the user.
        if ($this->adminModel->requestPromotion($clientid)) {
            echo json_encode(['success' => true, 'message' => "Request Sent"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Something went wrong with the Request']);
        }
    }

    /**
     * Accept Request
     *
     * This method processes a request to accept a promotion request using the request ID provided via POST.
     * It calls the admin model to update the user's role and, if successful, returns a JSON response indicating success.
     * If the operation fails, it returns an error message in JSON format.
     *
     * @return void Outputs a JSON response with the result.
     */
    public function acceptRequest()
    {
        $clientid = $_POST['acc_id']; // Get the request ID from POST data.

        // Process the acceptance of the request.
        if ($this->adminModel->acceptRequest($clientid)) {
            echo json_encode(['success' => true, 'message' => "Request Accepted"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Something went wrong with the Request']);
        }
    }


    /**
     * Delete Request
     *
     * This method handles the deletion of a promotion request using the ID provided via POST.
     * It calls the admin model to delete the request from the database.
     * The method returns a JSON-encoded result indicating whether the operation was successful.
     *
     * @return void Outputs a JSON response with the result.
     */
    public function deleteRequest()
    {
        $clientid = $_POST['del_id']; // Get the request ID from POST data.

        // Attempt to delete the request.
        if ($this->adminModel->deleteRequest($clientid)) {
            echo json_encode(['success' => true, 'message' => "Request Deleted"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Something went wrong with the Request']);
        }
    }


    /**
     * Add a new Recipe with image.
     *
     * Processes the form submission, sanitizes input, handles image upload,
     * and add a new recipe in the database. It includes the profile image's filename in the database.
     * Responds with JSON indicating success or failure.
     *
     * @return void Outputs JSON response.
     */

    public function addNewRecipe()
    {
        // Check if a file was uploaded and handle the file upload first
        $imageUploadPath = ''; // Default value if no file is uploaded
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["imageUpload"])) {
            $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/archiweb_2024_projets_gr06/public/images/recipesImages/';
            $fileName = basename($_FILES["imageUpload"]["name"]);
            $targetFilePath = $targetDir . $fileName;

            // Optional: Validate file size and type here before proceeding with the upload

            // Create the target directory if it doesn't exist
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["imageUpload"]["tmp_name"], $targetFilePath)) {
                $imageUploadPath = $fileName;
            } else {
                // Handle file upload failure
                header(APPJSON);
                echo json_encode(['success' => false, 'message' => 'File upload failed']);
                exit; // Stop execution if file upload fails
            }
        }

        // Sanitize input data
        $name = trim($_POST['name'] ?? '');
        $calories = trim($_POST['calories'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $visibility = ($_POST['visibility'] ?? '') === 'visible' ? 1 : 0;
        $creationDate = trim($_POST['creation_date'] ?? '');
        $creator = trim($_POST['creator'] ?? '');

        $data = [
            'name' => $name,
            'calories' => $calories,
            'type' => $type,
            'visibility' => $visibility,
            'creation_date' => $creationDate,
            'creator' => $creator,
            'image' => $imageUploadPath
        ];


        // Attempt to register the recipe
        if ($this->adminModel->addNewRecipe($data)) {
            header(APPJSON);
            echo json_encode(['success' => true, 'redirect' => 'login.php']);
        } else {
            header(APPJSON);
            echo json_encode(['success' => false, 'message' => 'Something went wrong']);
        }
    }
    /**
     * Update an existing recipe with or without a new image.
     *
     * Processes the form submission, sanitizes input, handles image upload (if a new image is provided),
     * and updates the recipe in the database. If a new image is provided, it updates the image filename in the database.
     * Responds with JSON indicating success or failure.
     *
     * @return void Outputs JSON response.
     */
    public function updateRecipe()
    {
        // Check if a new file was uploaded and handle the file upload first
        $newImageUploadPath = ''; // Default value if no new file is uploaded
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["edit_imageUpload"])) {
            $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/archiweb_2024_projets_gr06/public/images/recipesImages/';
            $fileName = basename($_FILES["edit_imageUpload"]["name"]);
            $targetFilePath = $targetDir . $fileName;

            // Optional: Validate file size and type here before proceeding with the upload

            // Create the target directory if it doesn't exist
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["edit_imageUpload"]["tmp_name"], $targetFilePath)) {
                $newImageUploadPath = '/public/images/recipesImages/' . $fileName;
            } else {
                // Handle file upload failure
                header(APPJSON);
                echo json_encode(['success' => false, 'message' => 'File upload failed']);
                exit; // Stop execution if file upload fails
            }
        }

        // Sanitize input data
        $id = trim($_POST['edit_id'] ?? '');
        $name = trim($_POST['edit_name'] ?? '');
        $calories = trim($_POST['edit_calories'] ?? '');
        $type = trim($_POST['edit_type'] ?? '');
        $visibility = ($_POST['edit_visibility'] ?? '') === 'visible' ? 1 : 0;
        $creator = trim($_POST['edit_creator'] ?? '');

        $data = [
            'id' => $id,
            'name' => $name,
            'calories' => $calories,
            'type' => $type,
            'visibility' => $visibility,
            'creator' => $creator,
            'image' => $newImageUploadPath
        ];

        // Attempt to update the recipe
        if ($this->adminModel->updateRecipe($data)) {
            header(APPJSON);
            echo json_encode(['success' => true, 'message' => 'Recipe updated successfully']);
        } else {
            header(APPJSON);
            echo json_encode(['success' => false, 'message' => 'Something went wrong']);
        }
    }
    /**
     * Handles the deletion of a recipe.
     *
     * This method is called when a request to delete a recipe is received.
     * It retrieves the recipe ID from the POST data, calls the model to delete the recipe,
     * and then returns a JSON response indicating the success or failure of the operation.
     *
     * @return void Outputs a JSON response with the operation result.
     */

    public function deleteRecipe()
    {
        if (isset($_POST['del_id'])) {
            $del_id = $_POST['del_id'];
            $result = $this->adminModel->deleteRecipeById($del_id);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Recipe deleted successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete Recipe.']);
            }
            exit;
        }
    }
}
