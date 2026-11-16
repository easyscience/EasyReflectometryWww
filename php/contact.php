<?php

############################
# Only process POST reqeusts
############################

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  ############################################
  # BEGIN Setting reCaptcha v3 validation data
  ############################################

  $secret_key = "6LfjgU8bAAAAAHvLKRGg4A2-RRToXGzrmP_zyTWC";

  $url = "https://www.google.com/recaptcha/api/siteverify";

  $data = [
    'secret' => $secret_key,
    'response' => $_POST['token'],
    'remoteip' => $_SERVER['REMOTE_ADDR']
  ];

  $options = array(
    'http' => array(
      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
      'method'  => 'POST',
      'content' => http_build_query($data)
    )
  );

  # Creates and returns stream context with options supplied in options preset
  $context  = stream_context_create($options);

  # file_get_contents() is the preferred way to read the contents of a file into a string
  $response = file_get_contents($url, false, $context);

  # Takes a JSON encoded string and converts it into a PHP variable
  $res = json_decode($response, true);

  ##########################################
  # END setting reCaptcha v3 validation data
  ##########################################

  # Post form OR output alert and bypass post if false. NOTE: score conditional is optional
  # since the successful score default is set at >= 0.5 by Google. Some developers want to
  # be able to control score result conditions, so I included that in this example.

  if ($res['success'] == true && $res['score'] >= 0.5) {

    # Recipient email
    $mail_to = "support@easyreflectometry.org";

    # Sender form data
    $name = str_replace(array("\r","\n"), array(" "," "), strip_tags(trim($_POST["name"])));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);

    # Check that all required fields are not empty
    if (empty($name) OR empty($message) OR empty($subject) OR !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      # Set a 400 (bad request) response code and exit
      http_response_code(400);
      echo '<script>alert("Please complete the form and try again.")</script>';
      exit;
    }

    # Mail content
    $content = "Name: $name\n";
    $content .= "Email: $email\n\n";
    $content .= "$message\n";

    # Email headers
    $headers = "From: $name <$email>";

    # Send the email
    $success = mail($mail_to, $subject, $content, $headers);

    if ($success) {
      # Set a 200 (okay) response code
      http_response_code(200);
      echo '<script>alert("Thank You! Your message has been successfully sent.")</script>';
    } else {
      # Set a 500 (internal server error) response code
      http_response_code(500);
      echo '<script>alert("Something went wrong, your message could not be sent.")</script>';
    }

  } else {
    echo '<script>alert("Error! The security token has expired or you are a bot.")</script>';
  }

#########################################################
# Not a POST request, set a 403 (forbidden) response code
#########################################################

} else {
  http_response_code(403);
  echo '<script>alert("There was a problem with your submission, please try again.")</script>';
}

# Go back to contact form and clear it
echo '<script>history.go(-1)</script>';

?>
