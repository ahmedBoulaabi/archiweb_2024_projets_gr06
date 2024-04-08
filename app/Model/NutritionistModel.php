<?php

namespace Manger\Model;

use Config\Database;

/**
 * NutritionistModel Class
 *
 * Represents the model for managing nutritionist data.
 */
class NutritionistModel
{
    /**
     * @var Database The database instance.
     */
    private $db;

    /**
     * Admin constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get All Users
     *
     * Retrieves all users for the admin dashboard.
     *
     * @return array|false An array of user data if users are found, or false if no users are present.
     */
    public function getUserByFullname($namePart, $userType)
    {
        if ($userType == "Regular") { // les regular verront les nutritionnistes
            $targetType = "Nutritionist";
        } else if ($userType == "Nutritionist") { // les nutritionnistes verront les regular
            $targetType = "Regular";
        } else { // juste pour éviter les bugs si un admin cherche ( admin n'est pas censé chercher though)
            $targetType = "Admin";
        }
        $sql = "SELECT * FROM users WHERE fullname LIKE CONCAT('%', :namePart, '%') AND role=:targetType LIMIT 4;";
        $this->db->query($sql);
        $this->db->bind(':namePart', $namePart);
        $this->db->bind(':targetType', $targetType);


        $results = $this->db->resultSet();

        if ($this->db->rowCount() > 0) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * checkIfWaitingNotification
     * 
     * Check if a notification already exists with the given receiver and sender ids and type 1
     * by counting the number of rows affected
     *
     * @param int $receiverID
     * @param int $senderID
     * @return bool
     */
    private function checkIfWaitingNotification($receiverID, $senderID)
    {
        $this->db->query("SELECT COUNT(*) FROM notifications WHERE receiver_id = :receiverID AND sender_id = :senderID AND type = 1");
        $this->db->bind(':receiverID', $receiverID);
        $this->db->bind(':senderID', $senderID);
        $this->db->execute();
        $count = $this->db->fetchCount(); // récupère le résultat COUNT(*)

        return $count > 0;
    }


    /**
     * checkNotifThenSend
     * 
     * Fetch the email of the user clicked on, then send them a notification through the database
     *
     * @param  mixed $receiverID Id clicked on
     * @param  mixed $senderID Own Id, stocked in the session
     * @return bool|mixed return the user clicked on on success, to send them an email
     */
    public function checkNotifThenSend($receiverID, $senderID)
    {
        $sql = "SELECT * FROM users WHERE id=:receiverID;";
        $this->db->query($sql);
        $this->db->bind(':receiverID', $receiverID);


        $result = $this->db->single();
        if (!empty($result) && !$this->checkIfWaitingNotification($receiverID, $senderID)) {

            //  si utilisateur existe, et si pas de notif déjà en attente d'accept/decline
            $addQuery = "INSERT INTO notifications (`receiver_id`,`sender_id`,`type`) VALUES (:receiverID,:senderID,1)";
            $this->db->query($addQuery);
            $this->db->bind(':receiverID', $receiverID);
            $this->db->bind(':senderID', $senderID);
            if ($this->db->execute()) { // si les 2 requêtes se sont bien passées on renvoit les données de l'user cliqué
                return $result->email;
            }
        }

        return false;
    }


    /**
     * Get All Client for a Nutritionist
     *
     * Retrieves all clients for a given nutritionist from the users table.
     *
     * @param int $nutritionistId The ID of the nutritionist
     * @return array|false An array of user data if users are found, or false if no users are present.
     */
    public function getUsersForNutritionist($nutritionistId)
    {
        $data = array();
        $sql = "SELECT u.* 
                    FROM users u
                    JOIN nutritionist_client nc ON u.id = nc.client_id
                    WHERE nc.nutritionist_id = :nutritionist_id";


        $this->db->query($sql);
        $this->db->bind(':nutritionist_id', $nutritionistId);
        $rows = $this->db->resultSet();

        if ($this->db->rowCount() > 0) {
            // If rows are found, iterate through them and add to the data array
            foreach ($rows as $row) {
                $data[] = $row;
            }
            return $data;
        } else {
            // If no rows are found, return false
            return false;
        }
    }

    /**
     * Get Count of Clients for a Nutritionist
     *
     * Returns the number of clients for a given nutritionist ID.
     *
     * @param int $nutritionistId The ID of the nutritionist.
     * @return int The count of clients for the given nutritionist.
     */
    public function getClientsCountForNutritionist($nutritionistId)
    {
        $sql = "SELECT COUNT(*) AS clientCount FROM nutritionist_client WHERE nutritionist_id = :nutritionistId";

        $this->db->query($sql);
        $this->db->bind(':nutritionistId', $nutritionistId);
        $this->db->execute();

        $row = $this->db->single();

        if ($row) {
            return $row->clientCount;
        } else {
            return 0;
        }
    }

    /**
     * Get Count of Recipes for a Creator
     *
     * Returns the number of recipes created by a given creator ID.
     *
     * @param int $creatorId The ID of the creator.
     * @return int The count of recipes for the given creator.
     */
    public function getRecipesCountForCreator($creatorId)
    {
        $sql = "SELECT COUNT(*) AS recipeCount FROM recipes WHERE creator = :creatorId";

        $this->db->query($sql);
        $this->db->bind(':creatorId', $creatorId);
        $this->db->execute();

        $row = $this->db->single();

        if ($row) {
            return $row->recipeCount;
        } else {
            return 0; // In case of no recipes or an error
        }
    }

    /**
     * getUserProgressForNutritionist
     * 
     * Retrieves the progress data for all clients associated with a specific nutritionist.
     *
     * This method fetches detailed progress information for each client managed by a specified nutritionist.
     * It includes each client's ID, full name, email, dietary goal, profile image, progress on their dietary plan
     * (as a percentage), and the creation date of their plan. Additionally, it calculates overall statistics such
     * as the total number of users managed by the nutritionist, the number of users who have not completed their
     * plan, and the number of users who have completed their plan.
     *
     * The progress percentage is calculated based on the current date relative to the plan's start date and total
     * length, ensuring that the progress reflects real-time information. This method is particularly useful for
     * nutritionists looking to monitor the status and progress of their clients' dietary plans.
     *
     * @return array|bool Outputs a JSON-encoded array containing a success message and the data array if progress data
     *         is found for the specified nutritionist. The data array includes keys for 'total_users', 'not_completed',
     *         'completed', and 'users_progress'. The 'users_progress' key contains an array of user progress information,
     *         each including 'user_id', 'fullname', 'email', 'goal', 'img', 'plan_progress', and 'plan_creation_date'.
     *         If no progress data is found or if the nutritionist ID is not provided, outputs a JSON-encoded array
     *         containing an error message.
     *
     * The method assumes the presence of a 'nutri_id' parameter in the request, identifying the nutritionist whose
     * client progress data is to be retrieved. It relies on the nutriModel's `getUserProgressForNutritionist` method
     * to fetch the necessary data from the database.
     */

    public function getUserProgressForNutritionist($nutritionistId)
    {

        $sql = "SELECT u.*, p.total_length, up.creation_date, 
        (DATEDIFF(CURDATE(), up.creation_date) / p.total_length) AS progress,
        COUNT(up.user_id) OVER () AS total_users,
        SUM(CASE WHEN DATEDIFF(CURDATE(), up.creation_date) < p.total_length THEN 1 ELSE 0 END) OVER () AS not_completed,
        SUM(CASE WHEN DATEDIFF(CURDATE(), up.creation_date) >= p.total_length THEN 1 ELSE 0 END) OVER () AS completed

        FROM users u
        JOIN nutritionist_client nc ON u.id = nc.client_id
        LEFT JOIN user_plan up ON u.id = up.user_id
        LEFT JOIN plans p ON up.plan_id = p.id
        WHERE nc.nutritionist_id = :nutritionist_id";




        $this->db->query($sql);
        $this->db->bind(':nutritionist_id', $nutritionistId);
        $rows = $this->db->resultSet();
        //  var_dump($rows); 
        if ($this->db->rowCount() > 0) {
            return $rows;
        } else {
            return false;
        }
    }

    /**
     * Deletes a user by their ID.
     *
     * This method executes a DELETE SQL statement to remove a user from the database.
     * It uses prepared statements to prevent SQL injection attacks.
     *
     * @param int $id The unique identifier of the user to be deleted.
     * @return bool Returns true if the operation was successful, false otherwise.
     */
    public function deleteClientById($id)
    {
        $sql = "DELETE FROM nutritionist_client WHERE 	client_id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
            /**
     * getClienPlan
     * 
     * Retrieves the plan associated with the specified user ID from the database.
     * 
     * @param int $clientId The ID of the user to retrieve the plan for.
     * @return mixed Returns the plan details if found, or false if no plan exists for the client.
     */
    function getClientPlan($clientId)
    {
        $sql = "SELECT * FROM user_plan WHERE user_id = :userId";
        $this->db->query($sql);
        $this->db->bind(':userId', $clientId);
        $plan = $this->db->single();
        if ($this->db->rowCount() > 0) {
            return $plan;
        } else {
            return false;
        }
    }
        /**
     * getPlanInfo
     * 
     * get all information about user plan from plans table 
     * 
     * @param int $planId The ID of the plan to retrieve the plan information.
     * @return mixed Returns the plan information if found, or false if no plan exist.
     */
    function getPlanInfo($planId)
    {
        $sql = "SELECT * FROM plans WHERE id = :planId";
        $this->db->query($sql);
        $this->db->bind(':planId', $planId);
        $plan = $this->db->single();
        if ($this->db->rowCount() > 0) {
            return $plan;
        } else {
            return false;
        }
    }
        /**
     * getRecipesAndDay
     *
     * Retrieves recipes along with their associated day from the database based on the provided plan ID.
     * @param int $planId The ID of the plan for which recipes are being retrieved.
     * @return array An array containing recipe information along with their associated day, or an empty array if no recipes are found.
     */
    function getRecipesAndDay($planId)
    {
        $sql = "SELECT r.*, pr.date FROM recipes r JOIN plan_recipes pr ON r.id = pr.recipe_id WHERE pr.plan_id = :planId";
        $this->db->query($sql);
        $this->db->bind(':planId', $planId);
        $recipes = $this->db->resultSet();
        return $recipes;
    }
        /**
     * getPlanRecipesDetail
     * 
     * Retrieves the details of recipes associated with the user's plan from the database.
     * This function first retrieves the user's plan using the getUserPlan method,
     * then fetches the details of recipes associated with the retrieved plan using the getPlanRecipesDetails method.
     * 
     * @return array|null Returns an array containing the details of recipes associated with the user's plan.
     *                   If no plan is found for the user or if no recipes are associated with the plan, returns null.
     */
    function getPlanRecipesDetail($clientId)
    {
        // Récupération du plan de l'utilisateur
        $userId = $_SESSION['id'];
        $plan = $this->getClientPlan($clientId);
        $planId = $plan->id;
        $planInfo = $this->getPlanInfo($planId);
        $planRecipesDetails = $this->getRecipesAndDay($planId);
        $result = array(
            'planData' => $planInfo,
            'planRecipesDetails' => $planRecipesDetails
        );
        return $result;
    }



 /**
     * addClientPlan
     * 
     * This function adds a new plan for the user based on the provided data. If a plan name
     * is not provided, it defaults to "Default Plan for Client" followed by the client's ID. 
     * The plan details are inserted into the plans table, and the user-plan relationship 
     * is stored in the user_plan table. Additionally, each recipe in the plan is inserted 
     * into the plan_recipes table.
     * 
     * @param array $recipesData An array containing information about the recipes in the plan
     * @param int $period The number of days of the plan (repeats through the duration)
     * @param int $clientId The number of days of the plan (repeats through the duration)

     * @param int $duration The total number of days of the plan
     * @param string|null $plan_name The name of the plan (optional)
     * @return bool Returns true if the plan is successfully added, false otherwise
     */
    function addClientPlan($clientId,$recipesData, $period, $duration, $plan_name)
    {
    
        // Insert into the `plans` table
        $planName = $plan_name ??  "Default Plan for User " .$clientId;
        $creatorId = $_SESSION['id']; //  user ID from the session
        $type = "Your Plan Type"; // You can define a type for the plan
        $sql = "INSERT INTO plans (name, period, total_length, creator, type) VALUES (:name, :period, :total_length, :creator, :type)";
        $this->db->query($sql);
        $params_dict = [
            ":name",
            ":period",
            ":total_length",
            ":creator",
            ":type",
        ];

        $values_dict = [
            $plan_name,
            $period,
            $duration,
            $creatorId,
            $type
        ];
        $this->db->bindMultipleParams($params_dict, $values_dict);

        $this->db->execute();
        $planId = $this->db->lastInsertId(); // Get the ID of the inserted plan

        $sql = "INSERT INTO user_plan (user_id, plan_id, creation_date) VALUES (:user_id, :plan_id, NOW())";
        $this->db->query($sql);
        $this->db->bindMultipleParams([':user_id', ':plan_id'], [$clientId, $planId]);
        $this->db->execute();

        foreach ($recipesData as $recipe) {
            $recipeId = $recipe['id'];
            $date = $recipe['date'];

            $sql = "INSERT INTO plan_recipes (plan_id, recipe_id, date) VALUES (:plan_id, :recipe_id, :date)";
            $this->db->query($sql);
            $this->db->bindMultipleParams([':recipe_id', ':plan_id', ':date'], [$recipeId, $planId, $date]);
            $this->db->execute();
        }
        return true;
    }
    

    function ifClientHavePlan($clientId)
    {
        $sql = "SELECT EXISTS (SELECT 1 FROM user_plan WHERE user_id = :userId) AS planExists";
        $this->db->query($sql);
        $this->db->bind(':userId', $clientId);
        $result = $this->db->single(); // Récupère le résultat de la clause EXISTS

        if ($result->planExists == 1) {
            return true;
        } else {
            return false;
        }
    }
}