<?php

namespace Manger\Controller;

use Manger\Model\CommunicationModel;

/**
 * CommunicationController
 * 
 * Handles communication related matters, such as
 * sending a message in a conversation
 */
class CommunicationController
{
    private $commModel;

    /**
     * Communication Controller constructor.
     */
    public function __construct()
    {
        $this->commModel = new CommunicationModel();
    }
    /**
     * sendMessage
     * 
     * Allow an user to send a message using a POST request
     *
     * @return void
     */
    public function sendMessage()
    {
        $targetID = $_POST['targetID'];
        $contentMessage = $_POST['content'];
        $contentMessage = htmlspecialchars($contentMessage);

        if ($this->commModel->putMessageInDatabase($targetID, $_SESSION['id'], $contentMessage)) {
            echo json_encode(['success' => true, 'ownID' => $_SESSION['id'], 'data' => $contentMessage]);
        } else {
            echo json_encode(['success' => false, 'data' => $contentMessage]);
        }
    }

    /**
     * getDiscussion
     * 
     * Fetch the conversation with the nutritionist using the Model
     *
     * @param string $clientID The id of the client we want our discussion with, if we're nutritionist. Null otherwise.
     * @return void
     */
    public function getDiscussion($clientID = null)
    {
        //header('Content-Type: application/json');
        $ownID = $_SESSION['id'];
        $role = $_SESSION['role'];

        $targetID = $this->commModel->getDiscussionPartner($ownID, $role);

        if ($role == "Regular") {
            if (!isset($targetID->nutritionist_id)) {
                echo json_encode(['success' => true, 'ownID' => $ownID, 'role' => "NoNutritionist"]);
                return;
            } else {
                $nutriID = $targetID->nutritionist_id;
                $arrayMessage = $this->commModel->getDiscussion($ownID, $nutriID, $role);
            }
        } else if ($role == "Nutritionist") {

            $arrayMessage = $this->commModel->getDiscussion($ownID, $clientID, $role);
        }

        //var_dump($arrayMessage);
        if (isset($arrayMessage['error'])) {
            echo json_encode(['success' => false, 'ownID' => $ownID, 'role' => $role, 'data' => $arrayMessage['error'], 'clientID' => $clientID ?? "", 'nutriID' => $nutriID ?? ""]);
        } else if (isset($arrayMessage)) {
            // Gérer la réponse réussie
            echo json_encode(['success' => true, 'ownID' => $ownID, 'role' => $role,  'data' => $arrayMessage]);
        }
    }

    /**
     * getMessagesFromAConvo
     * 
     * Get the discussion between their nutritionist and the regular client calling the method,
     * or if a nutritionist did, get their discussion with the client whose ID is in the GET request
     *
     * @return void
     */
    public function getMessagesFromAConvo()
    {
        if ($_SESSION['role'] == "Regular") {
            $this->getDiscussion();
        } else {
            $clientID = $_GET['receiverId'];
            $this->getDiscussion($clientID);
        }
    }
}
