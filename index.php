<!DOCTYPE html>
<html>
<head>
  <title>Feedback Form</title>
</head>
<body>
  <h2>Submit Feedback</h2>
  <form method="POST" action="">
    Name: <input type="text" name="name"><br><br>
    Feedback: <textarea name="feedback"></textarea><br><br>
    <input type="submit" value="Submit">
  </form>

  <?php
  $servername = getenv('DB_HOST');
  $username = getenv('DB_USER');
  $password = getenv('DB_PASS');
  $dbname = getenv('DB_NAME');

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $feedback = $_POST["feedback"];

    $sql = "INSERT INTO feedback (name, feedback) VALUES ('$name', '$feedback')";
    if ($conn->query($sql) === TRUE) {
      echo "Feedback submitted successfully!";
    } else {
      echo "Error: " . $conn->error;
    }
  }

  echo "<h3>All Feedback:</h3>";
  $result = $conn->query("SELECT * FROM feedback");
  while($row = $result->fetch_assoc()) {
    echo "<b>" . $row["name"] . ":</b> " . $row["feedback"] . "<br>";
  }

  $conn->close();
  ?>
</body>
</html>
