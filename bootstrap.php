<?php
require_once ABSPATH . "model/base/BaseRepository.php";
require_once ABSPATH . "model/action/Action.php";
require_once ABSPATH . "model/action/ActionRepository.php";
require_once ABSPATH . "model/category/Category.php";
require_once ABSPATH . "model/category/CategoryRepository.php";
require_once ABSPATH . "model/brand/Brand.php";
require_once ABSPATH . "model/brand/BrandRepository.php";
require_once ABSPATH . "model/comment/Comment.php";
require_once ABSPATH . "model/comment/CommentRepository.php";
require_once ABSPATH . "model/customer/Customer.php";
require_once ABSPATH . "model/customer/CustomerRepository.php";
require_once ABSPATH . "model/district/District.php";
require_once ABSPATH . "model/district/DistrictRepository.php";
require_once ABSPATH . "model/order/Order.php";
require_once ABSPATH . "model/order/OrderRepository.php";
require_once ABSPATH . "model/status/Status.php";
require_once ABSPATH . "model/status/StatusRepository.php";
require_once ABSPATH . "model/orderItem/OrderItem.php";
require_once ABSPATH . "model/orderItem/OrderItemRepository.php";
require_once ABSPATH . "model/product/Product.php";
require_once ABSPATH . "model/product/ProductRepository.php";
require_once ABSPATH . "model/brand/Brand.php";
require_once ABSPATH . "model/brand/BrandRepository.php";
require_once ABSPATH . "model/imageItem/ImageItem.php";
require_once ABSPATH . "model/imageItem/ImageItemRepository.php";
require_once ABSPATH . "model/province/Province.php";
require_once ABSPATH . "model/province/ProvinceRepository.php";
require_once ABSPATH . "model/role/Role.php";
require_once ABSPATH . "model/role/RoleRepository.php";
require_once ABSPATH . "model/roleAction/RoleAction.php";
require_once ABSPATH . "model/roleAction/RoleActionRepository.php";
require_once ABSPATH . "model/staff/Staff.php";
require_once ABSPATH . "model/staff/StaffRepository.php";
require_once ABSPATH . "model/transport/Transport.php";
require_once ABSPATH . "model/transport/TransportRepository.php";
require_once ABSPATH . "model/ward/Ward.php";
require_once ABSPATH . "model/ward/WardRepository.php";
require_once ABSPATH . "model/cart/Cart.php";
require_once ABSPATH . "model/cart/CartStorage.php";
require_once ABSPATH . "model/newsletter/NewsLetter.php";
require_once ABSPATH . "model/newsletter/NewsLetterRepository.php";

require_once ABSPATH . "service/EmailService.php";

//godashop.com
function get_host_name()
{
    return $_SERVER['HTTP_HOST'];
}
//http://
function getProtocol()
{
    $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    return $protocol;
}

//http://godashop.com
function get_domain()
{
    $protocol = getProtocol();
    return $protocol . $_SERVER['HTTP_HOST'];
}
//http://godashop.com/site
function get_domain_site()
{
    return get_domain() . "/site";
}
