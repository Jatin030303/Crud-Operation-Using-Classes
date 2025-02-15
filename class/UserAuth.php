<?php

class UserAuth       // Class NAme
{
    public $name, $email, $password, $c_password, $con;  //Properties
    private $error = [];
    private $isValid = true;
    private $alertMessage = '';
    public $redirectUser = false;
    public $redirectAdmin = false;
    private $redirect = false;
    public $userType = 'user';

    public function __construct($con)   //Construct
    {
        $this->con = $con;
    }
    //Method
    public function login($postData)  // Login Method 
    {
        $this->email = $postData['email'] ?? '';
        $this->password = $postData['password'] ?? '';
        $this->validateEmail();
        $this->validatePassword();
        if ($this->isValid) {
            $this->authenticate();
        }
    }

    public function signUp($postData)  // Sign-up value
    {
        $this->name = $postData['name'] ?? '';
        $this->email = $postData['email'] ?? '';
        $this->password = $postData['password'] ?? '';
        $this->c_password = $postData['confirm'] ?? '';

        $this->validateName();                    //Calling function
        $this->validateEmail();
        $this->validatePassword();
        $this->validateConfirmPassword();

        if ($this->isValid) {
            $this->checkUserExists();
        }
    }
    //Validation 

    private function validateName()
    {
        if (empty($this->name)) {
            $this->error['name'] = 'Name field cannot be empty';
            $this->isValid = false;
        } elseif (!preg_match("/^[A-Za-z ]+$/", $this->name)) {
            $this->error['name'] = 'Please enter a valid name';
            $this->isValid = false;
        }
    }

    private function validateEmail()
    {
        if (empty($this->email)) {
            $this->error['email'] = 'Email cannot be empty';
            $this->isValid = false;
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->error['email'] = 'Please enter a valid email';
            $this->isValid = false;
        }
    }

    private function validatePassword()
    {
        if (empty($this->password)) {
            $this->error['password'] = 'Password field cannot be empty';
            $this->isValid = false;
        } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,15}$/", $this->password)) {
            $this->error['password'] = 'Enter a valid password format';
            $this->isValid = false;
        }
    }

    private function validateConfirmPassword()
    {
        if (empty($this->c_password)) {
            $this->error['confirm'] = 'Confirm password cannot be empty';
            $this->isValid = false;
        } elseif ($this->c_password !== $this->password) {
            $this->error['confirm'] = 'Password and confirm password do not match';
            $this->isValid = false;
        }
    }

    // Check user already exist

    private function checkUserExists()
    {
        $sql = "SELECT * FROM `users` WHERE email ='{$this->email}'";
        $result = mysqli_query($this->con, $sql);
        if ($result) {
            $num = mysqli_num_rows($result);
            if ($num > 0) {
                $this->alertMessage = 'User already has an account. Please login';
            } else {
                $this->createUser(); // Create a new account calling function
            }
        } else {
            die(mysqli_error($this->con));
        }
    }

    private function createUser()           //Create a new account
    {
        $hash = password_hash($this->password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO `users` (name, email, password, type) VALUES ('{$this->name}', '{$this->email}', '{$hash}', '{$this->userType}')";
        $result = mysqli_query($this->con, $sql);

        if ($result) {
            $this->alertMessage = 'Account created successfully';
            $this->redirect = true;
        } else {
            $this->alertMessage = 'Error creating account';
        }
    }
    // For login Account
    private function authenticate()
    {
        $sql = "SELECT * FROM `users` WHERE email = '{$this->email}'";
        $result = mysqli_query($this->con, $sql);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $hashedPassword = $row['password'];
            $userType = $row['type'];
            //Convert encrypted password to decrypted 
            if (!password_verify($this->password, $hashedPassword)) {
                $this->alertMessage = 'Incorrect credential';
            } else {
                $_SESSION['email'] = $this->email;
                $_SESSION['type'] = $userType;

                // Redirect based on user type
                if ($userType === 'admin') {
                    $this->redirectAdmin = true;
                } elseif ($userType === 'user') {
                    $this->redirectUser = true;
                }
            }
        } else {
            $this->alertMessage = 'User needs to register first';
        }
    }
    public function getErrors()
    {
        return $this->error;
    }

    public function getAlertMessage()
    {
        return $this->alertMessage;
    }

    public function shouldRedirect()
    {
        return $this->redirect;
    }
    public function shouldRedirectAdmin()
    {
        return $this->redirectAdmin;
    }
    public function shouldRedirectUser()
    {
        return $this->redirectUser;
    }
}

$user = new UserAuth($db->con); //Object created
