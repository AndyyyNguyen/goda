function openMenuMobile() {
    $(".menu-mb").width("250px");
    $(".btn-menu-mb").hide("slow");
}

function closeMenuMobile() {
    $(".menu-mb").width(0);
    $(".btn-menu-mb").show("slow");
}

// Toàn bộ code html tải từ đầu đến cuối trang xong thì code trong $(function(){...}) mới chạy
$(function () {

    //Validate register form

    $(".form-register, .reset-password").validate({
        rules: {
            // simple rule, converted to {required:true}
            fullname: {
                required: true,
                maxlength: 50,
                regex:
                    /^[a-zA￾ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+$/i,
            },
            mobile: {
                required: true,
                regex: /^0([0-9]{9,9})$/,
            },

            email: {
                required: true,
                maxlength: 50,
                email: true,
                // server trả về false là lỗi, true là không lỗi
                remote: "/index.php?c=customer&a=notExistingEmail"
            },

            password: {
                required: true,
                regex: /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/
            },

            password_confirmation: {
                required: true,
                equalTo: "[name=password]"
            },

            hiddenRecaptcha: {
                //true: lỗi
                //false: passed
                required: function () {
                    if (grecaptcha.getResponse() == '') {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        },

        messages: {
            fullname: {
                required: "Vui lòng nhập họ và tên",
                maxlength: "Vui lòng nhập không quá 50 ký tự",
                regex: "Vui lòng nhập số và ký tự đặc biệt",
            },
            mobile: {
                required: 'Vui lòng nhập số điện thoại',
                regex: 'Vui lòng nhập 10 con số bắt đầu là 0',
            },
            email: {
                required: "Vui lòng nhập email",
                maxlength: "Vui lòng nhập không quá 50 ký tự",
                email: "Vui lòng nhập đúng định dạng email. vd: a@gmail.com",
                remote: "Email đã được đăng ký. Vui lòng nhập lại."
            },
            password: {
                required: "Vui lòng nhập mật khẩu",
                regex: "Mật khẩu ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt",
            },

            password_confirmation: {
                required: "Vui lòng nhập lại mật khẩu",
                equalTo: "Nhập lại mật khẩu phải trùng khớp",
            },
            hiddenRecaptcha: {
                required: "Vui lòng xác nhận Google reCAPTCHA"
            }
        },

    });

    // Thay đổi province
    $("main .province").change(function (event) {
        /* Act on the event */
        var province_id = $(this).val();
        if (!province_id) {
            updateSelectBox(null, "main .district");
            updateSelectBox(null, "main .ward");
            return;
        }

        $.ajax({
            url: '/index.php?c=address&a=getDistricts',
            type: 'GET',
            data: { province_id: province_id }
        })
            .done(function (data) {
                updateSelectBox(data, "main .district");
                updateSelectBox(null, "main .ward");
            });

        if ($("main .shipping-fee").length) {
            $.ajax({
                url: '/index.php?c=address&a=getShippingFee',
                type: 'GET',
                data: { province_id: province_id }
            })
                .done(function (data) {
                    //update shipping fee and total on UI
                    let shipping_fee = Number(data);
                    let payment_total = Number($("main .payment-total").attr("data")) + shipping_fee;

                    $("main .shipping-fee").html(number_format(shipping_fee) + "₫");
                    $("main .payment-total").html(number_format(payment_total) + "₫");
                });
        }


    });

    // Thay đổi district
    $("main .district").change(function (event) {
        /* Act on the event */
        var district_id = $(this).val();
        if (!district_id) {
            updateSelectBox(null, "main .ward");
            return;
        }

        $.ajax({
            url: '/index.php?c=address&a=getWards',
            type: 'GET',
            data: { district_id: district_id }
        })
            .done(function (data) {
                updateSelectBox(data, "main .ward");
            });
    });

    // Thêm sản phẩm vào giỏ hàng
    $("main .buy-in-detail").click(function (event) {
        /* Act on the event */
        var qty = $(this).prev("input").val();
        var product_id = $(this).attr("product-id");
        $.ajax({
            url: '/index.php?c=cart&a=add',
            type: 'GET',
            data: { product_id: product_id, qty: qty }
        })
            .done(function (data) {
                displayCart(data);

            });
    });

    $.ajax({
        url: '/index.php?c=cart&a=display',
        type: 'GET'
    })
        .done(function (data) {
            displayCart(data);

        });

    // / Thêm sản phẩm vào giỏ hàng
    $("main .buy").click(function (event) {

        var product_id = $(this).attr("product-id");
        $.ajax({
            url: '/index.php?c=cart&a=add',
            type: 'GET',
            data: { product_id: product_id, qty: 1 }
        })
            .done(function (data) {
                // console.log(data);
                displayCart(data);
            });
    });

    $(".info-account").validate({
        rules: {
            // simple rule, converted to {required:true}
            fullname: {
                required: true,
                maxlength: 50,
                regex: /^[a-zA￾ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+$/i

            },
            mobile: {
                required: true,
                regex: /^0([0-9]{9,9})$/
            },
            password: {

                regex: /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/
            },
            password_confirmation: {

                equalTo: '[name=password]'
            }

        },

        messages: {
            fullname: {
                required: 'Vui lòng nhập họ và tên',
                maxlength: 'Vui lòng nhập không quá 50 ký tự',
                regex: 'Vui lòng nhập đúng họ và tên'
            },
            // compound rule
            mobile: {
                required: 'Vui lòng nhập số điện thoại',
                regex: 'Vui lòng nhập đúng định dạng số điện thoại.vd: 0932538468'

            },
            password: {

                regex: 'Mật khẩu phải ít nhất 8 ký tự bao gồm chữ hoa, chữ thường, số và ký tự đặt biệt. vd: 123456aA@'
            },
            password_confirmation: {

                equalTo: 'Mật khẩu không trùng khớp'
            }
        }
    });
    $(".form-login").validate({
        rules: {
            // simple rule, converted to {required:true}
            email: {
                required: true,
                email: true,
            },
            password: {
                required: true,
            },

        },
        messages: {

            email: {
                required: 'Vui lòng nhập email',
                email: 'Vui lòng nhập đúng định dạng email. vd: abc@gmail.com',
            },
            password: {
                required: 'Vui lòng nhập mật khẩu',

            },

        },

    });

    // comment
    $(".form-comment").validate({
        rules: {
            // simple rule, converted to {required:true}
            fullname: {
                required: true,
                maxlength: 50,
                regex: /^[a-zA￾ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+$/i

            },
            // compound rule
            email: {
                required: true,
                email: true
            },

            description: {
                required: true
            }
        },

        messages: {
            fullname: {
                required: 'Vui lòng nhập họ và tên',
                maxlength: 'Vui lòng nhập không quá 50 ký tự',
                regex: 'Vui lòng nhập đúng họ và tên'
            },

            email: {
                required: 'Vui lòng nhập email',
                email: 'Vui lòng nhập đúng định dạng email. vd: abc@gmail.com'
            },
            description: {
                required: 'Vui lòng nhập nội dung'
            }
        },
        submitHandler: function (form) {
            // alert($(form).serialize());
            $('.message').html('<i class="fas fa-spinner fa-spin"></i> Hệ thống đang gởi đán giá, vui lòng chờ ...');
            $('.message').show();//display:block
            $.ajax({
                type: "POST",
                url: "?c=product&a=storeComment",
                data: $(form).serialize(),
                success: function (response) {
                    $('.comment-list').html(response);
                    $('.message').empty();
                    $('.message').hide();
                    //reset form
                    // form.reset();
                    //chuyển input có value là 4 thành 4 sao
                    $('main .product-detail .product-description .answered-rating-input').rating({
                        min: 0,
                        max: 5,
                        step: 1,
                        size: 'md',
                        stars: "5",
                        showClear: false,
                        showCaption: false,
                        displayOnly: false,
                        hoverEnabled: true
                    });
                }
            });
        }
    });

    //contact
    $(".form-contact").validate({
        rules: {
            // simple rule, converted to {required:true}
            fullname: {
                required: true,
                maxlength: 50,
                regex: /^[a-zA￾ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+$/i

            },
            // compound rule
            email: {
                required: true,
                email: true
            },
            mobile: {
                required: true,
                regex: /^0([0-9]{9,9})$/
            },
            content: {
                required: true
            }
        },

        messages: {
            fullname: {
                required: 'Vui lòng nhập họ và tên',
                maxlength: 'Vui lòng nhập không quá 50 ký tự',
                regex: 'Vui lòng nhập đúng họ và tên'
            },
            // compound rule
            mobile: {
                required: 'Vui lòng nhập số điện thoại',
                regex: 'Vui lòng nhập đúng định dạng số điện thoại.vd: 0932538468'

            },
            email: {
                required: 'Vui lòng nhập email',
                email: 'Vui lòng nhập đúng định dạng email. vd: abc@gmail.com'
            },
            content: {
                required: 'Vui lòng nhập nội dung'
            }
        },
        submitHandler: function (form) {
            // alert($(form).serialize());
            $('.message').html('<i class="fas fa-spinner fa-spin"></i> Hệ thống đang gởi mail, vui lòng chờ ...');
            $('.message').show();//display:block
            $.ajax({
                type: "POST",
                url: "?c=contact&a=sendEmail",
                data: $(form).serialize(),
                success: function (response) {
                    $('.message').html(response);
                    //reset form
                    // form.reset();
                }
            });
        }
    });

    //Kiểm tra dữ liệu thỏa mãn mẫu hay không
    //Nếu không thì chuỗi Please check your input hiện ra
    $.validator.addMethod(
        "regex",
        function (value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Please check your input."
    );


    // Tìm kiếm và sắp xếp sản phẩm
    $('#sort-select').change(function (event) {
        var fullURL = getUpdatedParam("sort", $(this).val());
        window.location.href = fullURL;
    });

    // Tìm kiếm theo range
    $('main .price-range input').click(function (event) {
        /* Act on the event */
        // var price_range = $(this).val();
        // window.location.href = 'https://vnexpress.net';
        // window.location.href = `?c=product&price-range=${price_range}`;
        window.location.href = getUpdatedParam('price-range',$(this).val());
        //header("location: /index.php?c=product&price-range=100000-200000")
    });

    $(".product-container").hover(function () {
        $(this).children(".button-product-action").toggle(400);
    });

    // Display or hidden button back to top
    $(window).scroll(function () {
        if ($(this).scrollTop()) {
            $(".back-to-top").fadeIn();
        }
        else {
            $(".back-to-top").fadeOut();
        }
    });

    // Khi click vào button back to top, sẽ cuộn lên đầu trang web trong vòng 0.8s
    $(".back-to-top").click(function () {
        $("html").animate({ scrollTop: 0 }, 800);
    });

    // Hiển thị form đăng ký
    $('.btn-register').click(function () {
        $('#modal-login').modal('hide');
        $('#modal-register').modal('show');
    });

    // Hiển thị form forgot password
    $('.btn-forgot-password').click(function () {
        $('#modal-login').modal('hide');
        $('#modal-forgot-password').modal('show');
    });

    // Hiển thị form đăng nhập
    $('.btn-login').click(function () {
        $('#modal-login').modal('show');
    });

    // Fix add padding-right 17px to body after close modal
    // Don't rememeber also attach with fix css
    $('.modal').on('hide.bs.modal', function (e) {
        e.stopPropagation();
        $("body").css("padding-right", 0);

    });

    // Hiển thị cart dialog
    $('.btn-cart-detail').click(function () {
        $('#modal-cart-detail').modal('show');
    });

    // Hiển thị aside menu mobile
    $('.btn-aside-mobile').click(function () {
        $("main aside .inner-aside").toggle();
    });

    // Hiển thị carousel for product thumnail
    $('main .product-detail .product-detail-carousel-slider .owl-carousel').owlCarousel({
        margin: 10,
        nav: true

    });
    // Bị lỗi hover ở bộ lọc (mobile) & tạo thanh cuộn ngang
    // Khởi tạo zoom khi di chuyển chuột lên hình ở trang chi tiết
    // $('main .product-detail .main-image-thumbnail').ezPlus({
    //     zoomType: 'inner',
    //     cursor: 'crosshair',
    //     responsive: true
    // });

    // Cập nhật hình chính khi click vào thumbnail hình ở slider
    $('main .product-detail .product-detail-carousel-slider img').click(function (event) {
        /* Act on the event */
        $('main .product-detail .main-image-thumbnail').attr("src", $(this).attr("src"));
        var image_path = $('main .product-detail .main-image-thumbnail').attr("src");
        $(".zoomWindow").css("background-image", "url('" + image_path + "')");

    });

    $('main .product-detail .product-description .rating-input').rating({
        min: 0,
        max: 5,
        step: 1,
        size: 'md',
        stars: "5",
        showClear: false,
        showCaption: false
    });

    $('main .product-detail .product-description .answered-rating-input').rating({
        min: 0,
        max: 5,
        step: 1,
        size: 'md',
        stars: "5",
        showClear: false,
        showCaption: false,
        displayOnly: false,
        hoverEnabled: true
    });

    $('main .ship-checkout[name=payment_method]').click(function (event) {
        /* Act on the event */
    });

    $('input[name=checkout]').click(function (event) {
        /* Act on the event */
        window.location.href = "/index.php?c=payment&a=checkout";
    });

    $('input[name=back-shopping]').click(function (event) {
        /* Act on the event */
        window.location.href = "/san-pham.html";
    });

    // Hiển thị carousel for relative products
    $('main .product-detail .product-related .owl-carousel').owlCarousel({
        // loop: true,
        margin: 10,
        nav: true,
        dots: false,
        responsive: {
            0: {
                items: 2
            },
            600: {
                items: 4
            },
            1000: {
                items: 5
            }
        }

    });
});

// Login in google
function onSignIn(googleUser) {
    var id_token = googleUser.getAuthResponse().id_token;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'http://study.com/register/google/backend/process.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        console.log('Signed in as: ' + xhr.responseText);
    };
    xhr.send('idtoken=' + id_token);
}

// Cập nhật giá trị của 1 param cụ thể
function getUpdatedParam(k, v) {//sort, price-asc
    const fullUrl = window.location.href;
    const objUrl = new URL(fullUrl);
    objUrl.searchParams.set(k, v);
    return objUrl.toString();
}

function goToPage(event, page) {
    // ngăn không cho chạy href mặc định của thẻ a
    event.preventDefault();
    const newURL = getUpdatedParam('page', page);
    window.location.href = newURL;
}
// Hiển thị cart
function displayCart(data) {

    //chuyển chuỗi dạng object thành object
    var cart = JSON.parse(data);

    var total_product_number = cart.total_product_number;
    $(".btn-cart-detail .number-total-product").html(total_product_number);

    var total_price = cart.total_price;
    $("#modal-cart-detail .price-total").html(number_format(total_price) + "₫");
    var items = cart.items;
    var rows = "";
    for (let i in items) {
        let item = items[i];
        var row =
            '<hr>' +
            '<div class="clearfix text-left">' +
            '<div class="row">' +
            '<div class="col-sm-6 col-md-1">' +
            '<div>' +
            '<img class="img-responsive" src="../upload/' + item.img + '" alt="' + item.name + ' ">' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-6 col-md-3">' +
            `<a class="product-name" href="${item.linkProductDetail}">` + item.name + '</a>' +
            '</div>' +
            '<div class="col-sm-6 col-md-2">' +
            '<span class="product-item-discount">' + number_format(Math.round(item.unit_price)) + '₫</span>' +
            '</div>' +
            '<div class="col-sm-6 col-md-3">' +
            '<input type="hidden" value="1">' +
            '<input type="number" onchange="updateProductInCart(this,' + item.product_id + ')" min="1" value="' + item.qty + '">' +
            '</div>' +
            '<div class="col-sm-6 col-md-2">' +
            '<span>' + number_format(Math.round(item.total_price)) + '₫</span>' +
            '</div>' +
            '<div class="col-sm-6 col-md-1">' +
            '<a class="remove-product" href="javascript:void(0)" onclick="deleteProductInCart(' + item.product_id + ')">' +
            '<span class="glyphicon glyphicon-trash"></span>' +
            '</a>' +
            '</div>' +
            '</div>' +
            '</div>';
        rows += row;
    }
    $("#modal-cart-detail .cart-product").html(rows);
}

function deleteProductInCart(product_id) {
    $.ajax({
        url: '/index.php?c=cart&a=delete',
        type: 'GET',
        data: { product_id: product_id }
    })
        .done(function (data) {
            displayCart(data);

        });
}

// Thay đổi số lượng sản phẩm trong giỏ hàng
function updateProductInCart(self, product_id) {
    var qty = $(self).val();
    $.ajax({
        url: '/index.php?c=cart&a=update',
        type: 'GET',
        data: { product_id: product_id, qty: qty }
    })
        .done(function (data) {
            displayCart(data);

        });
}


// Cập nhật các option cho thẻ select
function updateSelectBox(data, selector) {
    var items = JSON.parse(data);
    $(selector).find('option').not(':first').remove();
    if (!data) return;
    for (let i = 0; i < items.length; i++) {
        let item = items[i];
        let option = '<option value="' + item.id + '"> ' + item.name + '</option>';
        $(selector).append(option);
    }

}