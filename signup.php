<?php
require("Database.php");
session_start();
// https://board.phpbuilder.com/d/10312177-php-to-take-action-upon-button-click
if (!isset ($_SESSION["profile_name"])) { // check if session variable exists
    $_SESSION["profile_name"] = ""; // create the session variable
}

if (!isset ($_SESSION["email"])) { // check if session variable exists
    $_SESSION["email"]= ""; // create the session variable
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="description" content="Web application development" />
        <meta name="keywords" content="Assignment1" />
        <meta name="author" content="Dai Trung Duong Luong" />
        <link href="library/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
        <link href="library/bootstrap/css/bootstrap-grid.min.css" rel="stylesheet" />
        <link href="css/custom.css" rel="stylesheet" />
        <title>Assignment 2</title>
    </head>
    <body>
        <div class="container">
            <div class="bg-white text-dark rounded mt-5 p-3">
                <div class="row">
                    <div class="col-12 text-center">
                        <h1>My Friend System</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center">
                        <h1>Registration Page</h1>
                    </div>
                </div>


                <?php
                // if the form reset, delete all session variable to make input fields clear
                if (isset($_POST["reset"]))
                {
                    $_SESSION = array(); // unset all session variables
                    session_destroy();
                    echo "<meta http-equiv='refresh' content='0'>";
                }

                // if the form submit
                if ($_POST['submit']){
                    $allFieldsMeetRequirements = true;
                    // pattern for input form
                    $emailPattern = "/\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6}/";
                    $passwordPattern = "/[A-Za-z0-9]/";
                    $profileNamePattern = "/^[a-zA-z]+$/";
                    $isSignUpSuccessful = false;

                    // check if form data exists
                    $data_exist = isset($_POST["profile_name"]) && isset($_POST["password"] ) && isset($_POST["repassword"]) && isset($_POST["email"]);
                    $data_not_null = !empty($_POST["email"]) && !empty($_POST["profile_name"]) && !empty($_POST["password"]) && !empty($_POST["repassword"]);


                    // check it mandatory field is not NULL
                    if ($data_not_null && $data_exist){
                        $_SESSION["profile_name"] = $_POST["profile_name"];
                        $_SESSION["email"] = $_POST["email"];
                        $_SESSION["password"] = $_POST["password"];

                        $profile_name = $_SESSION["profile_name"];
                        $password = $_SESSION["password"];
                        $email = $_SESSION["email"];

                        // check if email match to regular expression
                        if (!preg_match($emailPattern, $_POST["email"])){
                            echo "<div class='row'><div class='col-12 text-danger text-center'><p>ERROR: Email must be a valid email format!!!</p></div></div>";
                            $allFieldsMeetRequirements = false;
                        }

                        // check if profile name match to regular expression
                        if (!preg_match($profileNamePattern, $_POST["profile_name"])){
                            echo "<div class='row'><div class='col-12 text-danger text-center'><p>ERROR: Profile must contain only valid letters and cannot be blank!!!</p></div></div>";
                            $allFieldsMeetRequirements = false;
                        }

                        // check if password match to regular expression
                        if (!preg_match($passwordPattern, $_POST["password"])){
                            echo "<div class='row'><div class='col-12 text-danger text-center'><p>ERROR: Password must contain only letters and numbers!!!</p></div></div>";
                            $allFieldsMeetRequirements = false;
                        }

                        if ($_POST["password"] != $_POST["repassword"]){
                            echo "<div class='row'><div class='col-12 text-danger text-center'><p>ERROR: Password does not not match confirm password!!!</p></div></div>";
                            $allFieldsMeetRequirements = false;
                        }

                        if ($allFieldsMeetRequirements){
                            $pdo = new Database();
                            $friendTableName = "friends";
                            $myfriendsTableName = "myfriends";

                            if ($pdo->tableExists($friendTableName)){
                                $queryCheckDuplicatedEmail = "SELECT * FROM friends WHERE friend_email= :friend_email";
                                $pdo->query($queryCheckDuplicatedEmail);
                                $pdo->bind(':friend_email', $email);
                                $resultQueryCheckDuplicatedEmail= $pdo->resultset();

                                print_r($resultQueryCheckDuplicatedEmail);
                                echo count($resultQueryCheckDuplicatedEmail);
                                if (count($resultQueryCheckDuplicatedEmail)!=0){
                                    echo "<div class='row'><div class='col-12 text-danger text-center'><p>Email $email already existed!!!</p></div></div>";
                               } else {
                                    $queryFriendsTableInsert = "INSERT INTO friends (friend_email, password, profile_name, date_started, num_of_friends) VALUES
(:friend_email, :password, :profile_name, :date_started, :num_of_friends)";
                                    $pdo->query($queryFriendsTableInsert);
                                    $pdo->bind(':friend_email', $email);
                                    $pdo->bind(':password', $password);
                                    $pdo->bind(':profile_name', $profile_name);
                                    $pdo->bind(':date_started', date("Y-m-d"));
                                    $pdo->bind(':num_of_friends', 0);
                                    $pdo->execute();

                                   $isLogInSuccessful = true;
                                }
                            } else {
                                echo "<div class='row'><div class='col-12 text-danger text-center'><p>Friends Table is not exist!!!</p></div></div>";
                            }

                            if ($isLogInSuccessful){
                                $_SESSION["isLogInSuccessful"]=true;
                                header('Location: friendadd.php');
                            }
                        }


                    } else {
                        echo "<div class='row'><div class='col-12 text-danger text-center'><p>ERROR: Please input all fields in the form!!!</p></div></div>";
                    }
                }
                ?>

                <form action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> " method ="post">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="email">Email: </label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="email" id="email" maxlength="50" value = "<?php
                            if (isset ($_SESSION["email"])) { // check if session variable exists
                                echo $_SESSION["email"]; // create the session variable
                            } else {
                                echo "";
                            }
                            ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="profile_name">Profile Name: </label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="profile_name" id="profile_name" value = "<?php
                            if (isset ($_SESSION["profile_name"])) { // check if session variable exists
                                echo $_SESSION["profile_name"]; // create the session variable
                            } else {
                                echo "";
                            }
                            ?>"  maxlength="50">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="password">Password: </label>
                        <div class="col-md-9">
                            <input type="password" class="form-control" name="password" id="password" maxlength="50"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="repassword">Confirm Password: </label>
                        <div class="col-md-9">
                            <input type="password" class="form-control" name="repassword" id="repassword" maxlength="50"/>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-9">
                            <input type="submit" name="submit" value="Register" class="btn btn-success mx-1"/>
                            <input type="submit" name="reset" value="Clear" class="btn btn-secondary mx-1"/>
                        </div>
                    </div>
                </form>

                <div class="row">
                    <div class="col-12 text-center">
                        <p><a href="index.php">Home</a></p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
