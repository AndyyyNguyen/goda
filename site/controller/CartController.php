<?php
class CartController
{
	protected $cartStorage;
	function __construct()
	{
		$this->cartStorage = new CartStorage();
	}
	function display()
	{
		//cart là object
		$cart = $this->cartStorage->fetch();
		echo json_encode($cart->convertToArray());
	}

	function add()
	{
		$product_id = $_GET["product_id"];
		$qty = $_GET["qty"];
		$cart = $this->cartStorage->fetch();

		$cart->addProduct($product_id, $qty);

		$this->cartStorage->store($cart);
		// Đổi đối tượng -> chuỗi
		// Đổi tượng thành array, sau đó từ array -> chuỗi
		$temp = json_encode($cart->convertToArray());
		echo $temp;
	}

	function update()
	{
		$product_id = $_GET["product_id"];
		$qty = $_GET["qty"];
		$cart = $this->cartStorage->fetch();
		//bia (2): 1
		//cập nhật lên 3
		//addProduct(bia, 2);

		//xóa bia
		//addProduct (bia, 3)
		$cart->deleteProduct($product_id);
		$cart->addProduct($product_id, $qty);

		$this->cartStorage->store($cart);

		echo json_encode($cart->convertToArray());
	}

	function delete()
	{
		$product_id = $_GET["product_id"];
		$cart = $this->cartStorage->fetch();

		$cart->deleteProduct($product_id);

		$this->cartStorage->store($cart);

		echo json_encode($cart->convertToArray());
	}

	function test()
	{
		$a = 1;
	}
}
