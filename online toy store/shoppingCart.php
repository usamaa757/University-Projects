<?php session_start();
include "connectToMysql.php";

if (isset($_POST['subtotal'])) {
$subtot=$_POST['subtotal'];
$_SESSION['subtotal']= $subtot;
header("location: cart.php");

}
//Add Toy
if (isset($_POST['add-to-cart'])) {
	$qty = $_POST['quantity'];
	$toyid = $_POST['toyid'];
	$name = $_POST['toyname'];
	// $categ=$_POST['sercate'];
	$price = $_POST['toyprice'];
	$img = $_POST['toypicture'];
	// $availqty=$_POST['seravailqnty'];
	
	//add item
	$_SESSION['cart1'][$toyid] = array("id" => $toyid, "img" => $img, "nm" => $name, "rate" => $price, "qty" => $qty);
	
	header("location: cart.php");

	exit();
}
//Update Toy
if (isset($_POST['modifyqty'])) {
	foreach ($_SESSION['cart1'] as $id => $val) {
		if (($val['nm']) == ($_POST['toyname'])) {
			$_SESSION['cart1'][$id]['qty'] = $_POST['modifyqty'];
			
			header("location: cart.php");
		}
	}
	exit();
}
//Delete Toy
if (isset($_POST['remove'])) {
	foreach ($_SESSION['cart1'] as $id => $val) {

		$name = $_POST['toyname'];
		if (($val['nm']) == ($name)) {
			unset($_SESSION['cart1'][$id]);
			$_SESSION["cart1"] = array_values($_SESSION["cart1"]);
			
			header("location: cart.php");
		}
	}
}
?>