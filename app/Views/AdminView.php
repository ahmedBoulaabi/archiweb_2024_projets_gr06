<?php

namespace Manger\Views;

/**
 * Access the PHP files to display
 *
 * Find the PHP files to display so the Controller can display them.
 *
 */
class AdminView
{

    /**
     * Send the requested Admin page to the Controller.
     *
     * Initiates output buffering, includes the specified page template, and returns the captured content.
     * If the specified template file does not exist, the *login.php* template is included by default.
     *
     * @param mixed $page The page to be displayed.
     * @return string The captured content from the output buffer.
     */
    public function viewAdminPage($page)
    {
        startStream();

        $filePath = TEMPLATESDIR . DS . 'admin' . DS . $page . '.php';

        if (file_exists($filePath)) {
            include $filePath;
        } else {
            include TEMPLATESDIR . DS . 'user' . DS . 'login.php'; // A modifier
        }

        return endStream();
    }

    public function renderUserTable($data){
        require VIEWSDIR . DS . 'components' . DS . 'admin' . DS . 'users-table.php';
    }

    
}
