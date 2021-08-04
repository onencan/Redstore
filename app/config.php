<?php 

session_start(); // start session
  // connect to database
  $conn = new mysqli("localhost", "root", "", "site");
  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
  // define global constants
  define ('ROOT_PATH', realpath(dirname(__FILE__))); // path to the root folder
  define ('INCLUDE_PATH', realpath(dirname(__FILE__) . '/includes' )); // Path to includes folder
  define('BASE_URL', 'http://localhost/dantty/'); // the home url of the website
  
  $favicon = 'fun_icon.png'; // The tab icon
  
  // The banner images
  $banner1 = 'slide1.jpg';
  $banner2 = 'slide2.jpg';
  $banner3 = 'slide3.jpg';
  $banner4 = 'slide4.jpg';
  $banner5 = 'slide5.png';

  $b_arr = [$banner1, $banner2, $banner3, $banner4, $banner5];
  shuffle($b_arr);

  require_once(INCLUDE_PATH. "/logic/authCookieSessionValidate.php");

function getMultipleRecords($sql, $types = null, $params = []) {
  global $conn;
  $stmt = $conn->prepare($sql);
  if (!empty($params) && !empty($params)) { // parameters must exist before you call bind_param() method
    $stmt->bind_param($types, ...$params);
  }
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
  return $user;
}
function getSingleRecord($sql, $types, $params) {
  global $conn;
  $stmt = $conn->prepare($sql);
  $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();
  $stmt->close();
  return $user;
}
function modifyRecord($sql, $types, $params) {
  global $conn;
  $stmt = $conn->prepare($sql);
  $stmt->bind_param($types, ...$params);
  $result = $stmt->execute();
  $stmt->close();
  return $result;
}

// get the ip address
$ipAddress = $_SERVER['REMOTE_ADDR'];

// create visitor profile in the database for reference issues
function add_visitor($visitor_ip)
{
  global $conn;
  $query = 'INSERT INTO visitors (visitor_ip) VALUES (?)';
  $stmt = $conn->prepare($query);
  $stmt->bind_param('s', $visitor_ip);
  $stmt->execute();
  $stmt->close();
}

// update the visitor profile when the user is logged in with the same ip --- include the user using the IP
function addUserToVisitTable($visitor_ip, $user)
{
  global $conn;
  $query = 'INSERT INTO visitors (visitor_ip, user_id) VALUES (?,?)';
  $stmt = $conn->prepare($query);
  $stmt->bind_param('si', $visitor_ip, $user);
  $stmt->execute();
  $stmt->close();
}

// add searches
function addSearches($word, $results)
{
  global $conn;
  $visitor_id = visitor_id();
  $query = 'INSERT INTO search (word, visitor_id, resultNUM) VALUES (?,?,?)';
  $stmt = $conn->prepare($query);
  $stmt->bind_param('sii', $word, $visitor_id, $results);
  $stmt->execute();
  $stmt->close();
}

// add recently visited products
function RecentClickedProducts($prod_id)
{
  global $conn;
  $v_id = visitor_id();
  $query = 'INSERT INTO recent (prod_id, visitor_id) VALUES (?,?)';
  $stmt = $conn->prepare($query);
  $stmt->bind_param('ii', $prod_id, $v_id);
  $stmt->execute();
  $stmt->close();
}

// Retrieve recent clicks
function RecentClicks()
{
  global $conn;
  $v_id = visitor_id();
  $query = 'SELECT * from recent where visitor_id = "'.$v_id.'" ORDER BY id DESC LIMIT 4';
  $checkdb = mysqli_query($conn, $query);
  $numR = mysqli_num_rows($checkdb);
  return $numR;
}


// the logic for ip addresses
// ip magic works here
function ip()
{
  global $ipAddress, $conn;
  // check the database for the current ip
  $ipq1 = 'SELECT * from visitors where visitor_ip="'.$ipAddress.'" AND user_id ="0"';
  $ipdb = mysqli_query($conn, $ipq1);
  $ipresults = mysqli_num_rows($ipdb);
  
  if ($ipresults < 1) {// create a new visitor
    add_visitor($ipAddress);
  }else{
    // check if he is the registred visitor, 
    if (isset($_SESSION['user']['id'])) {
      $user = $_SESSION['user']['id'];

      $ipdb2 = mysqli_query($conn, 'SELECT * from visitors where visitor_ip="'.$ipAddress.'" AND user_id ="'.$user.'"');
      $ip2results = mysqli_num_rows($ipdb2);

      if ($ip2results < 1) {
        // create a new visitor
        addUserToVisitTable($ipAddress, $user);
      }
    }
  }
}

// Get current visitor_id
function visitor_id()
{
  global $ipAddress, $conn;
  if (isset($_SESSION['user']['id'])) {
    $user = $_SESSION['user']['id'];
    $vsql = 'SELECT * from visitors where visitor_ip="'.$ipAddress.'" AND user_id ="'.$user.
    '" ORDER BY id DESC LIMIT 1';
    $ipdb3 = mysqli_query($conn, $vsql);
    $id = mysqli_fetch_assoc($ipdb3);
    return $id['id'];
  } else{
    $vsql = 'SELECT * from visitors where visitor_ip="'.$ipAddress.
    '" AND user_id ="0" ORDER BY id DESC LIMIT 1';
    $ipdb3 = mysqli_query($conn, $vsql);
    $id = mysqli_fetch_assoc($ipdb3);
    return $id['id'];
  }
  

}

// get the last URL
function URL()
{
  $url =$_SERVER['REQUEST_URI'];
  return $url;
}

// finds mobile devices
function mobile_redirect()
{
  $mobi = strstr($_SERVER['HTTP_USER_AGENT'], "Mobile");
  if($mobi){$read = true;}else{ $read = false;}
  if ($read) {
    header('location: mobi/');
  }
}

// Function for getting currencies
function currency($cur)
{
    $currency = $cur;
    $sql = 'SELECT * FROM `countries` WHERE currency = ?';
    $exR = getSingleRecord($sql, 's', [$currency]);
    return $exR['UG_rate'];
}

// function that fetches the user details
function userDetails($user_id)
{
    $sql = 'SELECT * FROM `countries` WHERE id = ?';
    $user = getSingleRecord($sql, 's', [$user_id]);
    return $user;
}

// fuction that restricts user access when not logged in
function logRestrict()
{
  if (isset($_SESSION['user'])) {
    # code...
  }else {
    header('Location: ./');
  }
}
?>