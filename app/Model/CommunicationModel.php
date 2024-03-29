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
}
