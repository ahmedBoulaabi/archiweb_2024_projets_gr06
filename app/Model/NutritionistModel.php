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
        // Construct the SQL query with a join operation
        $sql = "SELECT u.* 
                    FROM users u
                    JOIN nutritionist_client nc ON u.id = nc.client_id
                    WHERE nc.nutritionist_id = :nutritionist_id";


        // Prepare the query
        $this->db->query($sql);
        // Bind the nutritionist ID parameter
        $this->db->bind(':nutritionist_id', $nutritionistId);
        // Execute the query
        $rows = $this->db->resultSet();

        // Check if any rows were returned
        if ($this->db->rowCount() > 0) {
            // If rows are found, iterate through them and add to the data array
            foreach ($rows as $row) {
                $data[] = $row;
            }
            // Return the data array

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
     * Retrieves progress information for all clients managed by a specific nutritionist. This includes details
     * about each user's progress with their dietary plan, the total length of their plan, and the creation date
     * of their plan. Additionally, it calculates the overall progress percentage based on the current date 
     * relative to the plan's start and total length. The function also aggregates the total number of users, 
     * the number of users who have not completed their plan, and the number of users who have completed their plan.
     * 
     * The method joins several tables: users, nutritionist_client, user_plan, and plans, to gather the required
     * information. It filters records based on the provided nutritionist ID, ensuring that only clients associated 
     * with the specified nutritionist are considered.
     * 
     * @param int $nutritionistId The unique identifier for the nutritionist whose clients' progress is being queried.
     * 
     * @return array|false Returns an array of objects where each object contains the user details along with their
     *                     plan progress, total users under the nutritionist, the count of users not completed, and
     *                     the count of users completed. If no records are found, or in the case of a query failure,
     *                     returns false.
     * 
     * The progress calculation is dynamic and reflects the user's progression through their plan as of the current date.
     * This function is particularly useful for nutritionists looking to monitor the status and progress of their clients'
     * dietary plans.
     */


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
     * @return void Outputs a JSON-encoded array containing a success message and the data array if progress data
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
        JOIN user_plan up ON u.id = up.user_id
        JOIN plans p ON up.plan_id = p.id
        WHERE nc.nutritionist_id = :nutritionist_id";

        $this->db->query($sql);
        $this->db->bind(':nutritionist_id', $nutritionistId);

        $rows = $this->db->resultSet();

        // var_dump($rows); 


        if ($this->db->rowCount() > 0) {
            return $rows;
        } else {
            return false;
        }
    }


}
