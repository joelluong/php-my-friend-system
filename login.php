<?php
require("Database.php");
session_start();
if (!isset ($_SESSION["email"])) { // check if session variable exists
    $_SESSION["email"]= ""; // create the session variable
}

if (!isset($_SESSION["isLogInSuccessful"])){  // check if session variable exists
    $_SESSION["isLogInSuccessful"] = false; // create the session variable
}
$isLogInSuccessful = $_SESSION["isLogInSuccessful"];
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="description" content="Web application development" />
        <meta name="keywords" content="Assignment2" />
        <meta name="author" content="Dai Trung Duong Luong" />
        <title>Assignment 2</title>
        <link href="library/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
        <link href="library/bootstrap/css/bootstrap-grid.min.css" rel="stylesheet" />
        <link href="css/custom.css" rel="stylesheet" />
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
                        <h1>Log in Page</h1>
                    </div>
                </div>

                <?php
                // if the form reset, delete all session variable and refresh to make input fields clear
                if (isset($_POST["reset"]))
                {
                    $_SESSION = array(); // unset all session variables
                    session_unset();
                    session_destroy();
                    echo "<meta http-equiv='refresh' content='0'>";
                }

                // if the form submit
                if (isset($_POST['submit'])){
                    // check if form data exists
                    $data_exist = isset($_POST["email"]) && isset($_POST["password"] );
                    $data_not_null = !empty($_POST["email"]) && !empty($_POST["password"]);

                    // check if mandatory fields are not NULL
                    if ($data_not_null && $data_exist){
                        $email = $_POST["email"];
                        $password = $_POST["password"];
                        $_SESSION["email"] = $email;

                        $pdo = new Database(); // call php data object
                        $friendTableName = "friends";
                        $myfriendsTableName = "myfriends";

                        if ($pdo->tableExists($friendTableName)){ // check if friends table exist or not
                            $querySQL = "SELECT password FROM friends WHERE friend_email=:friend_email";
                            $pdo->query($querySQL);
                            $pdo->bind(':friend_email', $email);

                            $rows =  $pdo->resultset(); // only one record is received
                            if (count($rows)!=0){
                                if ($rows[0]['password'] == $password){
                                    $isLogInSuccessful = true;
                                } else {
                                    echo "<div class='row'><div class='col-12 text-danger text-center'><p>Password or email is wrong!!!</p></div></div>";
                                }
                            } else {
                                echo "<div class='row'><div class='col-12 text-danger text-center'><p>Email $email is not existed!!!</p></div></div>";
                            }
                        } else {
                            echo "<div class='row'><div class='col-12 text-danger text-center'><p>Friends Table is not exist, please go to the home page!!!</p></div></div>";
                        }

                        if ($isLogInSuccessful){ // if log in is successful
                            $_SESSION["email"] = $email; // send session variable to friendlist.php
                            $_SESSION["isLogInSuccessful"] = $isLogInSuccessful; // send session variable to friendlist.php
                            header('Location: friendlist.php');
                        }

                    } else{
                        echo "<div class='row'><div class='col-12 text-danger text-center'><p>ERROR: Please input all fields in the form!!!</p></div></div>";
                    }
                }

                ?>

                <form action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method ="post">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="email">Email </label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="email" id="email" maxlength="50" value = "<?php
                            echo $_SESSION["email"];
                            ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="password">Password: </label>
                        <div class="col-md-9">
                            <input type="password" class="form-control" name="password" id="password" maxlength="50"/>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-9">
                            <input type="submit" name="submit" value="Log in" class="btn btn-success mx-1"/>
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