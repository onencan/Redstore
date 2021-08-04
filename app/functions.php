<?php
function pdo_connect_mysql() {
    // Update the details below with your MySQL details
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'site';
    try {
	return new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
    } catch (PDOException $exception) {
	// If there is an error with the connection, stop the script and display the error.
	die ('Failed to connect to database!');
    }
}
//===========================================================================================================
// Template header, feel free to customize this
function showLogin() {
    $loggedin = isset($_SESSION['user']['id']) ? '<a class="dropdown-item" href="logout.php">Log out</a>':'<a class="dropdown-item" href="login.php">Log In</a><a class="dropdown-item" href="signup.php"> SignUp</a>';
    return $loggedin;
}

//Cart function
function numCart() {
    //Get the amount of items in the shopping carrt this will display in the header
    $num_items_in_cart = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;    
    return $num_items_in_cart;
}

//show admin panel
function admin(){
    $forAdmin = isset($_SESSION['user']['id'])&&isAdmin($_SESSION['user']['id']) ? '<li><a class="nav-link" href="lkjdfam@jodcnaosmmdloisdmnoe8iwejh/panel/">Dashoard</a></li><li><a class="nav-link" href="jhsodnaoiidniaw9daow84woe8wne8ienoh29se32isudnkjsdks/products/">Products</a></li>' :'' ;
    return $forAdmin;
}

//Show signin on cart
function cartReg(){
    $Signin = isset($_SESSION['user']['id']) ? '<button class="btn btn-primary float-md-right" name="placeorder" type="submit"> Make Purchase <i class="fa fa-chevron-right"></i> </button>' : '<button class="btn btn-primary float-md-right" name="signin" type="submit"> Sign In to Continue <i class="fa fa-chevron-right"></i> </button>' ;
    return $Signin;
}

//User's name
function userName() {
    $uName = isset($_SESSION['user']['id']) ? $_SESSION['user']['username'] : '<a href="login.php">Sign In</a> | <small><a href="signup.php"> Register</a></small>';
    return $uName;
}

//========================================================================================
// Template footer
function footer() {
    $year = date('Y');
    return $year;
}

//Function to display stuff on login
function UserLoginOption($logged, $loggedOut){
    $Signin = isset($_SESSION['user']['id']) ? $logged: $loggedOut;
    return $Signin;
}


// Discounts on products
function discount($price, $rrp)
{
    if ($price<$rrp) {
        $dis = (($rrp - $price)/$rrp) * 100;
        return ('-'.round($dis, 0).'%');
    }else {
        return '';
    }    
}

// price formatting function
function f_price($price)
{
    $p = strlen($price);
    if ($p > 12) {
        $fprice = substr($price, 0, -12) . ',' . substr($price, 0, -9) . ',' . substr($price, 0, -6).','.substr($price, -6, -3) .','. substr($price, -3); 
    }elseif ($p > 9) {
		$fprice = substr($price, 0, -9) . ',' . substr($price, 0, -6).','.substr($price, -6, -3) .','. substr($price, -3); 
	}elseif ($p > 6) {
        $fprice = substr($price, 0, -6).','.substr($price, -6, -3) .','. substr($price, -3); 
	}elseif ($p > 3) {
		$fprice = substr($price, 0, -3) . ',' . substr($price, -3);
	}else {
		$fprice = $price;
    }
    
    return $fprice;
}

//IP call function, Its defines in config.php
ip();

?>