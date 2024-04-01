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
        try {
            if ($role == "Regular") {
                $sql = "SELECT * FROM nutritionist_client WHERE client_id=:client_id;";
                $this->db->query($sql);
                $this->db->bind(':client_id', $ownID);
                $result = $this->db->single();
            } else if ($role == "Nutritionist") {
                $sql = "SELECT * FROM nutritionist_client WHERE nutritionist_id=:nutri_id;";
                $this->db->query($sql);
                $this->db->bind(':nutri_id', $ownID);
                $result = $this->db->resultSet(true);
            }

            return $result;
        } catch (\PDOException $e) {
            return ['error' => $e->getMessage()];
        }
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

            $sql = "SELECT m.*, u.img AS interlocutor_img, u.fullname AS interlocutor_fullname
            FROM messages m
            JOIN users u ON (
                (m.expediteur_id = :own_id AND m.destinataire_id = :target_id AND u.id = :target_id)
                OR (m.expediteur_id = :target_id AND m.destinataire_id = :own_id AND u.id = :target_id)
            )
            WHERE (m.expediteur_id = :own_id AND m.destinataire_id = :target_id)
                OR (m.expediteur_id = :target_id AND m.destinataire_id = :own_id)
            ORDER BY m.date_envoi;";
            $this->db->query($sql);
            $this->db->bind(':own_id', $ownID);
            $this->db->bind(':target_id', $targetID);
            $result = $this->db->resultSet();
        } else if ($role == "Nutritionist") {

            $sql = "SELECT m.*, u.fullname AS interlocutor_fullname, u.img AS interlocutor_img
        FROM messages m
        JOIN users u ON (m.expediteur_id = u.id OR m.destinataire_id = u.id) AND u.id != :own_id
        WHERE m.expediteur_id = :own_id OR m.destinataire_id = :own_id
        ORDER BY m.date_envoi;";
            $this->db->query($sql);
            $this->db->bind(':own_id', $ownID);
            $result = $this->db->resultSet(true);

            foreach ($result as $row) {
                //    echo ("expediteur: " . $row->expediteur_id . "destinataire: " . $row->destinataire_id . "contenu: " . $row->contenu);
            }
        }
        return $result ?? false;
    }
}
