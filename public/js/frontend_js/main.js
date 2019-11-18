/*price range*/

if ($.fn.slider) {
    $('#sl2').slider();
}

var RGBChange = function () {
    $('#RGB').css('background', 'rgb(' + r.getValue() + ',' + g.getValue() + ',' + b.getValue() + ')')
};

/*scroll to top*/

$(document).ready(function () {
    $(function () {
        $.scrollUp({
            scrollName: 'scrollUp', // Element ID
            scrollDistance: 300, // Distance from top/bottom before showing element (px)
            scrollFrom: 'top', // 'top' or 'bottom'
            scrollSpeed: 300, // Speed back to top (ms)
            easingType: 'linear', // Scroll to top easing (see http://easings.net/)
            animation: 'fade', // Fade, slide, none
            animationSpeed: 200, // Animation in speed (ms)
            scrollTrigger: false, // Set a custom triggering element. Can be an HTML string or jQuery object
            //scrollTarget: false, // Set a custom target element for scrolling to the top
            scrollText: '<i class="fa fa-angle-up"></i>', // Text for element, can contain HTML
            scrollTitle: false, // Set a custom <a> title if required.
            scrollImg: false, // Set true to use image
            activeOverlay: false, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
            zIndex: 2147483647 // Z-Index for the overlay
        });
    });
});

$(document).ready(function (){

    //Change Price & Stock with size
    $("#selSize").change(function(){
        var idSize = $(this).val();
        if(idSize == ""){
            return false;
        }
        $.ajax({
            type:'get',
            url:'/get-product-price',
            data:{idSize:idSize},
            success:function(resp){
                //alert(resp); return false;
                var arr = resp.split('#');
                $("#getprice").html("KSH "+arr[0]);
                $("#price").val(arr[0]);
                if(arr[1] ==0){
                    $("#cartButton").hide();
                    $("#availability").text("Out of Stock");
                }else{
                    $("#cartButton").show();
                    $("#availability").text("In Stock");
                }
            },error:function(){
                alert("Error");
            }
        });
    });
    //Replace main Image with Alternate Image
    $(".changeImage").click(function(){
        var image= $(this).attr('src');
        $(".mainImage").attr("src", image);
    });
});




$().ready(function(){
    //Validate Register form on keyup and submit
    $("#registerForm").validate({
        rules:{
            name:{
                required:true,
                minlength:2,
                accept: "[a-zA-Z]+"
            },
            password:{
                required:true,
                minlength:6
            },
            email:{
                required:true,
                email:true,
                remote:"/check-email"
            }
        },
        messages:{
            name:{ 
                required:"Please enter your Name",
                minlength: "Your Name must be atleast 2 characters long",
                accept: "Your Name must contain letters only"       
            }, 
            password:{
                required:"Please provide your Password",
                minlength: "Your Password must be atleast 6 characters long"
            },
            email:{
                required: "Please enter your Email",
                email: "Please enter valid Email",
                remote: "Email already exists!"
            }
        }
    });

    //Validate login form on keyup and submit
    $("#loginForm").validate({
        rules:{
            password:{
                required:true,
            },
            email:{
                required:true,
                email:true
            }
        },
        messages:{ 
            password:{
                required:"Please provide your Password"
            },
            email:{
                required: "Please enter your Email",
                email:"Enter a valid Email"
            }
        }
    });

    //Password Strength Script

    $('#password').passtrength({
        minChars:6,
        passwordToggle:true,
        tooltip:true,
        eyeImg: "/images/frontend_images/eye.svg"
    });

    //Account Form
    $('#accountForm').validate({
        rules:{
            name:{
                required:true,
                minlength:2,
                accept: "[a-zA-Z]+"
            },
            address:{
                required:true,
                minlength:10
            },
            county:{
                required:true,
            },
            region:{
                required:true,
            }

        },
        messages:{
            name:{ 
                required:"Please enter your Name",
                minlength: "Your Name must be atleast 2 characters long",
                accept: "Your Name must contain letters only"       
            }, 
            address:{
                required:"Please provide your Address",
                minlength: "Your Address must be atleast 10 characters long"
            },
            county:{
                required: "Please enter your County",
            }
        }
    });
    $('#current_pass').keyup(function(){
        var current_pass = $(this).val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            },
            type:'post',
            url:'/check-user-pass',
            data:{current_pass:current_pass},
            success:function (resp){
                if(resp=="false"){
                    $("#chkPwd").html("<font color = 'red'> Current Password is incorrect</font>");
                }else if(resp=="true"){
                    $("#chkPwd").html("<font color = 'green'> Current Password is correct</font>");
                }
            },error:function(){
                alert('Error');
            }
        });
    });

    $("#passwordForm").validate({
        rules:{
            current_pass:{
                required: true,
                minlength:6,
                maxlength:20
            },
            new_pass:{
                required: true,
                minlength:6,
                maxlength:20
            },
            confirm_pass:{
                required:true,
                minlength:6,
                maxlength:20,
                equalTo:"#new_pass"
            }
        },
        errorClass: "help-inline",
        errorElement: "span",
        highlight:function(element, errorClass, validClass) {
            $(element).parents('.control-group').addClass('error');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).parents('.control-group').removeClass('error');
            $(element).parents('.control-group').addClass('success');
        }
    });

    // Copy Billing Address to Shipping Address Script
    $("#copyAddress").click(function(){
        if(this.checked){
            $("#shipping_name").val($("#billing_name").val());
            $("#shipping_address").val($("#billing_address").val());
            $("#shipping_county").val($("#billing_county").val());
            $("#shipping_region").val($("#billing_region").val());
            $("#shipping_phone").val($("#billing_phone").val());
        }else{
            $("#shipping_name").val('');
            $("#shipping_address").val('');
            $("#shipping_county").val('');
            $("#shipping_region").val('');
            $("#shipping_phone").val('');
        }
    });

});

// Instantiate EasyZoom instances
var $easyzoom = $('.easyzoom').easyZoom();

// Setup thumbnails example
var api1 = $easyzoom.filter('.easyzoom--with-thumbnails').data('easyZoom');

$('.thumbnails').on('click', 'a', function(e) {
    var $this = $(this);

    e.preventDefault();

    // Use EasyZoom's `swap` method
    api1.swap($this.data('standard'), $this.attr('href'));
});

// Setup toggles example
var api2 = $easyzoom.filter('.easyzoom--with-toggle').data('easyZoom');

$('.toggle').on('click', function() {
    var $this = $(this);

    if ($this.data("active") === true) {
        $this.text("Switch on").data("active", false);
        api2.teardown();
    } else {
        $this.text("Switch off").data("active", true);
        api2._init();
    }
});

function selectPaymentMethod(){
    alert("test");
    return false;
}
