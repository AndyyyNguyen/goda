<?php
class PaymentController
{
    function checkout()
    {
        //Kiểm tra giỏ hàng
        $cartStorage = new CartStorage();
        $cart = $cartStorage->fetch();
        if (empty($cart->getTotalProductNumber())) {
            $_SESSION['error'] = "Giỏ hàng rỗng";
            header("location: /index.php?c=product");
            exit;
        }
        //Hiển thị thông tin đơn hàng
        $email = "khachvanglai@gmail.com";
        if (!empty($_SESSION['email'])) {
            $email = $_SESSION['email'];
        }
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        require ABSPATH_SITE . 'layout/variable_address.php';
        require ABSPATH_SITE . 'view/payment/checkout.php';
    }

    function order()
    {

        //lưu đơn hàng
        $email = "khachvanglai@gmail.com";
        if (!empty($_SESSION['email'])) {
            $email = $_SESSION['email'];
        }
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);

        $provinceRepository = new ProvinceRepository();
        $province = $provinceRepository->find($_POST['province']);
        $shipping_fee = $province->getShippingFee();
        $data = [];
        $data["created_date"] = date('Y-m-d H:i:s');
        $data["order_status_id"] = 1;
        $data["staff_id"] = NULL;
        $data["customer_id"] = $customer->getId();
        $data["shipping_fullname"] = $_POST['fullname'];
        $data["shipping_mobile"]  = $_POST['mobile'];
        $data["payment_method"]  = $_POST['payment_method'];
        $data["shipping_ward_id"]  = $_POST['ward'];
        $data["shipping_housenumber_street"]  = $_POST['address'];
        $data["shipping_fee"]  = $shipping_fee;
        $data["delivered_date"]  = date('Y-m-d H:i:s', strtotime('+3 days'));

        $orderRepository = new OrderRepository();
        if (!$order_id = $orderRepository->save($data)) {
            $_SESSION['error'] = 'Đơn hàng bị lỗi';
            header('location: index.php');
            exit;
        }

        $orderItemRepository = new OrderItemRepository();
        $cartStorage = new CartStorage();
        $cart = $cartStorage->fetch();
        foreach ($cart->getItems() as $item) {
            $dataItem = [];
            $dataItem['product_id'] = $item['product_id'];
            $dataItem['order_id'] = $order_id;
            $dataItem['unit_price'] = $item['unit_price'];
            $dataItem['qty'] = $item['qty'];
            $dataItem['total_price'] = $item['total_price'];
            $orderItemRepository->save($dataItem);
        }
        $cartStorage->clear();
        $_SESSION['success'] = 'Đơn hàng đã tạo thành công';
        header('location:/');
    }
}
