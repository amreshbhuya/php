<?php
$servername = "localhost"; // Replace with your server name
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "user_form_data"; // Replace with your database name

// Function to sanitize user inputs
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Function to handle form submission
function handleFormSubmission() {
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve and sanitize form inputs
    $email = filter_var(sanitizeInput($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = password_hash(sanitizeInput($_POST['password']), PASSWORD_DEFAULT); // Hash the password for security
    $gender = sanitizeInput($_POST['gender']);
    $interests = isset($_POST['interest']) ? sanitizeInput(implode(',', (array)$_POST['interest'])) : '';
    $city = sanitizeInput($_POST['city']);
    $address = sanitizeInput($_POST['address']);

    // Validate inputs
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format";
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO user_details (email, password, gender, interests, city, address) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        return "Error preparing statement: " . $conn->error;
    }
    $stmt->bind_param("ssssss", $email, $password, $gender, $interests, $city, $address);

    // Execute the statement
    if ($stmt->execute()) {
        $response = "New record created successfully";
    } else {
        $response = "Error: " . $stmt->error;
    }

    // Close connections
    $stmt->close();
    $conn->close();

    return $response;
}

$responseMessage = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $responseMessage = handleFormSubmission();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Form</title>
</head>
<body>
    <div class="container mt-5">
        <?php if ($responseMessage): ?>
            <div class="alert alert-info"><?php echo $responseMessage; ?></div>
        <?php endif; ?>
        <form method="post" action="submit.php" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <div class="invalid-feedback">
                    Please enter a valid email.
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="invalid-feedback">
                    Please enter your password.
                </div>
            </div>
            <fieldset class="mb-3">
                <legend class="col-form-label pt-0">Gender</legend>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="male" value="male" required>
                    <label class="form-check-label" for="male">Male</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="female" value="female" required>
                    <label class="form-check-label" for="female">Female</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="other" value="other" required>
                    <label class="form-check-label" for="other">Other</label>
                </div>
            </fieldset>
            <fieldset class="mb-3">
                <legend class="col-form-label pt-0">Interests</legend>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="interest[]" id="cricket" value="cricket">
                    <label class="form-check-label" for="cricket">Cricket</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="interest[]" id="football" value="football">
                    <label class="form-check-label" for="football">Football</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="interest[]" id="basketball" value="basketball">
                    <label class="form-check-label" for="basketball">Basketball</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="interest[]" id="chess" value="chess">
                    <label class="form-check-label" for="chess">Chess</label>
                </div>
            </fieldset>
            <div class="mb-3">
                <label for="city" class="form-label">City</label>
                <select class="form-select" id="city" name="city" required>
                    <option value="" disabled selected>Select your city</option>
                    <option value="Kolkata">Kolkata</option>
                    <option value="Delhi">Delhi</option>
                    <option value="Mumbai">Mumbai</option>
                    <option value="Bangalore">Bangalore</option>
                    <option value="Outstation">Outstation</option>
                </select>
                <div class="invalid-feedback">
                    Please select a city.
                </div>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="4" required>Write your address...</textarea>
                <div class="invalid-feedback">
                    Please enter your address.
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        (function () {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
        })();
    </script>
</body>
</html>

