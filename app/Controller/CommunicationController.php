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
            echo json_encode(['success' => true, 'data' => $contentMessage]);
        } else {
            echo json_encode(['success' => false, 'data' => $contentMessage]);
        }
    }

    /**
     * getDiscussion
     * 
     * Fetch the conversation with the nutritionist using the Model
     *
     * @return void
     */
    public function getDiscussion()
    {
        $ownID = $_SESSION['id'];
        $role = $_SESSION['role'];

        $targetID = $this->commModel->getDiscussionPartner($ownID, $role);

        if ($role == "Regular") {
            $nutriID = $targetID->nutritionist_id;
            $arrayMessage = $this->commModel->getDiscussion($ownID, $nutriID, $role);
        } else if ($role == "Nutritionist") {
            $nutritionistIds = array_map(function ($object) {
                return $object->nutritionist_id;
            }, $targetID);
            $arrayMessage = $this->commModel->getDiscussion($ownID, $nutritionistIds, $role);
        }

        if (!empty($arrayMessage)) {
            echo json_encode(['success' => true, 'data' => $arrayMessage]);
        } else {
            echo json_encode(['success' => false, 'data' => "Couldn't get conversation"]);
        }
    }
}
