<?php
session_start();
if (!isset($_SESSION["cus_username"])) {
    header("Location: login.php?error=restrictedAccess");
    
}
?>
<!DOCTYPE HTML>
<html>

    <head>
        <title>Create Customer</title>
        <!-- Latest compiled and minified Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
        <!-- Add icon library -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <style>
            #leftrow {
                width: 25%;

            }

            .container {
                width: 50%;
            }
            .nav {
                padding-left: 30px;
                font-size: 18px;
                font-weight: normal;
                font-family: sans-serif;
            }

            span {
                font-weight: bolder;
                color: white;
            }

            .input-container {
                display: -ms-flexbox;
                /* IE10 */
                display: flex;
                width: 100%;
                margin-bottom: 15px;
            }

            .icon {
                padding: 10px;
                background: dodgerblue;
                color: white;
                min-width: 50px;
                text-align: center;
            }
        </style>
    </head>

    <body>
        <?php
        include 'menu.php';
        ?>

        <div class="container">
            <div class="page-header">
                <h1>Create Customer</h1>
            </div>

            <!-- PHP insert code will be here -->
            <?php
                if ($_POST) {
                    include 'config/database.php';
                    try {
                        if (
                            empty($_POST['cus_username']) ||   empty($_POST['password'])
                            ||  empty($_POST['firstname'])  ||  empty($_POST['lastname'])
                            ||  empty($_POST['gender'])  || empty($_POST['dateofbirth'])
                            ||  empty($_POST['registrationdatetime']) ||  empty($_POST['accountstatus'])
                            ||  empty($_POST['confPass'])
                        ) {
                            throw new Exception("<div class='alert alert-danger'>Please make sure all fields are not empty</div>");
                        }
                        $namelength = strlen($_POST['cus_username']);
                        if ($namelength <= 6) {
                            throw new Exception("<div class='alert alert-danger'>Please make sure your name should be greater than 6 characters</div>");
                        }
                        if ($_POST['password'] != $_POST['confPass']) {
                            throw new Exception("<div class='alert alert-danger'>Please make sure your password same as confirm password</div>");
                        }
                        if (!preg_match('/[A-Za-z]/', $_POST['password']) || !preg_match('/[0-9]/', $_POST['password'])||strlen($_POST["password"]) < 8) {
                            throw new Exception("<div class='alert alert-danger'>Please make sure your password which contain at least one lowercase letter, uppercase letter, numeric digit, and special character in at least 8 characters</div>");
                        }
                        $date1 = "Y";
                        $diff = abs(strtotime($date1) - strtotime($_POST['dateofbirth']));
                        $years = floor($diff / (365 * 60 * 60 * 24));
                        if ($years < 18) {
                            throw new Exception("<div class='alert alert-danger'>Please make sure your ages are 18 years old and above</div>");
                        }
                        // include database connection
                        $target_dir = "img/";
                        $fileToUpload = $_FILES['fileToUpload']['name']; 
                        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);//the name of target file u choose
                        $isUploadOK = TRUE;
                        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                        // Check if image file is a actual image or fake image
                        if(isset($_POST["submit"])&&!empty($_POST["submit"])) {
                            
                            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                         if($check == false) {
                            $isUploadOK = false;
                            echo"<div class='alert alert-danger'>Please make sure File is an image!</div>";  
                          } 
                        }
                        list($width, $height, $type, $attr) = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                        if($width!=$height){
                            $isUploadOK = false;
                            echo"<div class='alert alert-danger'>Please make sure File is in square!</div>";  
                        }
                        if ($_FILES["fileToUpload"]["size"] > 5120000) {
                            echo"<div class='alert alert-danger'>Please make sure File is not larger than 512kb!</div>";
                            $isUploadOK = false;
                        }
                        if ($isUploadOK == false) {
                            echo"<div class='alert alert-danger'>Sorry, your file was not uploaded!</div>";
                            $fileToUpload ="";
                        } 
                        // insert query
                        $query = "INSERT INTO customer SET fileToUpload=:fileToUpload,cus_username=:cus_username,password=:password,confPass=:confPass,firstname=:firstname,lastname=:lastname, gender=:gender,dateofbirth=:dateofbirth,registrationdatetime=:registrationdatetime,
                        accountstatus=:accountstatus";
                        // prepare query for execution
                        $stmt = $con->prepare($query);
                        // posted values
                        
                        $cus_username = $_POST['cus_username'];
                        $password = $_POST['password'];
                        $confPass = $_POST['confPass'];
                        $firstname = $_POST['firstname'];
                        $lastname = $_POST['lastname'];
                        $gender = $_POST['gender'];
                        $dateofbirth = $_POST['dateofbirth'];
                        $registrationdatetime = $_POST['registrationdatetime'];
                        $accountstatus = $_POST['accountstatus'];
                        // bind the parameters
                        $stmt->bindParam(':fileToUpload', $fileToUpload);
                        $stmt->bindParam(':cus_username', $cus_username);
                        $stmt->bindParam(':password', $password);
                        $stmt->bindParam(':confPass', $confPass);
                        $stmt->bindParam(':firstname', $firstname);
                        $stmt->bindParam(':lastname', $lastname);
                        $stmt->bindParam(':gender', $gender);
                        $stmt->bindParam(':dateofbirth', $dateofbirth);
                        $stmt->bindParam(':registrationdatetime', $registrationdatetime);
                        $stmt->bindParam(':accountstatus', $accountstatus);
                        // Execute the query
                        if ($stmt->execute()) {
                            echo "<div class='alert alert-success'>Record was saved.</div>";
                        } else {
                            echo "<div class='alert alert-danger'>Unable to save record.</div>";
                        }
                    }
                    // show error
                    catch (PDOException $exception) {
                        echo "<div class='alert alert-danger'>" . $exception->getMessage() . "</div>";
                    } catch (Exception $exception) {
                        echo "<div class='alert alert-danger'>" . $exception->getMessage() . "</div>";
                    }
                }
            ?>
            <!-- html form here where the product information will be entered -->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"  onsubmit="return validateForm()"enctype="multipart/form-data">

                <table class='table table-hover table-responsive table-bordered'>
                    <tr>
                        <td>Profile Image(*Optional)</td>
                        <td>
                            <input type="file" name="fileToUpload" id="fileToUpload">
                        </td>
                    </tr>
                    <tr>
                        <td id="leftrow">User Name</td>
                        <td>
                            <div class="input-container">
                                <i class="fa fa-user icon"></i>
                                <div class="input-group">
                                    <input type='text' name='cus_username' placeholder="Enter user name " class='form-control' id="cName"/>
                        
                                </div>
                            </div>
                        </td>
                    </tr>

                            <tr>
                                <td>Password</td>
                                <td>
                                    <div class="input-container">
                                        <i class="fa fa-key icon"></i>
                                        <div class="input-group">
                                            <input type='password' name='password' placeholder="Enter password " class='form-control' id="pass"/>
                                
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Confirm Password</td>
                                <td>
                                    <div class="input-container">
                                        <i class="fa fa-key icon"></i>
                                        <div class="input-group">
                                            <input type='password' name='confPass' placeholder="Enter confirm password " class='form-control' id="conPass"/>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>

                                <td>First Name</td>
                                <td>
                                    <input type='text' name='firstname' placeholder="Enter Firstname" class='form-control' id="fname"/>
                                </td>
                                </div>
                            </tr>
                            <tr>
                                <td>Last Name</td>
                                <td>
                                    <div class="input-group">
                                        <input type='text' name='lastname' id="lname" placeholder="Enter Lastname" class='form-control' />
                                    </div>
                                </td>
                            </tr>
                           
                            <tr>
                                <td>Gender</td>
                                <td>
                                    <input type="radio" name="gender" value="male" id="gen1">
                                      <label for="html">Male</label><br>
                                      <input type="radio" name="gender" value="female" id="gen2">
                                      <label for="css">Female</label>
                                </td>
                            </tr>

                            <tr>
                                <td>Date of birth</td>
                                <td>
                                    <div class="input-container">
                                        <i class="fa fa-birthday-cake icon"></i>
                                        <input type='date' name='dateofbirth' class='form-control' id="datbir"/>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Registration Date And Time</td>
                                <td><input type='datetime-local' name='registrationdatetime' class='form-control' id="reDate" /></td>
                            </tr>

                            <tr>
                                <td>Accounts Status</td>
                                <td><input type="radio" name="accountstatus" value="active" id="acc1">
                                      <label for="html">Active</label><br>
                                      <input type="radio" name="accountstatus" value="inactive" id="acc2">
                                      <label for="css">Inactive</label>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <input type='submit' name="submit" value='Save' class='btn btn-primary' />
                                    <a href='customer_read.php' class='btn btn-danger'>View Customer</a>

                                </td>
                            </tr>
                </table>
            </form>
        <?php
        include 'footer.php';
        ?>
        </div>
        <!-- end .container -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
        <script>
            function validateForm() {
                var cName = document.getElementById("cName").value;
                var nameC = /^[a-zA-Z0-9.\-_$@*!]{6,}$/;
                var pass = document.getElementById("pass").value;
                var conPass = document.getElementById("conPass").value;
                var fname = document.getElementById("fname").value;
                var lname = document.getElementById("lname").value;
                var gen1 = document.getElementById("gen1").checked;
                var gen2 = document.getElementById("gen2").checked;
                var datbir = document.getElementById("datbir").value;
                var reDate = document.getElementById("reDate").value;
                var acc1 = document.getElementById("acc1").checked;
                var acc2 = document.getElementById("acc2").checked;
                var passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,}$/;
                
                
                var date1 = new Date().getFullYear();
                var date2 = new Date(datbir);
                var yearsDiff =  date1 - date2.getFullYear();
                var flag = false;
                var msg = "";
                if (cName == ""||pass == "" ||conPass == ""|| fname == ""||lname == "" ||datbir =="" || reDate == "" ||(gen1 == false && gen2 == false)||(acc1 == false && acc2 == false)){ 
                    flag = true;
                msg = msg + "Please make sure all fields are not empty!\r\n";
                }
                else if(cName.length <= 6){
                    flag = true;
                msg = msg + "Please make sure your name should be greater than 6 characters!\r\n";
                }
                else if(pass != conPass){
                    flag = true;
                msg = msg + "Please make sure your password same as confirm password!\r\n";
                }
                else if(!pass.match(passw)){ 
                    flag = true;
                msg = msg + "Please make sure your password which contain at least one lowercase letter, one uppercase letter, one numeric digit, and one special character in at least 8 characters!\r\n";
                }
                else if(yearsDiff < 18){
                    flag = true;
                msg = msg + "Please make sure your ages are 18 years old and above!\r\n";
                }
                
                if (flag == true) {
                alert(msg);
                return false;
                } else {
                return true;
                }
            }
        </script>
    </body>

</html>