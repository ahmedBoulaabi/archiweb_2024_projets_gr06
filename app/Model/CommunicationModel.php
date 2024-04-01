<?php

namespace Manger\Model;

use Config\Database;

class CommunicationModel
{

    /**
     * @var Database The database instance.
     */
    private $db;

    /**
     * CommunicationModel constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
    }

    public function putMessageInDatabase($targetID, $ownID, $textContent)
    {
        $addQuery = "INSERT INTO messages (`destinataire_id`,`expediteur_id`,`contenu`, `etat`) VALUES (:destinataire_id,:expediteur_id,:texte,0)";
        $this->db->query($addQuery);
        $this->db->bind(':destinataire_id', $targetID);
        $this->db->bind(':expediteur_id', $ownID);
        $this->db->bind(':texte', $textContent);
        try {
            $this->db->execute();
            return true;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }


    /**
     * getDiscussionPartner
     *
     * Retrieve the nutritionist affiliated to a client, or the clients as an array
     * affiliated to a nutritionist
     * 
     * @param  mixed $ownID
     * @param  mixed $role
     * @return array|bool|object
     */
    public function getDiscussionPartner($ownID, $role)
    {
        if ($role == "Regular") {
            $sql = "SELECT * FROM nutritionist_client WHERE client_id=:client_id;";
            $this->db->query($sql);
            $this->db->bind(':client_id', $ownID);
            $result = $this->db->single();
        } else if ($role == "Nutritionist") {
            $sql = "SELECT * FROM nutritionist_client WHERE nutritionist=:nutri_id;";
            $this->db->query($sql);
            $this->db->bind(':nutri_id', $ownID);
            $result = $this->db->resultSet();
        }
        return $result ?? false;
    }

    /**
     * getDiscussion
     * 
     * Returns all messages with the nutritionist if called by a client,
     * or all discussions of a nutritionist if called by that nutritionist
     *
     * @param  mixed $ownID
     * @param  mixed $targetID
     * @param  mixed $role
     * @return array|bool
     */
    public function getDiscussion($ownID, $targetID, $role)
    {
        if ($role == "Regular") {
            $sql = "SELECT * FROM messages WHERE (expediteur_id=:own_id AND destinataire_id=:target_id) 
            OR (expediteur_id=:target_id AND destinataire_id=:own_id) ORDER BY date_envoi;";
            $this->db->query($sql);
            $this->db->bind(':own_id', $ownID);
            $this->db->bind(':target_id', $targetID);
            $result = $this->db->resultSet();
        } else if ($role == "Nutritionist") {
            // Construction des placeholders
            $targetPlaceholders = implode(',', array_map(function ($i) {
                return ":targetId$i";
            }, range(1, count($targetID))));

            // Modification de la requête SQL pour utiliser l'opérateur IN
            $sql = "SELECT * FROM messages WHERE (expediteur_id=:own_id AND destinataire_id IN ($targetPlaceholders)) 
        OR (expediteur_id IN ($targetPlaceholders) AND destinataire_id=:own_id) ORDER BY date_envoi;";
            $this->db->query($sql);

            // Liaison du paramètre own_id
            $this->db->bind(':own_id', $ownID);

            // Liaison des IDs de targetID à leurs placeholders
            foreach ($targetID as $index => $id) {
                $this->db->bind(":targetId" . ($index + 1), $id);
            }

            // Exécution de la requête et récupération des résultats
            $result = $this->db->resultSet();
        }


        return $result ?? false;
    }
}
