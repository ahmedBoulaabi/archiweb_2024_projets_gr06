<?php

namespace Manger\Controller;

use Manger\Model\CommunicationModel;

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
}
