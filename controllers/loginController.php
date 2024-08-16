<?php
// loginController.php
require_once('loginModel.php');

class LoginController {
    public function authenticate($username, $password) {
        $model = new LoginModel();
        return $model->validateCredentials($username, $password);
    }
}
?>
