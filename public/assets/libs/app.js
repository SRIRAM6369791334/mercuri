$(".name").blur(function () {
    var lettersRegex = /^[a-zA-Z]+$/;

    var name = $(this).val();
    var out = lettersRegex.test(name);

    if (name.length != 0) {
        if (out == false) {
            $("#name-error").text(
                "Letters only allowed don't use numbers & special char"
            );
            $(this).focus();
        } else {
            $("#name-error").text("");
        }
    } else {
        $("#name-error").text("");
    }
});

$("#email").blur(function () {
    var lettersRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    var email = $(this).val();
    var out = lettersRegex.test(email);

    if (email.length != 0) {
        if (out == false) {
            $("#email-error").text("Email format error");
            $(this).focus();
        } else {
            $("#email-error").text("");
        }
    } else {
        $("#email-error").text("");
    }
});

$("#pin").blur(function () {
    var lettersRegex = /^[0-9]{4}$/;

    var pin = $(this).val();
    var out = lettersRegex.test(pin);

    if (pin.length != 0) {
        if (out == false) {
            $("#pin-error").text("Enter 4 digit numbers only");
            $(this).focus();
        } else {
            $("#pin-error").text("");
        }
    } else {
        $("#pin-error").text("");
    }
});

$("#dropdowndata").hide();

$("#fieldtype").change(function () {
    var fieldtype = $(this).val();

    if (fieldtype == 7) {
        $("#dropdowndata").show();
    } else {
        $("#dropdowndata").hide();
    }
});

$(".addfields").click(function () {
    var main = $("#optionfielddetails");
    var optionfielddetails = `
        <tr>
            <td>
                <input type="text" class="form-control" name="orderno[]">
            </td>
            <td>
                <input type="text" class="form-control" name="optionname[]">
            </td>
            <td>
                <select class="form-control" name="mhe_rack[]">
                    <option value="MHE">MHE</option>
                    <option value="Rack">Rack</option>
                </select>
            </td>
            <td>
                <select class="form-control" name="pro_catid[]">
                    <option value="Special">Special</option>
                    <option value="Standard">Standard</option>
                </select>
            </td>
            <td>
                <button class="btn btn-danger remove" type="button">Remove</button>
            </td>
        </tr>`;

    main.append(optionfielddetails);
});

$(document).on("click", ".remove", function () {
    $(this).closest("tr").remove();
});

function textboxvalidation(input) {
    var lettersRegex = /^.*$/;

    var name = $(input).val();
    var out = lettersRegex.test(name);

    if (name.length != 0) {
        if (out == false) {
            $("#myalert").removeClass("d-none");
            $(".error-text").text(
                "Letters only allowed don't use numbers & special char"
            );
            // alert("Letters only allowed don't use numbers & special char");
            $(input).focus();
        } else {
            $("#name-error").text("");
            $("#myalert").addClass("d-none");
        }
    } else {
        $("#name-error").text("");
        $("#myalert").addClass("d-none");
    }
}

function namevalidation(input) {
    var lettersRegex = /^[A-Za-z\s]+$/;

    var name = $(input).val();
    var out = lettersRegex.test(name);

    // if(name.length < 3)
    // {

    //     $('#myalert').removeClass('d-none');
    //     $(".error-text").text("Minimum Enter 3 Letters");
    //     // alert("Letters only allowed don't use numbers & special char");
    //     $(input).focus();
    // }
    // else
    // {
    //     $("#name-error").text("");
    //     $('#myalert').addClass('d-none');
    // }

    if (name.length != 0) {
        if (out == false) {
            $("#myalert").removeClass("d-none");
            $(".error-text").text(
                "Letters only allowed don't use numbers & special char"
            );
            // alert("Letters only allowed don't use numbers & special char");
            $(input).focus();
        } else {
            $("#name-error").text("");
            $("#myalert").addClass("d-none");
        }
    } else {
        $("#name-error").text("");
        $("#myalert").addClass("d-none");
    }
}

function gstvalidation(input) {
    var lettersRegex =
        /^(?:\d{2}[A-Z]{5}\d{4}[A-Z]{1}[A-Z\d]{1}[Z]{1}[A-Z\d]{1})$/;

    var name = $(input).val();
    var out = lettersRegex.test(name);

    if (name.length < 3) {
        $("#myalert").removeClass("d-none");
        $(".error-text").text("Minimum Enter 3 Letters");
        // alert("Letters only allowed don't use numbers & special char");
        $(input).focus();
    } else {
        $("#name-error").text("");
        $("#myalert").addClass("d-none");
    }
    if (name.length != 0) {
        if (out == false) {
            $("#myalert").removeClass("d-none");
            $(".error-text").text("GST Format Error");
            // alert("Letters only allowed don't use numbers & special char");
            $(input).focus();
        } else {
            $("#name-error").text("");
            $("#myalert").addClass("d-none");
        }
    } else {
        $("#name-error").text("");
        $("#myalert").addClass("d-none");
    }
}

function mobilenumvalidation(input) {
    var value = $(input).val();

    // Check if the input value does not contain "+91-"
    // if (!value.includes("+91-")) {
    //     // Restore the "+91-" prefix
    //     input.value = "+91-" + value;
    // }

    // var lettersRegex =  /^\+91-\d{10}$/;
    var lettersRegex = /^\d{10}$/;

    var mobilenum = $(input).val();
    var out = lettersRegex.test(mobilenum);

    if (mobilenum.length != 0) {
        if (out == false) {
            $("#myalert").removeClass("d-none");
            $(".error-text").text("Enter 10 digit numbers only ");
            // alert("Enter 10 digit numbers only");
            $(input).focus();
        } else {
            $("#mobile-error").text("");
            $("#myalert").addClass("d-none");
        }
    } else {
        $("#mobile-error").text("");
        $("#myalert").addClass("d-none");
    }
}

function mobilenumvalidationuser(input) {
    var value = $(input).val();

    // Check if the input value does not contain "+91-"
    if (!value.includes("+91-")) {
        // Restore the "+91-" prefix
        input.value = "+91-" + value;
    }

    var lettersRegex = /^\+91-\d{10}$/;
    // var lettersRegex = /^\d{10}$/;

    var mobilenum = $(input).val();
    var out = lettersRegex.test(mobilenum);

    if (mobilenum.length != 0) {
        if (out == false) {
            $("#myalert").removeClass("d-none");
            $(".error-text").text("Enter 10 digit numbers only ");
            // alert("Enter 10 digit numbers only");
            $(input).focus();
        } else {
            $("#mobile-error").text("");
            $("#myalert").addClass("d-none");
        }
    } else {
        $("#mobile-error").text("");
        $("#myalert").addClass("d-none");
    }
}

function decimalnumvalidation(input) {
    // var lettersRegex = /^\d+\.\d+$/;
    var lettersRegex = /^[0-9]+$/;

    var mobilenum = $(input).val();
    var out = lettersRegex.test(mobilenum);

    if (mobilenum.length != 0) {
        if (out == false) {
            $("#myalert").removeClass("d-none");
            $(".error-text").text("Enter Decimal Numbers only");
            // alert("Enter 10 digit numbers only");
            $(input).focus();
        } else {
            $("#mobile-error").text("");
            $("#myalert").addClass("d-none");
        }
    } else {
        $("#mobile-error").text("");
        $("#myalert").addClass("d-none");
    }
}

function requiredvalidation(input) {
    // var lettersRegex = /^\d+\.\d+$/;

    var requirednum = $(input).val();
    // var out = lettersRegex.test(mobilenum);

    if (requirednum.length == 0) {
        if (out == false) {
            $("#myalert").removeClass("d-none");
            $(".error-text").text("Required Field");
            // alert("Enter 10 digit numbers only");
            $(input).focus();
        } else {
            $("#mobile-error").text("");
            $("#myalert").addClass("d-none");
        }
    } else {
        $("#mobile-error").text("");
        $("#myalert").addClass("d-none");
    }
}

function emailvalidation(input) {
    // var lettersRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    var lettersRegex =
        /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*\.[a-zA-Z]{2,}$/;

    var email = $(input).val();
    var out = lettersRegex.test(email);

    if (email.length != 0) {
        if (out == false) {
            $("#myalert").removeClass("d-none");
            $(".error-text").text("Email format error");
            // alert("Email Format Error");
            $(input).focus();
        } else {
            $("#email-error").text("");
            $("#myalert").addClass("d-none");
        }
    } else {
        $("#email-error").text("");
        $("#myalert").addClass("d-none");
    }
}

function numbersonlyvalidation(input) {
    var lettersRegex = /^[0-9]+$/;

    var postcode = $(input).val();
    var out = lettersRegex.test(postcode);

    if (postcode.length != 0) {
        if (out == false) {
            $("#myalert").removeClass("d-none");
            $(".error-text").text("Numbers Only Allowed");
            // alert("Numbers Only Allowed");

            $(input).focus();
        } else {
            $("#postcode-error").text("");
            $("#myalert").addClass("d-none");
        }
    } else {
        $("#postcode-error").text("");
        $("#myalert").addClass("d-none");
    }
}

$(document).on("keydown", ".pinvalidation", function (event) {
    if (event.key === "e" || event.key === "E") {
        event.preventDefault();
    }

    if (
        (event.key >= 65 && event.key <= 90) ||
        (event.key >= 97 && event.key <= 122)
    ) {
        event.preventDefault(); // Prevent the default action
    }
});

function pinvalidation(input) {
    var lettersRegex = /^[0-9]{4}$/;

    var postcode = $(input).val();
    var out = lettersRegex.test(postcode);

    if (postcode.length != 0 || postcode.length < 4) {
        if (out == false) {
            $("#myalert").removeClass("d-none");
            $(".error-text").text("Enter 4 Digit Numbers Only");
            // alert("Numbers Only Allowed");

            $(input).focus();
        } else {
            $("#postcode-error").text("");
            $("#myalert").addClass("d-none");
        }
    } else {
        $("#postcode-error").text("");
        $("#myalert").addClass("d-none");
    }
}

function postvalidation(input) {
    var lettersRegex = /^[0-9]{6}$/;

    var postcode = $(input).val();
    var out = lettersRegex.test(postcode);

    if (postcode.length != 0) {
        if (out == false) {
            $("#myalert").removeClass("d-none");
            $(".error-text").text("Enter 6 Digit Numbers Only");
            // alert("Numbers Only Allowed");

            $(input).focus();
        } else {
            $("#postcode-error").text("");
            $("#myalert").addClass("d-none");
        }
    } else {
        $("#postcode-error").text("");
        $("#myalert").addClass("d-none");
    }
}

$(".formupdate").click(function () {
    var formid = $(this).data("formid");
    $("#exampleModal").modal("show");
    $("#formid").val(formid);
});

$(document).on("submit", "#formupdate", function () {
    $.ajax({
        type: "POST",
        url: "/updatestatus",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,

        success: function (data) {
            if ($.isEmptyObject(data.error)) {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Status Updated..!",
                    showConfirmButton: false,
                    timer: 1500,
                });

                location.reload();
            } else {
                printErrorMsg(data.error);
            }
        },
    });
});

$(document).on("submit", "#formupdatesinglelead", function () {
    $.ajax({
        type: "POST",
        url: "/updatestatusformlead",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,

        success: function (data) {
            if ($.isEmptyObject(data.error)) {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Status Updated..!",
                    showConfirmButton: false,
                    timer: 1500,
                });

                location.reload();
            } else {
                printErrorMsg(data.error);
            }
        },
    });
});

$(document).on("click", ".prodelete", function () {
    const id = $(this).attr("id");
    text = id.replace("id-", "");
    // alert(text);
    Swal.fire({
        title: "Are you sure?",
        text: "You want to delete this record..!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "GET",
                url: `productdelete/${text}`,
                success: function (data) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Your file has beed Deleted..",
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    location.reload();
                },
                error: function (data) {
                    Swal.fire(
                        "Deleted!",
                        "Your file has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
});

$(document).on("click", ".userdelete", function () {
    // $(".userdelete").on("click", function () {

    const id = $(this).attr("id");
    text = id.replace("id-", "");
    // alert(text);
    Swal.fire({
        title: "Are you sure?",
        text: "You want to delete this record..!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "GET",
                url: `userdelete/${text}`,
                success: function (data) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Your file has beed Deleted..",
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    location.reload();
                },
                error: function (data) {
                    Swal.fire(
                        "Deleted!",
                        "Your file has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
});

$(document).on("click", ".bankdelete", function () {
    const id = $(this).attr("id");
    text = id.replace("id-", "");
    // alert(text);
    Swal.fire({
        title: "Are you sure?",
        text: "You want to delete this record..!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "GET",
                url: `bankdelete/${text}`,
                success: function (data) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Your file has beed Deleted..",
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    location.reload();
                },
                error: function (data) {
                    Swal.fire(
                        "Deleted!",
                        "Your file has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
});

// $(".leaddelete").on("click", function () {
$(document).on("click", ".leaddelete", function () {
    const id = $(this).attr("id");
    text = id.replace("id-", "");
    // alert(text);
    Swal.fire({
        title: "Are you sure?",
        text: "You want to delete this record..!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "GET",
                url: `leaddelete/${text}`,
                success: function (data) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Your file has beed Deleted..",
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    location.reload();
                },
                error: function (data) {
                    Swal.fire("111!", "ddd", "success");
                },
            });
        }
    });
});

setTimeout(function () {
    $(".alert").fadeOut("slow");
}, 1000);

// $('.leadedit').on('click', function() {
//     const id = $(this).attr('id');
//     text = id.replace('id-', '');
//     Livewire.emit('loadLeadData', text);

// });

$(function () {
    $("#datepicker").datepicker();
});

$(".sidebartrigger").click(function () {
    $(".todayactivities").toggleClass("show");
    // alert("data");
});

$(".filtersidebartrigger").click(function () {
    $(".filtertrigger").toggleClass("show");
    // alert("data");
});

$(".twosidebartrigger").click(function () {
    $(".overdueactivities").toggleClass("show");
    // alert("data");
});

$(".threesidebartrigger").click(function () {
    $(".renewalactivities").toggleClass("show");
    // alert("data");
});

$(".tenture").hide();
$(".cstartdate").hide();
$(".cenddate").hide();

$(document).ready(function () {
    var product = $(".getproduct").val();
    if (product == 2 || product == 3 || product == 4 || product == 18) {
        $(".tenture").show();
        $(".cstartdate").show();
        $(".cenddate").show();
    } else {
        $(".tenture").hide();
        $(".cstartdate").hide();
        $(".cenddate").hide();
    }
});
$(".getproduct").change(function () {
    var product = $(this).val();

    if (product == 2 || product == 3 || product == 4 || product == 18) {
        $(".tenture").show();
        $(".cstartdate").show();
        $(".cenddate").show();
    } else {
        $(".tenture").hide();
        $(".cstartdate").hide();
        $(".cenddate").hide();
    }
});

$(".tenturedata").change(function () {
    var tenture = $(this).val();
    var cstartdate = $(".startdate").val();

    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenture, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD");

    console.log(cenddate); // Outputs '2023-06-04'

    $(".enddate").val(cenddate);
    $(".renewaldate").val(cenddate);
});

$(".campaigntenturedata").keyup(function () {
    var tenture = $(this).val();
    var cstartdate = $(".campaignstartdate").val();

    var tenuredata = tenture - 1;
    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04'

    $(".campaignenddate").val(cenddate);
    $(".campaignrenewaldate").val(cenddate);
});

$(".campaigntenturedata2").keyup(function () {
    var tenture = $(this).val();
    var cstartdate = $(".campaignstartdate2").val();

    var tenuredata = tenture - 1;
    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04'

    $(".campaignenddate2").val(cenddate);
    $(".campaignrenewaldate2").val(cenddate);
});

$(".campaigntenturedata2").blur(function () {
    var tenture = $(this).val();
    var cstartdate = $(".campaignstartdate2").val();

    var tenuredata = tenture - 1;
    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04'

    $(".campaignenddate2").val(cenddate);
    $(".campaignrenewaldate2").val(cenddate);

    $(".campaigncharge2").focus();
});

// ----------------------

$(".campaigntenturedataedit").blur(function () {
    // $(document).on("input",".campaigntenturedataedit",function(){

    var tenture = $(this).val();
    // var cstartdate = $(".campaignstartdateedit").val();
    var cstartdate = $(".campaignstartdateedit").val();

    var tenuredata = tenture - 1;
    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04'
    // alert(cenddate);
    $(".campaignenddateedit").val(cenddate);
    $(".campaignrenewaldateedit").val(cenddate);

    $("#campaigncharge1").focus();
});

$(".campaignstartdateedit").blur(function () {
    var tenture = $(".campaigntenturedataedit").val();
    var cstartdate = $(this).val();

    var tenuredata = tenture - 1;

    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate, including time
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04 12:34'

    $(".campaignenddateedit").val(cenddate);
    $(".campaignrenewaldateedit").val(cenddate);
});

$(".campaigntenturedatadesignedit").blur(function () {
    var tenture = $(this).val();
    // var cstartdate = $(".campaignstartdateedit").val();
    var cstartdate = $("#campaignstartdatedesign").val();

    var tenuredata = tenture - 1;
    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04'
    // alert(cenddate);
    $(".campaignenddateedit").val(cenddate);
    $(".campaignrenewaldateedit").val(cenddate);

    $(".postercount").focus();
});
// -----------------------

$(".campaignstartdate").change(function () {
    var tenture = $(".campaigntenturedata").val();
    var cstartdate = $(this).val();

    var tenuredata = tenture - 1;

    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate, including time
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04 12:34'

    $(".campaignenddate").val(cenddate);
    $(".campaignrenewaldate").val(cenddate);
});

$(".campaignstartdate2").change(function () {
    var tenture = $(".campaigntenturedata2").val();
    var cstartdate = $(this).val();

    var tenuredata = tenture - 1;

    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate, including time
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04 12:34'

    $(".campaignenddate2").val(cenddate);
    $(".campaignrenewaldate2").val(cenddate);
});

$(document).on("keyup", ".renewalamount", function () {
    var renewalamount = $(this).val();
    var receivedamount = $(".receivedamount").val();

    $(".pendingamount").val(renewalamount - receivedamount);
});
$(".outstandingexpecteddatemandatory").hide();
$(document).on("input", ".receivedamount", function () {
    var receivedamount = $(this).val();
    var renewalamount = $(".renewalamount").val();

    $(".pendingamount").val(renewalamount - receivedamount);
    $(".receivedamount").prop("max", renewalamount);

    if ($(".pendingamount").val() != 0) {
        $(".outstandingexpecteddatemandatory").show();
        $(".outstandingexpecteddate").prop("required", true);
    } else {
        $(".outstandingexpecteddatemandatory").hide();
        $(".outstandingexpecteddate").prop("required", false);
    }
});

$(".cstartdate").change(function () {
    var cstartdate = $(".startdate").val();
    var tenturedata = $(".tenturedata").val();

    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenturedata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD");

    console.log(cenddate); // Outputs '2023-06-04'

    $(".enddate").val(cenddate);
    $(".renewaldate").val(cenddate);
});
// introJs().start();

// $(".allleads").click(function(){

//     var fromdate = $(".fromdate").val();
//     var todate = $(".todate").val();
//     var user = $(".user").val();

//     $.ajax({
//         type:"POST",
//         url: "/getallleads",
//         // async: false,
//         cache: false,
//         contentType: false,
//         processData: false,

//         success: function(data) {

//         },

//     });

//     $("#staticBackdrop").modal('show');
// });

document.addEventListener("livewire:load", function () {
    Livewire.on("show-modal", function () {
        $("#staticBackdrop").modal("show");
    });
});

$(document).on("click", ".campaignpause", function () {
    const renewalid = $(this).data("renewalid");
    const campaignid = $(this).data("campaignid");
    // url: `/campaignpause/${id}`,

    const id = $(this).data("id");

    $.ajax({
        type: "GET",
        url: "/getcampaigndates/" + campaignid,
        // async: false,
        cache: true,
        contentType: false,
        processData: false,

        success: function (data) {
            var startDate = moment(data.campaignstartdate);
            var endDate = moment(data.campaignenddate);
            var changedStartDateStr = startDate.format("DD-MM-YYYY");
            var changedEndDateStr = endDate.format("DD-MM-YYYY");

            // var startDate = moment(data.campaignstartdate);
            // var endDate = moment(data.campaignenddate);
            // var pdate = moment(data.campaign_pausedate);
            // var changedStartDateStr = startDate.format("DD-MM-YYYY");
            // var changedEndDateStr = endDate.format("DD-MM-YYYY");
            // var pausedate = pdate.format("DD-MM-YYYY");

            $("#campaignpausemodal").modal("show");
            $("#campaignperiod3").text(
                "Campaign Start Date " +
                    changedStartDateStr +
                    " Campaign End Date " +
                    changedEndDateStr
            );
            $("#campaignpausedate").attr("min", changedStartDateStr);
            $("#campaignpausedate").attr("max", changedEndDateStr);
            $("#campaignid1").val(campaignid);
        },
    });
});

$(document).on("submit", "#campaignpause", function () {
    $.ajax({
        type: "POST",
        url: "/campaignpause",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Updated..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("click", ".campaignresume", function () {
    // $(".campaignresume").on("click", function () {

    const renewalid = $(this).data("renewalid");
    const campaignid = $(this).data("campaignid");
    // url: `/campaignpause/${id}`,

    const id = $(this).data("id");

    $.ajax({
        type: "GET",
        url: "/getcampaigndates/" + campaignid,
        // async: false,
        cache: false,
        contentType: false,
        processData: false,

        success: function (data) {
            var startDate = moment(data.campaignstartdate);
            var endDate = moment(data.campaignenddate);
            var pdate = moment(data.campaign_pausedate);
            var changedStartDateStr = startDate.format("DD-MM-YYYY");
            var changedEndDateStr = endDate.format("DD-MM-YYYY");
            var pausedate = pdate.format("DD-MM-YYYY");

            $(".campaignresumecalendar").flatpickr({
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                theme: "light",
            });
            $("#campaignpausedate").text("Pause Date : " + pausedate);
            $("#campaignperiod1").text(
                "Campaign Start Date " +
                    changedStartDateStr +
                    " Campaign End Date " +
                    changedEndDateStr
            );
            $("#campaignresumedate").attr("min", changedStartDateStr);
            $("#campaignresumedate").attr("max", changedEndDateStr);
            $("#campaignresumemodal").modal("show");
            $("#campaignid2").val(campaignid);
        },
    });
});

$(document).on("submit", "#campaignresume", function () {
    $.ajax({
        type: "POST",
        url: "/campaignresume",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Updated..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("click", ".notesdelete", function () {
    const id = $(this).data("id");

    Swal.fire({
        title: "Are you sure?",
        text: "You want to delete this record?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "GET",
                url: `/deletedesigns/${id}`,
                success: function (data) {
                    if (data.success) {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Record deleted successfully!",
                            showConfirmButton: false,
                            timer: 1500,
                        });
                        location.reload();
                    } else {
                        Swal.fire("Error!", "Failed to delete the record.", "error");
                    }
                },
                error: function () {
                    Swal.fire("Error!", "Something went wrong.", "error");
                },
            });
        }
    });
});


// $(".fileupload").hide();
$(".openfileupload").click(function () {
    $(".fileupload").toggle(200);
});

$(document).on("click", ".notesedit", function () {
    var id = $(this).data("id");

    $.ajax({
        type: "GET",
        url: "/getnotesinfo/" + id,
        // async: false,
        cache: false,
        contentType: false,
        processData: false,

        success: function (data) {
            $("#leadeditModal").modal("show");
            $("#leadsdata").val(data.notes_data);
            $("#oldimage").val(data.filename);
            $("#oldimage").val(data.filename);
            $("#callinfoedit").val(data.callinfostatus);
            $("#id").val(data.id);
        },
    });
});

// $(".remainderedit").click(function(){
$(document).on("click", ".remainderedit", function () {
    var id = $(this).data("id");

    $.ajax({
        type: "GET",
        url: "/getremainderedit/" + id,
        // async: false,
        cache: false,
        contentType: false,
        processData: false,

        success: function (data) {
            $("#remaindereditModal").modal("show");
            $("#remainderdata").val(data.remainder_details);
            $("#reminderdate").val(data.remainder_date);
            $("#remainderid").val(data.id);
        },
    });
});

$(document).on("submit", "#updatenotes", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/updatenotes",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Updated..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("submit", "#updateremainder", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/updateremainder",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Updated..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).ready(function () {
    // Get the active tab from local storage
    var activeTab = localStorage.getItem("activeTab");

    //   alert(activeTab);
    // If an active tab is stored, show it
    if (activeTab) {
        $('#pills-tab button[data-bs-target="' + activeTab + '"]').tab("show");
    }

    // Update the active tab in local storage when a new tab is shown
    $('button[data-bs-toggle="pill"]').on("show.bs.tab", function (e) {
        localStorage.setItem("activeTab", $(e.target).data("bs-target"));
    });
});

// $(document).on("click", ".addrow", function () {

//     var appedestimate = $(".estimatetable");

//     var appendrows = $(".estimatetable tr").html();

//     appedestimate.append('<tr>'+appendrows+'</tr>');

//         $(document).on('click', '.remove', function() {
//             $(this).parents('tr').remove();
//             });

// });

$(document).ready(function () {
    $(".removestage:first").css("visibility", "hidden");

    $(".js-example-basic-single").select2();

    $(document).on("click", ".addstage", function () {
        $(".js-example-basic-single").select2("destroy");

        setTimeout(function () {
            $(".js-example-basic-single").select2();
        }, 100);

        $(".removestage:first").css("visibility", "visible");

        var main = $("#appendstageparent");

        // var appendstage = main.children('.appendstage')[0].outerHTML;

        var appendstage = $(this).closest("tr").html();

        if ($(".removestage:first").css("visibility", "visible")) {
            if (
                main.append("<tr class='appendstage'>" + appendstage + "</tr>")
            ) {
                $(".addstage:first").prop("disabled", true);
                $(".removestage:first").css("visibility", "hidden");
            }
        }
    });

    $(document).on("click", ".removestage", function () {
        if ($(this).closest("tr").remove()) {
            if ($(".addstage").length == 1) {
                $(".addstage:first").prop("disabled", false);
                $(".removestage:first").css("visibility", "hidden");
            }
        }
    });
});

$(document).ready(function () {
    $(".removestage:first").css("visibility", "hidden");

    $(".js-example-basic-single").select2();

    $(".hiddenelement").css("display", "none");
    $(document).on("click", ".editaddstage", function () {
        $(".js-example-basic-single").select2("destroy");

        setTimeout(function () {
            $(".js-example-basic-single").select2();
        }, 100);

        $(".removestage:first").css("visibility", "visible");

        var main = $("#editappendstageparent");

        // var appendstage = main.children('.appendstage')[0].outerHTML;

        var editappendstage = $(".hiddenelement").closest("tr").html();

        if ($(".removestage:first").css("visibility", "visible")) {
            if (
                main.append(
                    "<tr class='editappendstage'>" + editappendstage + "</tr>"
                )
            ) {
                $(".editappendstage:first").prop("disabled", true);
                $(".removestage:first").css("visibility", "hidden");
            }
        }

        if ($(".hiddenelement").length == 1) {
            $(".hiddenelement").css("display", "none");
        }
    });

    $(document).on("click", ".removestage", function () {
        if ($(this).closest("tr").remove()) {
            if ($(".editaddstage").length == 1) {
                $(".editappendstage:first").prop("disabled", false);
                $(".removestage:first").css("visibility", "hidden");
            }

            gstcalc();
        }
    });
});

$(document).on("change", ".prname", function () {
    var prname = $(this).val();

    var hsncode = $(this).closest("tr").find(".hsncode");
    var qty = $(this).closest("tr").find(".qty").val("");

    var amt = $(this).closest("tr").find(".amt").val("");

    var hsncode = $(this).closest("tr").find(".hsncode");
    var qty = $(this).closest("tr").find(".qty");
    var rate = $(this).closest("tr").find(".rate");
    var gst = $(this).closest("tr").find(".gst");
    var amt = $(this).closest("tr").find(".amt");

    // alert(d);
    $.ajax({
        type: "GET",
        url: "/getdetails/" + prname,
        success: function (data) {
            hsncode.val(data.hsn);
            rate.val(data.rate);
            gst.val(data.GST);
        },
    });
});

$(document).on("blur", ".rate", function () {
    var rate = $(this).val();
    var qty = $(this).closest("tr").find(".qty").val();

    var totamt = $(this).closest("tr").find(".amt");

    if (rate == "") {
        rate = 0;
    } else {
        rate = parseInt(rate);
    }

    var totamt1 = parseInt(rate);
    // alert(totamt);
    totamt.val(totamt1 * qty);

    gstcalc();
});

$(document).on("blur", ".qty", function () {
    var qty = $(this).val();
    var rate = $(this).closest("tr").find(".rate").val();

    var totamt = $(this).closest("tr").find(".amt");

    if (rate == "") {
        rate = 0;
    } else {
        rate = parseInt(rate);
    }

    var totamt1 = parseInt(rate);
    // alert(totamt);
    totamt.val(totamt1 * qty);

    gstcalc();
});

$(document).on("blur", ".gst", function () {
    gstcalc();
});

function gstcalc() {
    var total_price = 0;
    var gstcalc = 0;
    var tprice = 0;
    var txtcgst = 0;
    var txtsgst = 0;
    var allFieldsFilled = false; // Flag to track if any non-empty field is found
    var gstTotal = 0;

    $(".amt").each(function () {
        var value = $(this).val();
        if (value.trim() !== "") {
            allFieldsFilled = true;
            total_price += parseInt(value);
        }
    });

    $(".gst").each(function () {
        var value = $(this).val();
        if (value.trim() !== "") {
            gstTotal += parseInt(value);
        }
    });

    if (allFieldsFilled) {
        var cgst = gstTotal / 2;
        var sgst = gstTotal / 2;

        txtcgst = (total_price / 100) * cgst;
        txtsgst = (total_price / 100) * sgst;
        tprice = total_price.toFixed(2);
        // gstcalc = total_price / 100 * 9;
        // tprice = total_price.toFixed(2);
        // txtcgst = gstcalc.toFixed(2);
        // txtsgst = gstcalc.toFixed(2);
    }

    $(".totalsgst").val(txtsgst);
    $(".totalcgst").val(txtcgst);
    $(".totprice").val(tprice);
    $(".finalamt").val(
        parseFloat(txtsgst) + parseFloat(txtcgst) + parseFloat(tprice)
    );
    $(".wodiscounttotprice").val(
        parseFloat(txtsgst) + parseFloat(txtcgst) + parseFloat(tprice)
    );
}

// function gstcalc() {
//     var total_price = 0;
//     var gstcalc = 0;
//     var tprice = 0;
//     var txtcgst = 0;
//     var txtsgst = 0;
//     var allFieldsFilled = false; // Flag to track if any non-empty field is found

//     $(".amt").each(function() {
//         var value = $(this).val();
//         if (value.trim() !== "") {
//             allFieldsFilled = true;
//             total_price += parseInt(value);
//         }
//     });

//     if (allFieldsFilled) {
//         gstcalc = total_price / 100 * 9;
//         tprice = total_price.toFixed(2);
//         txtcgst = gstcalc.toFixed(2);
//         txtsgst = gstcalc.toFixed(2);
//     }

//     $(".totalsgst").val(txtsgst);
//     $(".totalcgst").val(txtcgst);
//     $(".totprice").val(tprice);
//     $(".finalamt").val(parseFloat(txtsgst) + parseFloat(txtcgst) + parseFloat(tprice));
//     $(".wodiscounttotprice").val(parseFloat(txtsgst) + parseFloat(txtcgst) + parseFloat(tprice));
// }

$(document).on("input", ".discount", function () {
    var discounttype = $(".discounttype").val();
    var discount = $(this).val();
    var totalsgst = $(".totalsgst").val();
    var totalcgst = $(".totalcgst").val();
    var totprice = $(".totprice").val();
    var finalamt =
        parseFloat(totalsgst) + parseFloat(totalcgst) + parseFloat(totprice);

    // var finalamt = $(".finalamt").val();

    if (discounttype == 1) {
        if (discount == "") {
            var discountcalc = parseInt(finalamt) - 0;
            $(".finalamt").val(discountcalc);
        } else {
            var discountcalc = parseInt(finalamt) - parseInt(discount);
            $(".finalamt").val(discountcalc);
            $(".discountamt").val(discount);
        }
    }

    if (discounttype == 2) {
        if (discount == "") {
            var discountcalc = parseInt(finalamt) - 0;
            $(".finalamt").val(discountcalc);
        } else {
            var percentage = (parseInt(finalamt) / 100) * discount;
            var discountcalc = parseInt(finalamt) - parseInt(percentage);
            $(".finalamt").val(discountcalc);
            $(".discountamt").val(percentage);
        }
    }
});

$(document).on("change", ".discounttype", function () {
    var discounttype = $(this).val();
    var discount = $(".discount").val();
    var totalsgst = $(".totalsgst").val();
    var totalcgst = $(".totalcgst").val();
    var totprice = $(".totprice").val();

    var finalamt =
        parseFloat(totalsgst) + parseFloat(totalcgst) + parseFloat(totprice);

    if (discounttype == 0) {
        $(".discount").val(0);
        $(".finalamt").val(finalamt);
        $(".discountamt").val(0);
    }

    if (discounttype == 1) {
        if (discount == "") {
            var discountcalc = parseInt(finalamt) - 0;
            $(".finalamt").val(discountcalc);
        } else {
            var discountcalc = parseInt(finalamt) - parseInt(discount);
            $(".finalamt").val(discountcalc);
        }
    }

    if (discounttype == 2) {
        if (discount == "") {
            var discountcalc = parseInt(finalamt) - 0;
            $(".finalamt").val(discountcalc);
        } else {
            var percentage = (parseInt(finalamt) / 100) * discount;

            var discountcalc = parseInt(finalamt) - parseInt(percentage);
            $(".finalamt").val(discountcalc);
        }
    }
});

// $(".remainderdelete").on("click", function () {
$(document).on("click", ".remainderdelete", function () {
    const id = $(this).data("id");

    // alert(text);
    Swal.fire({
        title: "Are you sure?",
        text: "You want Delete this record..!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "GET",
                url: `/deleteremainder/${id}`,
                success: function (data) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Your Reminder Deleted..",
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    location.reload();
                },
                error: function (data) {
                    Swal.fire(
                        "Error!",
                        "Your file has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
});

// $(".meetingsedit").click(function(){
$(document).on("click", ".meetingsedit", function () {
    var id = $(this).data("id");

    $.ajax({
        type: "GET",
        url: "/getmeetinginfo/" + id,
        // async: false,
        cache: false,
        contentType: false,
        processData: false,

        success: function (data) {
            $("#meetingeditModal").modal("show");
            $("#meetingdata").val(data.notes_data);
            $("#meetingid").val(data.id);
        },
    });
});

$(document).on("submit", "#updatemeeting", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/updatemeeting",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Meetings Updated..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("click", ".meetingsdelete", function () {
    // $(".meetingsdelete").on("click", function () {

    const id = $(this).data("id");

    // alert(text);
    Swal.fire({
        title: "Are you sure?",
        text: "You want Delete this record..!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "GET",
                url: `/deletemeeting/${id}`,
                success: function (data) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Your Meeting Updation Deleted..",
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    location.reload();
                },
                error: function (data) {
                    Swal.fire(
                        "Error!",
                        "Your file has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
});

$(document).on("click", ".renewalsnotesedit", function () {
    var id = $(this).data("id");

    $.ajax({
        type: "GET",
        url: "/getrenewalinfo/" + id,
        // async: false,
        success: function (data) {
            var sectionContent = $(data).find("#updaterenewalnotes").html();

            $(".modal-content").html(sectionContent);

            // Show the modal
            $("#renewaleditModal").modal("show");
            $("#renewalcallinfoedit").val(data.callinfostatus);
            $("#renewalsnotesdata").val(data.notes_data);
            $("#oldimage").val(data.filename);
            $("#renewalid").val(data.id);
        },
    });
});

$(document).on("submit", "#updaterenewalnotes", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/updaterenewalnotes",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Notes Updated..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("click", ".renewalsnotesdelete", function () {
    // $(".renewalsnotesdelete").on("click", function () {

    const id = $(this).data("id");

    // alert(text);
    Swal.fire({
        title: "Are you sure?",
        text: "You want Delete this record..!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "GET",
                url: `/deleterenewalnotes/${id}`,
                success: function (data) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Your Notes Deleted..",
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    location.reload();
                },
                error: function (data) {
                    Swal.fire(
                        "Error!",
                        "Your file has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
});

$(document).on("click", ".renewalsremaindersedit", function () {
    // $(".renewalsremaindersedit").click(function(){

    var id = $(this).data("id");

    $.ajax({
        type: "GET",
        url: "/getremainderinfo/" + id,
        // async: false,

        success: function (data) {
            $("#renewalremaindereditModal").modal("show");
            $("#reminderrenewaldata").val(data.remainder_details);

            $("#reminderdate").val(data.remainder_date);
            $("#renewalreminderid").val(data.id);
        },
    });
});

$(document).on("submit", "#updaterenewalreminder", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/updaterenewalreminder",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Reminder Updated..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("click", ".renewalsremainderdelete", function () {
    // $(".renewalsremainderdelete").on("click", function () {

    const id = $(this).data("id");

    // alert(text);
    Swal.fire({
        title: "Are you sure?",
        text: "You want Delete this record..!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "GET",
                url: `/renewalsremainderdelete/${id}`,
                success: function (data) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Your Notes Deleted..",
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    location.reload();
                },
                error: function (data) {
                    Swal.fire(
                        "Error!",
                        "Your file has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
});

$(document).on("click", ".renewalsmeetingedit", function () {
    // $(".renewalsmeetingedit").click(function(){

    var id = $(this).data("id");

    $.ajax({
        type: "GET",
        url: "/getrenewalsmeetinginfo/" + id,
        // async: false,

        success: function (data) {
            $("#renewalmeetingseditModal").modal("show");
            $("#meetingrenewaldata").val(data.notes_data);
            $("#renewalmeetingid").val(data.id);
        },
    });
});

$(document).on("submit", "#updaterenewalmeetings", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/updaterenewalmeetings",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Reminder Updated..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("click", ".renewalsmeetingdelete", function () {
    // $(".renewalsmeetingdelete").on("click", function () {

    const id = $(this).data("id");

    // alert(text);
    Swal.fire({
        title: "Are you sure?",
        text: "You want Delete this record..!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "GET",
                url: `/renewalsmeetingdelete/${id}`,
                success: function (data) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Your Meetings Deleted..",
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    location.reload();
                },
                error: function (data) {
                    Swal.fire(
                        "Error!",
                        "Your file has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
});

$(".deleteestimate").on("click", function () {
    const id = $(this).data("id");

    // alert(text);
    Swal.fire({
        title: "Are you sure?",
        text: "You want Delete this Estimate..!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "GET",
                url: `/estimatedelete/${id}`,
                success: function (data) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Your Estimate Deleted..",
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    location.reload();
                },
                error: function (data) {
                    Swal.fire(
                        "Error!",
                        "Your file has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
});

$(".shareestimate").click(function () {
    var estid = $(this).data("estid");

    $.ajax({
        type: "GET",
        url: "/getestimatedata/" + estid,
        // async: false,

        success: function (data) {
            $("#shareestimate").modal("show");
            $("#attachmentfilename").val(data.estimate_pdf_name);
            $("#estid1").val(data.est_id);
        },
    });
});

$("#responseloader").hide();

$(document).on("submit", "#sendestimate", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/sendestimate",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Mail Send Successfully..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$("#responseloader").hide();

$(document).on("submit", "#sendestimaterenewal", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/sendestimaterenewal",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Mail Send Successfully..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("click", ".addpayment", function () {
    var renewalid = $(this).data("renewalid");
    var campaignid = $(this).data("campaignid");
    var startdate = $(this).data("startdate");
    var enddate = $(this).data("enddate");
    var renewalconverttype = $(this).data("renewalconverttype");
    var leadid = $(this).data("leadid");
    var totpayment = $(this).data("totamt");
    var leadproductid = $(this).data("leadproductid");
    alert(leadid);

    $.ajax({
        type: "GET",
        url: "/getmaxamount/" + renewalid + "/" + campaignid,
        // async: false,

        success: function (data) {
            $("#addrenewalpayment").modal("show");
            $("#campaignid").val(campaignid);
            $("#totpayment").val(totpayment);
            $("#startdate").val(startdate);
            $("#enddate").val(enddate);
            $("#leadid").val(leadid);
            $("#leadproductid").val(leadproductid);
            $("#renewalconverttype").val(renewalconverttype);
            $("#pendingpayment").attr("max", data.pendingamt);
        },
    });
});

$(document).on("submit", "#updatepayment", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/renewalspaymentupdate",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Payment Added Successfully..",
                showConfirmButton: false,
                timer: 1500,
            });

            if (data == "single-outstanding") {
                window.location.href = "/outstanding";
            } else {
                location.reload();
            }
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("click", ".getpaymenthistory", function () {
    var renewalid = $(this).data("renewalid");
    var startdate = $(this).data("startdate");
    var enddate = $(this).data("enddate");
    var campaignid = $(this).data("campaignid");
    $.ajax({
        type: "GET",
        // url: "/getpoproductdetails/"+poid,
        url: "/renewalpaymenthistory/" + renewalid + "/" + campaignid,
        success: function (data) {
            $("#renewalpaymenthistory").modal("show");

            $(".paymentinfo tbody").empty();
            $.each(data, function (key, item) {
                $(".paymentinfo tbody").append(
                    "<tr>\
                    <td>" +
                        item.paymentdate +
                        "</td>\
                    <td>" +
                        item.receivedamt +
                        "</td>\
                 </tr>"
                );
            });
        },
    });
});

$(".campaignstop").on("click", function () {
    const id = $(this).data("id");
    const campaigntype = $(this).data("campaigntype");
    // alert(text);
    Swal.fire({
        title: "Are you sure?",
        text: "You want Stop the Campaign..!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Stop!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "GET",
                url: `/campaignstop/${id}`,
                success: function (data) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Your Campaign Stopped..",
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    location.reload();
                },
                error: function (data) {
                    Swal.fire("Error!", "success");
                },
            });
        }
    });
});

$(document).on("click", ".campaignrestartstop", function () {
    var renewalid = $(this).data("id");
    var campaignid = $(this).data("campaignid");

    $("#campaignrestart").modal("show");
    $("#campid").val(campaignid);
});

$(document).on("click", ".createcampaign", function () {
    var renewalid = $(this).data("id");

    $("#createcampaign").modal("show");
});

$(document).on("keyup", ".receivedamt", function () {
    var receivedamt = $(this).val();
    var renewalamt = $(".renewalamt").val();
    var pendingamt = $(".pendingamt").val();

    $(".pendingamt").val(renewalamt - receivedamt);
});

$(document).on("keyup", ".campaigncharge", function () {
    var campaigncharge = parseInt($(this).val());
    // alert(campaigncharge);
    var servicecharge = parseInt($(".servicecharge").val());
    // alert(servicecharge);

    // alert(totalAmount);
    // if(totalAmount == 'NaN')
    // {
    //     $(".campaignrenewalamt").val(0);
    // }
    // else
    // {
    //     $(".campaignrenewalamt").val(totalAmount);
    // }

    if (isNaN(campaigncharge)) {
        campaigncharge = 0; // Set default value to 0 if parsing fails
    }

    if (isNaN(servicecharge)) {
        servicecharge = 0; // Set default value to 0 if parsing fails
    }
    var totalAmount = campaigncharge + servicecharge;
    $(".campaignrenewalamt").val(totalAmount);
});

$(document).on("keyup", ".campaigncharge2", function () {
    var campaigncharge = parseInt($(this).val());
    // alert(campaigncharge);
    var servicecharge = parseInt($(".servicecharge2").val());
    // alert(servicecharge);

    // alert(totalAmount);
    // if(totalAmount == 'NaN')
    // {
    //     $(".campaignrenewalamt2").val(0);
    // }
    // else
    // {
    //     $(".campaignrenewalamt2").val(totalAmount);
    // }

    if (isNaN(campaigncharge)) {
        campaigncharge = 0; // Set default value to 0 if parsing fails
    }

    if (isNaN(servicecharge)) {
        servicecharge = 0; // Set default value to 0 if parsing fails
    }

    var totalAmount = campaigncharge + servicecharge;
    $(".campaignrenewalamt2").val(totalAmount);
    $(".campaignpendingamt2").val(totalAmount);
});

$(document).on("keyup", ".servicecharge", function () {
    var campaigncharge = parseInt($(this).val());
    // alert(campaigncharge);
    var servicecharge = parseInt($(".campaigncharge").val());

    if (isNaN(campaigncharge)) {
        campaigncharge = 0; // Set default value to 0 if parsing fails
    }

    if (isNaN(servicecharge)) {
        servicecharge = 0; // Set default value to 0 if parsing fails
    }
    var totalAmount = campaigncharge + servicecharge;

    $(".campaignrenewalamt").val(totalAmount);

    console.log(totalAmount);
});

$(document).on("keyup", ".servicecharge2", function () {
    var campaigncharge = parseInt($(this).val());
    // alert(campaigncharge);
    var servicecharge = parseInt($(".campaigncharge2").val());

    if (isNaN(campaigncharge)) {
        campaigncharge = 0; // Set default value to 0 if parsing fails
    }

    if (isNaN(servicecharge)) {
        servicecharge = 0; // Set default value to 0 if parsing fails
    }
    var totalAmount = campaigncharge + servicecharge;

    $(".campaignrenewalamt2").val(totalAmount);
    $(".campaignpendingamt2").val(totalAmount);

    console.log(totalAmount);
});

$(document).on("keyup", ".campaignreceivedamt", function () {
    var receivedamt = $(this).val();
    var renewalamt = $(".campaignrenewalamt").val();
    var pendingamt = $(".campaignpendingamt").val();

    $(".campaignpendingamt").val(renewalamt - receivedamt);
});

$(document).on("input", ".campaignreceivedamt2", function () {
    var receivedamt = $(this).val();
    var renewalamt = $(".campaignrenewalamt2").val();
    var pendingamt = $(".campaignpendingamt2").val();
    // alert(renewalamt);
    $(this).prop("max", renewalamt);
    $(".campaignpendingamt2").val(renewalamt - receivedamt);
});

$(document).on("keyup", ".campaignrenewalamtdesign", function () {
    var campaignrenewalamtdesign = $(this).val();
    $(".campaignpendingamtdesign").val(campaignrenewalamtdesign);
});

$(document).on("input", ".campaignreceivedamtdesign", function () {
    var receivedamt = $(this).val();
    var renewalamt = $(".campaignrenewalamtdesign").val();
    var pendingamt = $(".campaignpendingamtdesign").val();

    $(this).prop("max", renewalamt);

    $(".campaignpendingamtdesign").val(renewalamt - receivedamt);
});

$(document).on("input", ".campaignreceivedamtcrm", function () {
    var receivedamt = $(this).val();
    var renewalamt = $(".campaignrenewalamtcrm").val();
    var pendingamt = $(".campaignpendingamtcrm").val();

    $(this).prop("max", renewalamt);
    $(".campaignpendingamtcrm").val(renewalamt - receivedamt);
});

$(document).on("keyup", ".campaignrenewalamtcrm", function () {
    var campaignrenewalamtcrm = $(this).val();
    $(".campaignpendingamtcrm").val(campaignrenewalamtcrm);
});

$(document).on("keyup", ".campaignrenewalamtseo", function () {
    var campaignrenewalamtseo = $(this).val();
    $(".campaignpendingamtseo").val(campaignrenewalamtseo);
});

$(document).on("keyup", ".campaignreceivedamtseo", function () {
    var receivedamt = $(this).val();
    var renewalamt = $(".campaignrenewalamtseo").val();
    var pendingamt = $(".campaignpendingamtseo").val();

    $(this).prop("max", renewalamt);
    $(".campaignpendingamtseo").val(renewalamt - receivedamt);
});

// ----------------

$(document).on("keyup", ".receivedamtedit", function () {
    var receivedamt = $(this).val();
    var renewalamt = $(".renewalamtedit").val();
    var pendingamt = $(".pendingamtedit").val();

    $(".pendingamt").val(renewalamt - receivedamt);
});

$(document).on("input", ".campaignchargeedit", function () {
    var campaigncharge = parseInt($(this).val());

    var servicecharge = parseInt($(".servicechargeedit").val());

    var receivedamt = parseInt($(".campaignreceivedamtedit").val());

    var totalAmount = campaigncharge + servicecharge;

    if (totalAmount == "NaN") {
        $(".campaignrenewalamtedit").val(0);
    } else {
        $(".campaignrenewalamtedit").val(totalAmount);
        $(".campaignpendingamtedit").val(totalAmount);
    }
});

$(document).on("input", ".servicechargeedit", function () {
    var campaigncharge = parseInt($(this).val());

    var servicecharge = parseInt($(".campaignchargeedit").val());

    var totalAmount = campaigncharge + servicecharge;

    if (isNaN(campaigncharge)) {
        campaigncharge = 0; // Set default value to 0 if parsing fails
    }

    if (isNaN(servicecharge)) {
        servicecharge = 0; // Set default value to 0 if parsing fails
    }

    $(".campaignrenewalamtedit").val(totalAmount);
});

$(document).on("keyup", ".campaignreceivedamtedit", function () {
    var receivedamt = $(this).val();
    var renewalamt = $(".campaignrenewalamtedit").val();
    var pendingamt = $(".campaignpendingamtedit").val();

    $(".campaignpendingamtedit").val(renewalamt - receivedamt);
});

$(document).on("keyup", ".campaignreceivedamtdesignedit", function () {
    var receivedamt = $(this).val();
    var renewalamt = $(".campaignrenewalamtdesignedit").val();
    var pendingamt = $(".campaignpendingamtdesignedit").val();

    $(this).prop("max", renewalamt);
    $(".campaignpendingamtdesignedit").val(renewalamt - receivedamt);
});

// --------------

$(document).on("submit", "#restartrenewal", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/renewalrestart",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Campaign Restart Successfully..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("click", ".campaignpausedays", function () {
    var renewalid = $(this).data("renewalid");

    $.ajax({
        type: "GET",
        // url: "/getpoproductdetails/"+poid,
        url: "/getcampaignpausedays/" + renewalid,
        success: function (data) {
            $("#getcampaignpausedays").modal("show");

            $(".campaignpausedays1 tbody").html("");
            $.each(data, function (key, item) {
                $(".campaignpausedays1 tbody").append(
                    "<tr>\
                    <td>" +
                        item.campaign_pausedate +
                        "</td>\
                    <td>" +
                        item.campaign_resumedate +
                        "</td>\
                    <td>" +
                        item.campaign_pausedays +
                        "</td>\
                 </tr>"
                );
            });
        },
    });
});

$(".autorenewalmain").on("click", function () {
    var checkbox = $(this).prev('input[type="checkbox"]');
    var label = $(this).siblings('label[for="' + checkbox.attr("id") + '"]');
    var renewaltypehiddenmain = $(".renewaltypehiddenmain");

    // $('#flexSwitchCheckChecked').prop('checked', true).prop('disabled', true);

    if (checkbox.prop("checked", true)) {
        label.text("Auto Renewal Disable");
        renewaltypehiddenmain.val("2");
    } else {
        label.text("Auto Renewal Enable");
        renewaltypehiddenmain.val("1");
    }
});

$(".form-check-input.autorenewalmain").on("change", function () {
    var checkbox = $(this);
    var label = $('label[for="' + checkbox.attr("id") + '"]');
    var renewaltypehidden = $(".renewaltypehiddenmain");
    if (checkbox.prop("checked")) {
        label.text("Auto Renewal Enable");
        renewaltypehidden.val("1");
    } else {
        label.text("Auto Renewal Disable");
        renewaltypehidden.val("2");
    }
});

$(".autorenewaledit").on("click", function () {
    var checkbox = $(this).prev('input[type="checkbox"]');
    var label = $(this).siblings('label[for="' + checkbox.attr("id") + '"]');
    var renewaltypehidden = $(".renewaltypehiddenedit");

    // $('#flexSwitchCheckChecked').prop('checked', true).prop('disabled', true);

    if (checkbox.prop("checked", true)) {
        label.text("Auto Renewal Disable");
        renewaltypehidden.val("2");
    } else {
        label.text("Auto Renewal Enable");
        renewaltypehidden.val("1");
    }
});

$(".form-check-input.autorenewaledit").on("change", function () {
    var checkbox = $(this);
    var label = $('label[for="' + checkbox.attr("id") + '"]');
    var renewaltypehidden = $(".renewaltypehiddenedit");
    if (checkbox.prop("checked")) {
        label.text("Auto Renewal Enable");
        renewaltypehidden.val("1");
    } else {
        label.text("Auto Renewal Disable");
        renewaltypehidden.val("2");
    }
});

$("#pendingpayment").on("keydown", function (event) {
    if (event.key === "-") {
        event.preventDefault();
    }

    if (event.key === "ArrowDown") {
        event.preventDefault();
    }

    if (event.key === "ArrowUp") {
        event.preventDefault();
    }
});

$(".logoutclick").on("click", function () {
    // alert(text);
    Swal.fire({
        title: "Are you sure?",
        text: "You want to Logout..!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Logout!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "GET",
                url: `/logout`,
                success: function (data) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "User Logout Successfully..",
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    window.location.href = "/";
                },
            });
        }
    });
});

$(document).on("click", ".logoutclick2", function () {
    Swal.fire({
        title: "Are you sure?",
        text: "You want to Logout..!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Logout!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "GET",
                url: `/logout`,
                success: function (data) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "User Logout Successfully..",
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    window.location.href = "/";
                },
            });
        }
    });
});

function panvalidation(input) {
    var lettersRegex = /^([A-Z]{5})(\d{4})([A-Z]{1})$/;

    var name = $(input).val();
    var out = lettersRegex.test(name);

    if (name.length != 0) {
        if (out == false) {
            $("#myalert").removeClass("d-none");
            $(".error-text").text("PAN No Format Error");
            // alert("Letters only allowed don't use numbers & special char");
            $(input).focus();
        } else {
            $("#name-error").text("");
            $("#myalert").addClass("d-none");
        }
    } else {
        $("#name-error").text("");
        $("#myalert").addClass("d-none");
    }
}

function bankaccountvalidation(input) {
    var lettersRegex = /^\d{9,}$/;

    var name = $(input).val();
    var out = lettersRegex.test(name);

    if (name.length != 0) {
        if (out == false) {
            $("#myalert").removeClass("d-none");
            $(".error-text").text("Bank Account No Format Error");
            // alert("Letters only allowed don't use numbers & special char");
            $(input).focus();
        } else {
            $("#name-error").text("");
            $("#myalert").addClass("d-none");
        }
    } else {
        $("#name-error").text("");
        $("#myalert").addClass("d-none");
    }
}

function bankifscvalidation(input) {
    var lettersRegex = /^[A-Z]{4}[0][A-Z0-9]{6}$/;

    var name = $(input).val();
    var out = lettersRegex.test(name);

    if (name.length != 0) {
        if (out == false) {
            $("#myalert").removeClass("d-none");
            $(".error-text").text("Bank IFSC Format Error");
            // alert("Letters only allowed don't use numbers & special char");
            $(input).focus();
        } else {
            $("#name-error").text("");
            $("#myalert").addClass("d-none");
        }
    } else {
        $("#name-error").text("");
        $("#myalert").addClass("d-none");
    }
}

$("#chooseform").change(function () {
    var chooseform = $(this).val();
    $("#subgroup").val(chooseform);
});

$(".deletefield").on("click", function () {
    const id = $(this).data("formid");
    const categoryid = $(this).data("catid");
    const subcatid = $(this).data("subcategoryid");
    const formtext = $(this).data("formtext");

    Swal.fire({
        title: "Are you sure?",
        text: "You want to delete this Column..!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "GET",
                url: `deletecolumn/${id}/${categoryid}/${subcatid}/${formtext}`,
                success: function (data) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Your Column has beed Deleted..",
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    location.reload();
                },
                error: function (data) {
                    Swal.fire(
                        "Deleted!",
                        "Your file has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
});

$(document).on("keyup", ".ReceivedCost", function () {
    var receivedCost = parseFloat($(this).val());
    var totalCost = parseFloat($(".TotalCost").val());

    if (receivedCost > totalCost) {
        // alert("Received cost is greater than total cost.");

        $("#myalert").removeClass("d-none");
        $(".error-text").text("Received cost is greater than total cost.");
        // alert("Letters only allowed don't use numbers & special char");
        $(".ReceivedCost").prop("max", totalCost);
        $(".ReceivedCost").focus();
    } else {
        // $(".ReceivedCost").attr("max",false);
        $(".error-text").text("");
    }
});

$(document).on("keyup", ".ExpectedAmount", function () {
    var receivedCost = parseFloat($(this).val());
    var totalCost = parseFloat($(".TotalCost").val());

    if (receivedCost > totalCost) {
        // alert("Received cost is greater than total cost.");

        $("#myalert").removeClass("d-none");
        $(".error-text").text("Expected cost is greater than total cost.");
        // alert("Letters only allowed don't use numbers & special char");
        $(".ExpectedAmount").prop("max", totalCost);
        $(".ExpectedAmount").focus();
    } else {
        // $(".ExpectedAmount").prop("max",false);
        $(".error-text").text("");
    }
});

$(document).on("blur", ".ReceivedCost", function () {
    var receivedCost = parseFloat($(this).val());
    var totalCost = parseFloat($(".TotalCost").val());

    if (receivedCost > totalCost) {
        // alert("Received cost is greater than total cost.");

        $("#myalert").removeClass("d-none");
        $(".error-text").text("Received cost is greater than total cost.");
        // alert("Letters only allowed don't use numbers & special char");
        $(".ReceivedCost").prop("max", totalCost);
        $(".ReceivedCost").focus();
    } else {
        // $(".ReceivedCost").prop("max",false);
        $(".error-text").text("");
    }
});

$(document).on("keyup", ".leadproducttotalcostedit", function () {
    var totalcost = parseInt($(this).val());

    var receivedCost = parseInt($(".leadproductreceivedcostedit").val());
});

$(document).on("blur", ".leadproductreceivedcostedit ", function () {
    var receivedCost = parseInt($(this).val());
    var totalCost = parseInt($(".leadproducttotalcostedit").val());

    if (receivedCost > totalCost) {
        // alert("Received cost is greater than total cost.");

        $("#myalert").removeClass("d-none");
        $(".error-text2").text("Received cost is greater than total cost.");
        // alert("Letters only allowed don't use numbers & special char");
        // $(".leadproductreceivedcostedit").prop("max",totalCost);
        $(".leadproductreceivedcostedit").focus();
    } else {
        // $(".leadproductreceivedcostedit").prop("max",false);
        $(".error-text2").text("");
    }
});

$(document).on("blur", ".ExpectedAmount ", function () {
    var ExpectedAmount = parseInt($(this).val());
    var totalCost = parseInt($(".TotalCost ").val());

    if (ExpectedAmount > totalCost) {
        // alert("Received cost is greater than total cost.");

        $("#myalert").removeClass("d-none");
        $(".error-text3").text("Expected cost is greater than total cost.");
        // alert("Letters only allowed don't use numbers & special char");
        $(".ExpectedAmount").prop("max", totalCost);
        $(".ExpectedAmount").focus();
    } else {
        // $(".ExpectedAmount").prop("max",false);
        $(".error-text3").text("");
    }
});

$(document).on("blur", ".expectedcostedit ", function () {
    var ExpectedAmount = parseInt($(this).val());
    var totalCost = parseInt($(".leadproducttotalcostedit").val());

    if (ExpectedAmount > totalCost) {
        // alert("Received cost is greater than total cost.");

        $("#myalert").removeClass("d-none");
        $(".error-text3").text("Expected cost is greater than total cost.");
        // alert("Letters only allowed don't use numbers & special char");
        $(".expectedcostedit").prop("max", totalCost);
        $(".expectedcostedit").focus();
    } else {
        // $(".expectedcostedit").prop("max",false);
        $(".error-text3").text("");
    }
});

// ----------------

$(document).on("keyup", ".ReceivedCostedit", function () {
    var receivedCost = parseFloat($(this).val());
    var totalCost = parseFloat($(".TotalCostedit").val());

    if (receivedCost > totalCost) {
        // alert("Received cost is greater than total cost.");

        $("#myalert").removeClass("d-none");
        $(".error-text").text("Received cost is greater than total cost.");
        // alert("Letters only allowed don't use numbers & special char");
        $(".ReceivedCostedit").focus();
    }
});

$(document).on("keyup", ".campaignrenewalamtdesignedit", function () {
    var campaignrenewalamtdesignedit = $(this).val();
    $(".campaignpendingamtdesignedit").val(campaignrenewalamtdesignedit);
});

$(document).on("blur", ".ReceivedCostedit", function () {
    var receivedCost = parseFloat($(this).val());
    var totalCost = parseFloat($(".TotalCostedit").val());

    if (receivedCost > totalCost) {
        $("#myalert").removeClass("d-none");
        $(".error-text").text("Received cost is greater than total cost.");
        // alert("Letters only allowed don't use numbers & special char");
        $(".ReceivedCostedit").focus();
    }
});

// --------------

$(document).on("submit", "#newcampaigncreate", function (event) {
    event.preventDefault();
    var d = new FormData(this);
    var postersCount = d.get("posterscount");
    var videocount = d.get("videocount");
    var reelscount = d.get("reelscount");

    if (postersCount == "" && videocount == "" && reelscount == "") {
        $(".designsubmitalert").text("Please Fill any Designs Count");
    } else {
        $.ajax({
            type: "POST",
            url: "/newcampaigncreate",
            data: new FormData(this),
            // async: false,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $("#responseloader").fadeIn();
            },
            success: function (data) {
                $("#responseloader").fadeOut();
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Campaign Created Successfully..",
                    showConfirmButton: false,
                    timer: 1500,
                });

                location.reload();
            },
            error: function (data) {
                Swal.fire("Error!", "Error.", "success");
            },
        });
    }
});

//Social floating button
$(document).on("click", ".main-btn", function () {
    // $(this).closest('.float-btn').find('ul').toggleClass('toggled');

    var clickedBtn = $(this);
    var closestFloatBtn = clickedBtn.closest(".float-btn");
    var clickedList = closestFloatBtn.find("ul");

    // Toggle the list associated with the clicked button
    clickedList.toggleClass("toggled");

    // Hide other lists
    $(".float-btn").not(closestFloatBtn).find("ul").removeClass("toggled");
});

$(document).on("click", function (event) {
    var target = $(event.target);
    if (!target.closest(".float-btn").length) {
        $(".float-btn ul").removeClass("toggled");
    }
});

$(document).on("click", ".getcampaigninfo", function () {
    var campaignid = $(this).data("campaignid");
    var renewalid = $(this).data("renewalid");

    $.ajax({
        type: "GET",
        // url: "/getpoproductdetails/"+poid,
        url: "/getcampaigninfo/" + campaignid,
        success: function (data) {
            $("#getcampaigninfomodal").modal("show");

            $("#campaignname").text(data.campaignname);
            $("#campaignstartdate").text(data.campaignstartdate);
            $("#campaignenddate").text(data.campaignenddate);
            $("#tenure").text(data.tenure);
            $("#renewaldate").text(data.renewaldate);
            $("#renewalamount").text(data.renewalamount);
            $("#receivedamount").text(data.receivedamount);
            $("#pendingamount").text(data.pendingamount);
            if (data.renewaltype == 1) {
                $("#renewaltype").text("Auto");
            } else {
                $("#renewaltype").text("Manual");
            }
            $("#campaignproduct").text(data.productname);
            $("#campaigncharge").text(data.campaigncharge);
            $("#service_charge").text(data.service_charge);
            $("#campaign_status").text(data.campaign_status);
            $("#campaign_posters").text(data.posterscount);
            $("#campaign_reels").text(data.reelscount);
            $("#campaign_video").text(data.videocount);
            $("#campaign_seoperiod").text(data.seocrmperiod);
            $("#campaign_crmuserscount").text(data.crmuserscount);
            $("#remarks").text(data.remarks);
            // $('.campaignpausedays1 tbody').html("");
            // $.each(data,function(key, item){
            //     $('.campaignpausedays1 tbody').append(
            //         '<tr>\
            //         <td>'+item.campaign_pausedate+'</td>\
            //         <td>'+item.campaign_resumedate+'</td>\
            //         <td>'+item.campaign_pausedays+'</td>\
            //      </tr>'
            //     )

            // });
        },
    });

    $.ajax({
        type: "GET",
        // url: "/getpoproductdetails/"+poid,
        url: "/renewalpaymenthistory/" + renewalid + "/" + campaignid,
        success: function (data) {
            // $('.paymentinfo tbody').html("");
            $(".paymentinfo1 tbody").empty();
            $.each(data, function (key, item) {
                $(".paymentinfo1 tbody").append(
                    "<tr>\
                    <td>" +
                        item.created_at +
                        "</td>\
                    <td>" +
                        item.receivedamt +
                        "</td>\
                 </tr>"
                );
            });
        },
    });

    $.ajax({
        type: "GET",
        // url: "/getpoproductdetails/"+poid,
        url: "/getcampaignpausedays/" + campaignid,
        success: function (data) {
            $(".campaignpausedays1 tbody").html("");
            $.each(data, function (key, item) {
                $(".campaignpausedays1 tbody").append(
                    "<tr>\
                    <td>" +
                        item.campaign_pausedate +
                        "</td>\
                    <td>" +
                        item.campaign_resumedate +
                        "</td>\
                    <td>" +
                        item.campaign_pausedays +
                        "</td>\
                 </tr>"
                );
            });
        },
    });

    $.ajax({
        type: "GET",
        // url: "/getpoproductdetails/"+poid,
        url: "/campaignhistory/" + campaignid,
        success: function (data) {
            $(".campaignhistory tbody").html("");
            $.each(data, function (key, item) {
                $(".campaignhistory tbody").append(
                    "<tr>\
                    <td>" +
                        item.campaignstartdate +
                        "</td>\
                    <td>" +
                        item.campaignenddate +
                        "</td>\
                    <td>" +
                        item.tenure +
                        "</td>\
                    <td>" +
                        item.renewaldate +
                        "</td>\
                 </tr>"
                );
            });
        },
    });

    $.ajax({
        type: "GET",
        // url: "/getpoproductdetails/"+poid,
        url: "/returnrefundinfo/" + campaignid,
        success: function (data) {
            $(".refundhistory tbody").html("");
            $.each(data, function (key, item) {
                $(".refundhistory tbody").append(
                    "<tr>\
                    <td>" +
                        item.refund_date +
                        "</td>\
                    <td>" +
                        item.refundamt +
                        "</td>\
                 </tr>"
                );
            });
        },
    });
});

$(document).on("click", ".editcampaign1", function () {
    var campaignid = $(this).data("campaignid");
    var renewalid = $(this).data("renewalid");
    var leadid = $(this).data("leadid");

    // alert(leadid);
    $.ajax({
        type: "GET",
        // url: "/getpoproductdetails/"+poid,
        url: "/editcampaigns/" + campaignid,
        success: function (data) {
            $("#editcampaignmodal").modal("show");
            $("#product").val(data.campaignproduct);
            $("#campaignstartdate1").val(data.campaignstartdate);
            $("#campaignenddate1").val(data.campaignenddate);
            $("#tenture").val(data.tenure);
            $("#campaignrenewaldate1").val(data.campaignenddate);
            $("#renewaltype").val(data.renewaltype);

            if (data.renewaltype == "1") {
                $("#Renewalcheckbox").prop("checked", true);
            } else {
                $("#Renewalcheckbox").prop("checked", false);
            }

            $("#campaigncharge1").val(data.campaigncharge);
            $("#servicecharge").val(data.service_charge);
            $("#campaignrenewalamt").val(data.renewalamount);
            $("#campaignreceivedamt").val(data.receivedamount);
            $("#campaignpendingamt").val(data.pendingamount);
            $("#campaignname1").val(data.campaignname);
            $("#campaigneditid").val(campaignid);
            $("#outstandingexpectdate1").val(data.expecteddate);
            $("#paymentdate1").val(data.paymentdate);

            if (leadid != "") {
                $(".leadid").val(leadid);
                $(".campaignrenewalamtedit").prop("readonly", true);
                $(".campaignreceivedamtedit").prop("readonly", true);
                $(".splitinstruction").text(
                    "Please Split Amount " + data.renewalamount
                );
                $("#campaigncharge1").removeClass("campaignchargeedit");
                $("#servicecharge").removeClass("servicechargeedit");

                if (data.pendingamount == 0) {
                    $(".renewalallexpecteddate9").prop("required", false);
                    $(".requiredalert9").hide();
                }
            }
        },
    });
});

$(document).on("submit", "#newcampaignupdate", function () {
    var campaignid = $("#campaignid").val();
    var formData = new FormData(this);
    var leadid = formData.get("leadid");

    if (leadid != "") {
        var campcharge = formData.get("campaigncharge");
        var servicecharge = formData.get("servicecharge");
        var renewalamount = formData.get("renewalamount");

        var totcharge = parseInt(campcharge) + parseInt(servicecharge);

        if (totcharge == renewalamount) {
            $.ajax({
                type: "POST",
                url: "/newcampaignupdate",
                data: new FormData(this),
                // async: false,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $("#responseloader").fadeIn();
                },
                success: function (data) {
                    $("#responseloader").fadeOut();
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Campaign Update Successfully..",
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    location.reload();
                },
                error: function (data) {
                    Swal.fire("Error!", "Error.", "success");
                },
            });
        } else {
            $(".updateerror").text("Please check campaign amount");
        }
    } else {
        $.ajax({
            type: "POST",
            url: "/newcampaignupdate",
            data: new FormData(this),
            // async: false,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $("#responseloader").fadeIn();
            },
            success: function (data) {
                $("#responseloader").fadeOut();
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Campaign Update Successfully..",
                    showConfirmButton: false,
                    timer: 1500,
                });

                location.reload();
            },
            error: function (data) {
                Swal.fire("Error!", "Error.", "success");
            },
        });
    }
});

$(document).on("submit", "#saveadvancepayments", function () {
    var renewalid = $("#renewalid").val();
    $.ajax({
        type: "POST",
        url: "/saveadvancepayments",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Advance Payment Successfully..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("change", "#advancepaymentcheck", function () {
    var advancepayment = $("#advancepayment").val();
    // alert(advancepayment);
    if ($(this).is(":checked")) {
        $(".campaignreceivedamt2").val(
            parseInt($(".campaignreceivedamt2").val()) +
                parseInt(advancepayment)
        );
    } else {
        $(".campaignreceivedamt2").val(
            parseInt($(".campaignreceivedamt2").val()) -
                parseInt(advancepayment)
        );
    }
});

$(document).on("change", "#advancepaymentcheckcampaigndesign", function () {
    var advancepayment = $("#advancepaymentcampaigndesign").val();
    // alert(advancepayment);
    if ($(this).is(":checked")) {
        $(".campaignreceivedamtdesign").val(
            parseInt($(".campaignreceivedamtdesign").val()) +
                parseInt(advancepayment)
        );
    } else {
        $(".campaignreceivedamtdesign").val(
            parseInt($(".campaignreceivedamtdesign").val()) -
                parseInt(advancepayment)
        );
    }
});

$(document).on("change", "#advancepaymentcheckcampaignseo", function () {
    var advancepayment = $("#advancepaymentcampaignseo").val();
    // alert(advancepayment);
    if ($(this).is(":checked")) {
        $(".campaignreceivedamtseo").val(
            parseInt($(".campaignreceivedamtseo").val()) +
                parseInt(advancepayment)
        );
    } else {
        $(".campaignreceivedamtseo").val(
            parseInt($(".campaignreceivedamtseo").val()) -
                parseInt(advancepayment)
        );
    }
});

$(document).on("change", "#advancepaymentcheckcampaigncrm", function () {
    var advancepayment = $("#advancepaymentcampaigncrm").val();
    // alert(advancepayment);
    if ($(this).is(":checked")) {
        $(".campaignreceivedamtcrm").val(
            parseInt($(".campaignreceivedamtcrm").val()) +
                parseInt(advancepayment)
        );
    } else {
        $(".campaignreceivedamtcrm").val(
            parseInt($(".campaignreceivedamtcrm").val()) -
                parseInt(advancepayment)
        );
    }
});

$(document).on(
    "change",
    "#advancepaymentcheckextendcampaigndesign",
    function () {
        var advancepayment = $("#advancepaymentcampaigndesign").val();
        // alert(advancepayment);
        if ($(this).is(":checked")) {
            $(".campaignextendreceivedamtdesign").val(
                parseInt($(".campaignextendreceivedamtdesign").val()) +
                    parseInt(advancepayment)
            );
        } else {
            $(".campaignextendreceivedamtdesign").val(
                parseInt($(".campaignextendreceivedamtdesign").val()) -
                    parseInt(advancepayment)
            );
        }
    }
);

$(document).on("change", "#advancepaymentcheckextendcampaigncrm", function () {
    var advancepayment = $("#advancepaymentextendcrm").val();
    // alert(advancepayment);
    if ($(this).is(":checked")) {
        $(".extendcampaignreceivedamtcrm").val(
            parseInt($(".extendcampaignreceivedamtcrm").val()) +
                parseInt(advancepayment)
        );
    } else {
        $(".extendcampaignreceivedamtcrm").val(
            parseInt($(".extendcampaignreceivedamtcrm").val()) -
                parseInt(advancepayment)
        );
    }
});

$(document).on("change", "#advancepaymentcheckextendcampaignseo", function () {
    var advancepayment = $("#advancepaymentextendseo").val();
    // alert(advancepayment);
    if ($(this).is(":checked")) {
        $(".extendcampaignreceivedamtseo").val(
            parseInt($(".extendcampaignreceivedamtseo").val()) +
                parseInt(advancepayment)
        );
    } else {
        $(".extendcampaignreceivedamtseo").val(
            parseInt($(".extendcampaignreceivedamtseo").val()) -
                parseInt(advancepayment)
        );
    }
});

$(document).on("change", "#advancepaymentcheckcampaignextent", function () {
    var advancepayment = $("#advancepaymentcampaignextend").val();
    // alert(advancepayment);
    if ($(this).is(":checked")) {
        $(".campaignreceivedamtextend").val(
            parseInt($(".campaignreceivedamtextend").val()) +
                parseInt(advancepayment)
        );
    } else {
        $(".campaignreceivedamtextend").val(
            parseInt($(".campaignreceivedamtextend").val()) -
                parseInt(advancepayment)
        );
    }
});

$(document).on("change", "#advancepaymentcheckrestart", function () {
    var advancepayment = $("#advancepaymentrestart").val();

    if ($(this).is(":checked")) {
        $(".campaignreceivedamt").val(
            parseInt($(".campaignreceivedamt").val()) + parseInt(advancepayment)
        );
    } else {
        $(".campaignreceivedamt").val(
            parseInt($(".campaignreceivedamt").val()) - parseInt(advancepayment)
        );
    }
});

$(document).on("change", "#advancepaymentrenewalcheck", function () {
    var advancepayment1 = $("#advancepaymentrenewal").val();
    if (advancepayment1 != "") {
        if ($(this).is(":checked")) {
            $(".Renewalpaymentadvance1").val(
                parseInt($(".Renewalpaymentadvance1").val()) +
                    parseInt(advancepayment1)
            );
        } else {
            $(".Renewalpaymentadvance1").val(
                parseInt($(".Renewalpaymentadvance1").val()) -
                    parseInt(advancepayment1)
            );
        }
    }
});

$(document).on("change", "#advanceproductpaymentrenewalcheck", function () {
    var advancepayment1 = $("#advancepaymentproductrenewal").val();
    if (advancepayment1 != "") {
        if ($(this).is(":checked")) {
            $(".productpendingpayment").val(
                parseInt($(".productpendingpayment").val()) +
                    parseInt(advancepayment1)
            );
        } else {
            $(".productpendingpayment").val(
                parseInt($(".productpendingpayment").val()) -
                    parseInt(advancepayment1)
            );
        }
    }
});

$("#refundsavebtn").hide();
$(document).on("click", ".getrefundinfo", function () {
    var campaignid = $(this).data("campaignid");
    var renewalid = $(this).data("renewalid");

    $.ajax({
        type: "GET",
        // url: "/getpoproductdetails/"+poid,
        url: "/getrefundinfo/" + campaignid,
        success: function (data) {
            $("#refundmodel").modal("show");

            $("#refundcampaigncharge").val(data.campaigncharge);
            $("#refundservicecharge").val(data.service_charge);
            $("#refundtotalcharge").val(data.renewalamount);
            $("#refundreceivedcharge").val(data.receivedamount);
            $("#refundpendingcharge").val(data.pendingamount);
            $("#completeddesigncount").val(data.pendingamount);
            $("#pendingdesigncount").val(data.pendingamount);
            $("#totaldesigncount").val(data.pendingamount);
            if (data.refund_amount == 0 || data.refund_amount == null) {
                $("#refundrefundcharge").val();
                $("#refundsavebtn").hide();
            } else {
                $("#refundsavebtn").show();
                $("#refundrefundcharge").val(data.refund_amount);
            }

            $("#renewalidrefund").val(data.renewalid);
            $("#refundrefundcharge").attr("max", data.refund_amount);
            $("#campaignidrefund").val(data.id);

            if (data.refund_status == 0) {
                $("#refundstatus").text("Refund Not Approved");
                $("#refundsavebtn").hide();
            } else {
                $("#refundstatus").text("Refund Approved");
                $(".approverefund").hide();
                $("#refundsavebtn").show();
            }
        },
    });

    $(document).on("submit", "#refundamount", function () {
        var renewalid = $("#renewalid").val();
        $.ajax({
            type: "POST",
            url: "/saverefunds",
            data: new FormData(this),
            // async: false,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $("#responseloader").fadeIn();
            },
            success: function (data) {
                $("#responseloader").fadeOut();
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Refund added..",
                    showConfirmButton: false,
                    timer: 1500,
                });

                location.reload();
            },
            error: function (data) {
                Swal.fire("Error!", "Error.", "success");
            },
        });
    });
});

$(function () {
    var $tableContainer = $(".tabledrag");
    var counter = 0;

    $tableContainer.on("dblclick", function () {
        counter++;

        if (counter % 2 === 1) {
            $tableContainer.draggable({
                axis: "x", // Enable dragging only along the horizontal axis
            });
            $(".tabledrag").css("cursor", "all-scroll");
        } else {
            $tableContainer.draggable("destroy");
            $(".tabledrag").css("cursor", "auto");
        }
    });
});

$(document).on("click", ".addleadpayment", function () {
    var totpayment = $(this).data("totpayment");
    var receivedcost = $(this).data("receivedcost");
    var pendingcost = $(this).data("pendingcost");

    $("#addpaymentmodal").modal("show");
    $("#totalcost").val(totpayment);
    $("#pendingpayment").val(pendingcost);
    $("#totreceivedcost").val(receivedcost);
    $("#addpaymentinput").attr("max", pendingcost);
});

$(document).on("submit", "#addpayment", function () {
    var renewalid = $("#leadid").val();
    $.ajax({
        type: "POST",
        url: "/addpayment",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Payment Added..",
                showConfirmButton: false,
                timer: 1500,
            });

            if (data == "single-outstanding") {
                window.location.href = "/outstanding";
            } else {
                location.reload();
            }
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("submit", "#savedesign", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/savedesign",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Campaign Created Successfully..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

// $(document).on("change","#packagetype",function(){

//     var packagetype = $(this).val();

//     $.ajax({
//         type:"GET",
//         url: "/getdesigns/"+packagetype,
//         // async: false,

//         success: function(data) {

//             $('#designs').empty();
//             $("#designs").html('<option value="">-- Choose Product --</option>')
//             $.each(data,function(key, item){
//                 $('#designs').append(
//                     '<option>'+item.productname+'</option>'
//                 )

//             });

//         }});

// });

$(".onetimeproject").hide();
$(".multipleproject").hide();

$(document).on("change", "#packagetype", function () {
    var packagetype = $(this).val();

    if (packagetype == "One time") {
        $(".onetimeproject").show();
        $(".multipleproject").hide();
    } else {
        $(".multipleproject").show();
        $(".onetimeproject").hide();
    }
});

$(document).on("submit", "#savedesigndetails", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/savedesigndetails",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Design Created Successfully..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("click", ".uploadfile", function () {
    const id = $(this).attr("id");
    text = id.replace("id-", "");

    $("#uploadfile").modal("show");
    $("#designid").val(text);
});

$(document).on("submit", "#uploaddesignfile", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/uploaddesignfile",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Design Created Successfully..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("click", ".viewuploadfile", function () {
    $("#showuploadfile").modal("show");
});

$(document).ready(function () {
    // Get the active tab from local storage
    var activeTab = localStorage.getItem("activeTab");

    //   alert(activeTab);
    // If an active tab is stored, show it
    if (activeTab) {
        $('#pills-tab button[data-bs-target="' + activeTab + '"]').tab("show");
    }

    // Update the active tab in local storage when a new tab is shown
    $('button[data-bs-toggle="pill"]').on("show.bs.tab", function (e) {
        localStorage.setItem("activeTab", $(e.target).data("bs-target"));
    });
});

function disallowMinus(event) {
    var key = event.keyCode || event.charCode;
    if (key === 109) {
        // 45 is the key code for minus symbol (-)
        event.preventDefault();
    }
    if (key === 189) {
        // 45 is the key code for minus symbol (-)
        event.preventDefault();
    }

    if (key === 69) {
        // 45 is the key code for minus symbol (-)
        event.preventDefault();
    }
}

$(document).on("click", ".campaignstartdate2", function () {
    $(".campaigndisable").off("click");
});

$(document).on("click", ".campaignstartdateedit", function () {
    $(".campaigndisable").off("click");
});

$(".deleterenewal").on("click", function () {
    const id = $(this).data("renewalid");

    // const id = $(this).attr('id');
    // text = id.replace('id-', '');

    // alert(text);
    Swal.fire({
        title: "Are you sure?",
        text: "You want to delete this record..!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "GET",
                url: `renewaldelete/${id}`,
                success: function (data) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Your file has beed Deleted..",
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    window.location.href = "/viewrenewals";
                },
                error: function (data) {
                    Swal.fire(
                        "Deleted!",
                        "Your file has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
});

$("#mobilenum").blur(function () {
    var mblnum = $(this).val();
    $.ajax({
        type: "GET",
        // url: "/getpoproductdetails/"+poid,
        url: "/mobilenumcheck/" + mblnum,
        success: function (data) {
            if (data != 0) {
                $("#mobilenum-error").html("Mobile Number Already Exist");
                $("#mobilenum").focus();
            } else {
                $("#mobilenum-error").html("");
            }
        },
    });
});

$("#email").blur(function () {
    var email = $(this).val();
    $.ajax({
        type: "GET",
        // url: "/getpoproductdetails/"+poid,
        url: "/emailcheck/" + email,
        success: function (data) {
            if (data != 0) {
                $("#email-error").html("E-mail Already Exist");
                $("#email").focus();
            } else {
                $("#email-error").html("");
            }
        },
    });
});

$("#mobilenumedit").keypress(function () {
    // alert("data");
    var mblnum = $(this).val();
    $.ajax({
        type: "GET",
        // url: "/getpoproductdetails/"+poid,
        url: "/mobilenumcheck/" + mblnum,
        success: function (data) {
            // alert(data);
            if (data != 0) {
                $("#mobilenum-error").html("Mobile Number Already Exist");
                $("#mobilenum").focus();
            } else {
                $("#mobilenum-error").html("");
            }
        },
    });
});

$(".campaigntenturedatadesign").keyup(function () {
    var tenture = $(this).val();
    var cstartdate = $(".campaignstartdatedesign").val();

    var tenuredata = tenture - 1;
    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04'

    $(".campaignenddatedesign").val(cenddate);
    $(".campaignrenewaldate2").val(cenddate);
});

$(".campaigntenturedatadesign").blur(function () {
    var tenture = $(this).val();
    var cstartdate = $(".campaignstartdatedesign").val();

    var tenuredata = tenture - 1;
    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04'

    $(".campaignenddatedesign").val(cenddate);
    $(".campaignrenewaldate2").val(cenddate);

    $(".postercount").focus();
});

$(".campaignstartdatedesign").change(function () {
    var tenture = $(".campaigntenturedatadesign").val();
    var cstartdate = $(this).val();

    var tenuredata = tenture - 1;

    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate, including time
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04 12:34'

    $(".campaignenddatedesign").val(cenddate);
    $(".campaignrenewaldate").val(cenddate);
});

$(".campaignstartdateseo").change(function () {
    var tenture = $(".seoperiod").val();
    var cstartdate = $(this).val();

    var tenuredata = tenture - 1;

    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate, including time
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04 12:34'

    $(".campaignenddateseo").val(cenddate);
});

$(".seoperiod").blur(function () {
    var tenture = $(this).val();
    var cstartdate = $(".campaignstartdateseo").val();

    var tenuredata = tenture - 1;
    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04'

    $(".campaignenddateseo").val(cenddate);
    $(".campaignrenewaldate2").val(cenddate);

    $(".campaignrenewalamtseo").focus();
});

$(".seoperiod").change(function () {
    var tenture = $(this).val();
    var cstartdate = $(".campaignstartdateseo").val();

    var tenuredata = tenture - 1;
    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04'

    $(".campaignenddateseo").val(cenddate);
    $(".campaignrenewaldate2").val(cenddate);

    $(".campaignrenewalamtseo").focus();
});

$(".editseoperiod").blur(function () {
    var tenture = $(this).val();
    var cstartdate = $(".campaignstartdateeditseo").val();

    var tenuredata = tenture - 1;
    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04'

    $(".campaignenddateeditseo").val(cenddate);
    $(".campaignrenewaldateeditseo").val(cenddate);

    $(".campaignrenewalamtdesignedit").focus();
});

$(".editseoperiod").change(function () {
    var tenture = $(this).val();
    var cstartdate = $(".campaignstartdateeditseo").val();

    var tenuredata = tenture - 1;
    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04'

    $(".campaignenddateeditseo").val(cenddate);
    $(".campaignrenewaldateeditseo").val(cenddate);

    $(".campaignrenewalamtdesignedit").focus();
});

$(".campaignstartdatecrm").change(function () {
    var tenture = $(".crmtenure").val();
    var cstartdate = $(this).val();

    var tenuredata = tenture - 1;

    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate, including time
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04 12:34'

    $(".campaignenddatecrm").val(cenddate);
});

$(".crmtenure").change(function () {
    var tenture = $(this).val();
    var cstartdate = $(".campaignstartdatecrm").val();

    var tenuredata = tenture - 1;
    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04'

    $(".campaignenddatecrm").val(cenddate);
    $(".campaignrenewaldate2").val(cenddate);

    $(".numofcount").focus();
});

$(".campaigntenturedataextend").blur(function () {
    var tenture = $(this).val();
    var cstartdate = $(".campaignstartdateextend").val();

    var tenuredata = tenture - 1;
    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04'

    $(".campaignenddateextend").val(cenddate);
    $(".campaignrenewaldateextend").val(cenddate);

    $(".campaignchargeextend").focus();
});

$(document).on("keyup", ".campaignchargeextend", function () {
    var campaigncharge = parseInt($(this).val());
    // alert(campaigncharge);
    var servicecharge = parseInt($(".servicechargeextend").val());
    // alert(servicecharge);

    // alert(totalAmount);
    // if(totalAmount == 'NaN')
    // {
    //     $(".campaignrenewalamt2").val(0);
    // }
    // else
    // {
    //     $(".campaignrenewalamt2").val(totalAmount);
    // }

    if (isNaN(campaigncharge)) {
        campaigncharge = 0; // Set default value to 0 if parsing fails
    }

    if (isNaN(servicecharge)) {
        servicecharge = 0; // Set default value to 0 if parsing fails
    }

    var totalAmount = campaigncharge + servicecharge;
    $(".campaignrenewalamtextend").val(totalAmount);
    $(".campaignpendingamtextend").val(totalAmount);
});

$(document).on("keyup", ".servicechargeextend", function () {
    var campaigncharge = parseInt($(this).val());
    // alert(campaigncharge);
    var servicecharge = parseInt($(".campaignchargeextend").val());

    if (isNaN(campaigncharge)) {
        campaigncharge = 0; // Set default value to 0 if parsing fails
    }

    if (isNaN(servicecharge)) {
        servicecharge = 0; // Set default value to 0 if parsing fails
    }
    var totalAmount = campaigncharge + servicecharge;

    $(".campaignrenewalamtextend").val(totalAmount);

    console.log(totalAmount);
});

$(document).on("keyup", ".campaignreceivedamtextend", function () {
    var receivedamt = $(this).val();
    var renewalamt = $(".campaignrenewalamtextend").val();
    var pendingamt = $(".campaignpendingamtextend").val();

    $(".campaignpendingamtextend").val(renewalamt - receivedamt);
});

$(document).on("click", ".extendcampaigns", function () {
    var extendcampaigns = $(this).data("campaignid");
    var campaigntitle = $(this).data("campaigntitle");
    var parentcapaignid = $(this).data("parentcapaignid");
    var renewalamt = $(this).data("renewalamt");
    var campaigncharge = $(this).data("campaigncharge");
    var servicecharge = $(this).data("servicecharge");
    var productid = $(this).data("productid");
    var extendcampaignstartdate = $(this).data("extendcampaignstartdate");
    var pendingamt = $(this).data("pendingamt");

    $("#extendcampaign").modal("show");

    $("#campaigntitle").val(campaigntitle);
    $("#extendcampaignid").val(parentcapaignid);
    $(".campaignchargeextend").val(campaigncharge);
    $(".servicechargeextend").val(servicecharge);
    $(".campaignrenewalamtextend").val(renewalamt);
    $(".campaignproduct").val(productid);
    $(".campaignstartdateextend").val(extendcampaignstartdate);

    $(".campaignpendingamtextend").val(renewalamt);
});

$(document).on("click", ".extendcampaignsdesigns", function () {
    var extendcampaigns = $(this).data("campaignid");
    var campaigntitle = $(this).data("campaigntitle");
    var parentcapaignid = $(this).data("parentcapaignid");
    var pendingamt = $(this).data("pendingamt");
    var posterscount = $(this).data("postercount");
    var reelscount = $(this).data("reelscount");
    var videocount = $(this).data("videocount");
    var product = $(this).data("videocount");
    var renewalamt = $(this).data("renewalamt");
    var extendcampaignstartdate = $(this).data("extendcampaignstartdate");

    $("#extendcampaigndesign").modal("show");

    $("#campaigntitledesign").val(campaigntitle);
    $("#extendcampaigniddesign").val(parentcapaignid);
    $(".designproduct").val(product);
    $(".postercount").val(posterscount);
    $(".videocount").val(videocount);
    $(".reelscount").val(reelscount);
    $(".reelscount").val(reelscount);
    $(".campaignextendrenewalamtdesign").val(renewalamt);
    $(".campaignextendstartdatedesign").val(extendcampaignstartdate);
    $(".campaignextendpendingamtdesign").val(renewalamt);
});

$(document).on("submit", "#extendcampaigncreate", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/extendcampaign",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Campaign Created Successfully..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("submit", "#extenddesigncreate", function () {
    var d = new FormData(this);

    var postersCount = d.get("posterscount");
    var videocount = d.get("videocount");
    var reelscount = d.get("reelscount");

    if (postersCount == "" && videocount == "" && reelscount == "") {
        $(".designsubmitalert").text("Please Fill any Designs Count");
    } else {
        $.ajax({
            type: "POST",
            url: "/extenddesign",
            data: new FormData(this),
            // async: false,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $("#responseloader").fadeIn();
            },
            success: function (data) {
                $("#responseloader").fadeOut();
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Campaign Created Successfully..",
                    showConfirmButton: false,
                    timer: 1500,
                });

                location.reload();
            },
            error: function (data) {
                Swal.fire("Error!", "Error.", "success");
            },
        });
    }
});

$(document).on("click", ".designcampaignstop", function () {
    var campaigntype = $(this).data("campaigntype");
    var id = $(this).data("id");
    var poster = parseInt($(this).data("poster"));
    var reels = parseInt($(this).data("reels"));
    var video = parseInt($(this).data("video"));

    if (isNaN(poster)) {
        poster = 0; // Set default value to 0 if parsing fails
    }

    if (isNaN(video)) {
        video = 0; // Set default value to 0 if parsing fails
    }

    if (isNaN(reels)) {
        reels = 0; // Set default value to 0 if parsing fails
    }

    var totcount = poster + video + reels;

    $("#designcampaignstop").modal("show");

    $("#totaldesigncount").val(totcount);

    $("#completedcount").attr("max", totcount);
    $("#id").val(id);
});

$(document).on("submit", "#stopdesign", function () {
    $.ajax({
        type: "POST",
        url: "/designstop",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Design Campaign Stopped..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("click", ".editcampaigndesign", function () {
    var campaignid = $(this).data("campaignid");
    var renewalid = $(this).data("renewalid");
    var leadid = $(this).data("leadid");

    $.ajax({
        type: "GET",
        // url: "/getpoproductdetails/"+poid,
        url: "/editcampaigns/" + campaignid,
        success: function (data) {
            // alert(data.campaignproduct);
            $("#editdesigncampaignmodal").modal("show");
            $("#productdesign").val(data.campaignproduct);
            $("#campaignstartdatedesign").val(data.campaignstartdate);
            $("#campaignenddatedesign").val(data.campaignenddate);
            $("#tenturedesign").val(data.tenure);
            $("#campaignrenewaldatedesign").val(data.campaignenddate);
            $("#renewaltypedesign").val(data.renewaltype);
            $("#postercountdesign").val(data.posterscount);
            $("#reelscountdesign").val(data.reelscount);
            $("#videocountdesign").val(data.videocount);

            if (data.renewaltype == "1") {
                $("#Renewalcheckbox").prop("checked", true);
            } else {
                $("#Renewalcheckbox").prop("checked", false);
            }

            $("#campaignrenewalamtdesign").val(data.renewalamount);
            $("#campaignreceivedamtdesign").val(data.receivedamount);
            $("#campaignpendingamtdesign").val(data.pendingamount);
            $("#campaignnamedesign").val(data.campaignname);
            $("#campaigneditiddesign").val(campaignid);
            $("#outstandingexpectdate2").val(data.expecteddate);
            $("#paymentdate2").val(data.paymentdate);

            if (leadid != "") {
                $(".leadid").val(leadid);
                $(".campaignrenewalamtdesignedit").prop("readonly", true);
                $(".campaignreceivedamtdesignedit").prop("readonly", true);

                if (data.pendingamount == 0) {
                    $(".renewalallexpecteddate11").prop("required", false);
                    $(".requiredalert10").hide();
                }
            }
        },
    });
});

$(document).on("submit", "#updatedesigncampaign", function () {
    var campaignid = $("#campaignid").val();
    var d = new FormData(this);
    var postersCount = d.get("posterscount");
    var videocount = d.get("videocount");
    var reelscount = d.get("reelscount");

    if (postersCount == "" && videocount == "" && reelscount == "") {
        $(".designsubmitalert").text("Please Fill any Designs Count");
    } else {
        $.ajax({
            type: "POST",
            url: "/newdesigncampaignupdate",
            data: new FormData(this),
            // async: false,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $("#responseloader").fadeIn();
            },
            success: function (data) {
                $("#responseloader").fadeOut();
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Campaign Update Successfully..",
                    showConfirmButton: false,
                    timer: 1500,
                });

                location.reload();
            },
            error: function (data) {
                Swal.fire("Error!", "Error.", "success");
            },
        });
    }
});

$(document).on("click", ".editcampaignseo", function () {
    var campaignid = $(this).data("campaignid");
    var renewalid = $(this).data("renewalid");

    $.ajax({
        type: "GET",
        // url: "/getpoproductdetails/"+poid,
        url: "/editcampaigns/" + campaignid,
        success: function (data) {
            // alert(data.campaignproduct);
            $("#editseocampaignmodal").modal("show");
            $("#productseo").val(data.campaignproduct);
            $("#campaignstartdateseo").val(data.campaignstartdate);
            $("#campaignenddateseo").val(data.campaignenddate);
            $("#tentureseo").val(data.seocrmperiod);
            $("#campaignrenewaldateseo").val(data.campaignenddate);
            $("#renewaltypeseo").val(data.renewaltype);
            $("#outstandingexpectdate3").val(data.expecteddate);
            $("#paymentdate3").val(data.paymentdate);

            if (data.renewaltype == "1") {
                $("#Renewalcheckbox").prop("checked", true);
            } else {
                $("#Renewalcheckbox").prop("checked", false);
            }

            $("#campaignrenewalamtseo").val(data.renewalamount);
            $("#campaignreceivedamtseo").val(data.receivedamount);
            $("#campaignpendingamtseo").val(data.pendingamount);
            $("#campaignnameseo").val(data.campaignname);
            $("#campaigneditidseo").val(campaignid);

            if (leadid != "") {
                $(".leadid").val(leadid);
                $(".campaignrenewalamtseoedit").prop("readonly", true);
                $(".campaignreceivedamtseoedit").prop("readonly", true);
            }
        },
    });
});

$(document).on("keyup", ".campaignreceivedamtseoedit", function () {
    var receivedamt = $(this).val();

    var renewalamt = $(".campaignrenewalamtseoedit").val();
    var pendingamt = $(".campaignpendingamtseoedit").val();
    $(this).prop("max", renewalamt);

    $(".campaignpendingamtseoedit").val(renewalamt - receivedamt);
});

$(document).on("keyup", ".campaignrenewalamtseoedit", function () {
    var campaignrenewalamtseoedit = $(this).val();
    $(".campaignpendingamtseoedit").val(campaignrenewalamtseoedit);
});

$(document).on("submit", "#updateseocampaign", function () {
    var campaignid = $("#campaignid").val();
    $.ajax({
        type: "POST",
        url: "/newseocampaignupdate",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Campaign Update Successfully..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("click", ".editcampaigncrm", function () {
    var campaignid = $(this).data("campaignid");
    var renewalid = $(this).data("renewalid");
    var leadid = $(this).data("leadid");

    $.ajax({
        type: "GET",
        // url: "/getpoproductdetails/"+poid,
        url: "/editcampaigns/" + campaignid,
        success: function (data) {
            $("#editcrmcampaignmodal").modal("show");
            $("#productcrm").val(data.campaignproduct);
            $("#campaignstartdatecrm").val(data.campaignstartdate);
            $("#campaignenddatecrm").val(data.campaignenddate);
            $("#tenturecrm").val(data.seocrmperiod);
            $("#campaignrenewaldatecrm").val(data.campaignenddate);
            $("#renewaltypecrm").val(data.renewaltype);
            $("#userscountcrm").val(data.crmuserscount);
            $("#renewalallexpecteddate12").val(data.expecteddate);
            $("#paymentdate12").val(data.paymentdate);

            if (data.renewaltype == "1") {
                $("#Renewalcheckbox").prop("checked", true);
            } else {
                $("#Renewalcheckbox").prop("checked", false);
            }

            $("#campaignrenewalamtcrm").val(data.renewalamount);
            $("#campaignreceivedamtcrm").val(data.receivedamount);
            $("#campaignpendingamtcrm").val(data.pendingamount);
            $("#campaignnamecrm").val(data.campaignname);
            $("#campaigneditidcrm").val(campaignid);

            if (leadid != "") {
                $(".leadid").val(leadid);
                $(".campaignrenewalamtcrmedit").prop("readonly", true);
                $(".campaignreceivedamtcrmedit").prop("readonly", true);

                if (data.pendingamount == 0) {
                    $(".renewalallexpecteddate12").prop("required", false);
                    $(".requiredalert12").hide();
                } else {
                    $(".renewalallexpecteddate12").prop("required", true);
                    $(".requiredalert12").show();
                }
            }
        },
    });
});

$(".campaignstartdateeditcrm").change(function () {
    var tenture = $(".editcrmperiod").val();
    var cstartdate = $(this).val();

    var tenuredata = tenture - 1;

    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate, including time
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04 12:34'

    $(".campaignenddateeditcrm").val(cenddate);
});

$(".editcrmperiod").change(function () {
    var tenture = $(this).val();
    var cstartdate = $(".campaignstartdateeditcrm").val();

    var tenuredata = tenture - 1;
    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04'

    $(".campaignenddateeditcrm").val(cenddate);

    $(".numofcount").focus();
});

$(document).on("keyup", ".campaignreceivedamtcrmedit", function () {
    var receivedamt = $(this).val();

    var renewalamt = $(".campaignrenewalamtcrmedit").val();
    var pendingamt = $(".campaignpendingamtcrmedit").val();

    $(this).prop("max", renewalamt);

    $(".campaignpendingamtcrmedit").val(renewalamt - receivedamt);
});

$(document).on("keyup", ".campaignrenewalamtcrmedit", function () {
    var campaignrenewalamtcrmedit = $(this).val();

    $(".campaignpendingamtcrmedit").val(campaignrenewalamtcrmedit);
});

$(document).on("submit", "#updatecrmcampaign", function () {
    var campaignid = $("#campaignid").val();
    $.ajax({
        type: "POST",
        url: "/updatecrmcampaign",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Campaign Update Successfully..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(".campaignextendtenturedatadesign").keyup(function () {
    var tenture = $(this).val();
    var cstartdate = $(".campaignextendstartdatedesign").val();

    var tenuredata = tenture - 1;
    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04'

    $(".campaignextendenddatedesign").val(cenddate);
});

$(document).on("input", ".campaignreceivedamtextend5", function () {
    var receivedamt = $(this).val();
    var renewalamt = $(".campaignrenewalamtextend").val();
    var pendingamt = $(".campaignpendingamtextend").val();

    $(this).prop("max", renewalamt);
    $(".campaignpendingamtextend").val(renewalamt - receivedamt);
});

$(document).on("keyup", ".campaignextendreceivedamtdesign", function () {
    var receivedamt = $(this).val();
    var renewalamt = $(".campaignextendrenewalamtdesign").val();
    var pendingamt = $(".campaignextendpendingamtdesign").val();

    $(this).prop("max", renewalamt);
    $(".campaignextendpendingamtdesign").val(renewalamt - receivedamt);
});

$(document).on("click", ".extendcampaignsseo", function () {
    var extendcampaigns = $(this).data("campaignid");
    var campaigntitle = $(this).data("campaigntitle");
    var parentcapaignid = $(this).data("parentcapaignid");
    var renewalamt = $(this).data("renewalamt");
    var product = $(this).data("product");
    var pendingamt = $(this).data("pendingamt");
    // var tenure = $(this).data("tenure");
    var extendcampaignstartdate = $(this).data("extendcampaignstartdate");

    // alert(tenure);
    $("#extendcampaignseomodal").modal("show");

    $("#extendcampaignnameseo").val(campaigntitle);
    $("#extendcampaignidseo").val(parentcapaignid);
    $(".extendcampaignrenewalamtseo").val(renewalamt);
    $("#extendproductseo").val(product);
    // $(".extendtentureseo").val(tenure);
    $(".extendcampaignstartdateseo ").val(extendcampaignstartdate);
    $(".extendcampaignpendingamtseo").val(renewalamt);
});

$(".extendtentureseo").change(function () {
    var tenture = $(this).val();
    var cstartdate = $(".extendcampaignstartdateseo").val();

    var tenuredata = tenture - 1;
    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04'

    $(".extendcampaignenddateseo").val(cenddate);
});

$(".extendtentureseo").blur(function () {
    var tenture = $(this).val();
    var cstartdate = $(".extendcampaignstartdateseo").val();

    var tenuredata = tenture - 1;
    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04'

    $(".extendcampaignenddateseo").val(cenddate);
    $("#extendcampaignrenewalamtseo").focus();
});

$(document).on("keyup", ".extendcampaignreceivedamtseo", function () {
    var receivedamt = $(this).val();
    var renewalamt = $(".extendcampaignrenewalamtseo").val();
    var pendingamt = $(".extendcampaignpendingamtseo").val();

    $(".extendcampaignreceivedamtseo").prop("max", renewalamt);
    $(".extendcampaignpendingamtseo").val(renewalamt - receivedamt);
});

$(document).on("submit", "#extendseocampaign", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/extendseo",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Campaign Created Successfully..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("click", ".extendcrmcampaigns", function () {
    var extendcampaigns = $(this).data("campaignid");
    var campaigntitle = $(this).data("campaigntitle");
    var parentcapaignid = $(this).data("parentcapaignid");
    var product = $(this).data("product");
    // var extendtenturecrm = $(this).data("tenure");
    var numofcount = $(this).data("numofcount");
    var renewalamt = $(this).data("renewalamt");
    var extendcampaignstartdate = $(this).data("extendcampaignstartdate");
    var pendingamt = $(this).data("pendingamt");

    $("#extendcampaigncrmmodal").modal("show");

    $("#extendcampaignnamecrm").val(campaigntitle);
    $("#extendcampaignidcrm").val(parentcapaignid);
    $(".numofcount").val(numofcount);
    $(".extendcampaignrenewalamtcrm").val(renewalamt);
    // $(".extendtenturecrm").val(extendtenturecrm);
    $("#extendproductcrm").val(product);
    $(".extendcampaignstartdatecrm ").val(extendcampaignstartdate);
    $(".extendcampaignpendingamtcrm").val(renewalamt);
});

$(".extendtenturecrm").change(function () {
    var tenture = $(this).val();
    var cstartdate = $(".extendcampaignstartdatecrm").val();

    var tenuredata = tenture - 1;
    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04'

    $(".extendcampaignenddatecrm").val(cenddate);
});

$(".extendtenturecrm").blur(function () {
    var tenture = $(this).val();
    var cstartdate = $(".extendcampaignstartdatecrm").val();

    var tenuredata = tenture - 1;
    // Convert cstartdate to a moment object
    var startMoment = moment(cstartdate, "YYYY-MM-DD H:i");

    // Add tenture days to startMoment
    var endMoment = startMoment.add(tenuredata, "days");

    // Format endMoment as a string in the same format as cstartdate
    var cenddate = endMoment.format("YYYY-MM-DD HH:mm");

    console.log(cenddate); // Outputs '2023-06-04'

    $(".extendcampaignenddatecrm").val(cenddate);

    $(".numofcount").focus();
});

$(document).on("keyup", ".extendcampaignreceivedamtcrm", function () {
    var receivedamt = $(this).val();
    var renewalamt = $(".extendcampaignrenewalamtcrm").val();
    var pendingamt = $(".extendcampaignpendingamtcrm").val();

    $(".extendcampaignreceivedamtcrm"), prop("max", renewalamt);
    $(".extendcampaignpendingamtcrm").val(renewalamt - receivedamt);
});

$(document).on("submit", "#extendcrmcampaign", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/extendcrm",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Campaign Created Successfully..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("click", "#leadaddresscopy", function () {
    var billtoaddress = $("#billtoaddress").val();

    $("#shipaddress").val(billtoaddress);
});

$(document).on("keyup", ".productreceivedcost", function () {
    var receivedamt = $(this).val();
    var renewalamt = $(".producttotalcost").val();
    var pendingamt = $(".productpendingcost").val();

    $(this).prop("max", renewalamt);
    $(".productpendingcost").val(renewalamt - receivedamt);
});

$(document).on("input", ".productreceivedcostedit", function () {
    var receivedamt = $(this).val();
    var renewalamt = $(".producttotalcostedit").val();
    var pendingamt = $(".productpendingcostedit").val();

    $(".productpendingcostedit").val(renewalamt - receivedamt);
});

$(document).on("keyup", ".producttotalcost", function () {
    var renewalamt = $(this).val();
    var receivedamt = $(".productreceivedcost").val();
    var pendingamt = $(".productpendingcost").val();

    $(".productpendingcost").val(renewalamt - receivedamt);
});

$(document).on("submit", "#saveextraproducts", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/saverenewalproduct",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Product Added Successfully..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("click", ".editproduct", function () {});

$(document).on("click", ".addproductpayment", function () {
    var renewalid = $(this).data("renewalid");
    var totamt = $(this).data("totamt");
    var receivedcost = $(this).data("receivedcost");
    var pendingcost = $(this).data("pendingcost");
    var productid = $(this).data("productid");

    $("#addproductrenewalpayment").modal("show");

    $("#productid").val(productid);
    $("#producttotpayment").val(totamt);
    $("#productpendingpayment").attr("max", pendingcost);
});

$(document).on("submit", "#updateproductpayment", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/renewalsproductpaymentupdate",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Payment Added Successfully..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("click", ".getproductpaymenthistory", function () {
    var renewalid = $(this).data("renewalid");
    var productid = $(this).data("productid");
    $.ajax({
        type: "GET",
        // url: "/getpoproductdetails/"+poid,
        url: "/renewalproductpaymenthistory/" + renewalid + "/" + productid,
        success: function (data) {
            $("#renewalpaymenthistory").modal("show");

            $(".paymentinfo tbody").empty();
            $.each(data, function (key, item) {
                $(".paymentinfo tbody").append(
                    "<tr>\
                    <td>" +
                        item.created_at +
                        "</td>\
                    <td>" +
                        item.receivedamt +
                        "</td>\
                 </tr>"
                );
            });
        },
    });
});

$(document).on("click", ".editproductrenewal", function () {
    var renewalid = $(this).data("renewalid");
    var productid = $(this).data("productid");
    var totamt = $(this).data("totamt");
    var receivedcost = $(this).data("receivedcost");
    var pendingcost = $(this).data("pendingcost");
    var productname = $(this).data("productname");
    var editremarks = $(this).data("remarks");

    $("#editproductrenewalmodal").modal("show");

    $("#editproductname").val(productname);
    $("#editproducttotalcost").val(totamt);
    $("#editproductreceivedcost").val(receivedcost);
    $("#editproductpendingcost").val(pendingcost);
    $("#editremarks").val(editremarks);
    $("#renewalid").val(renewalid);
    $("#editproductid").val(productid);
});

$(document).on("submit", "#updateextraproducts", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/updateextraproducts",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Updated Successfully..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(document).on("click", ".approverefund", function () {
    const renewalidrefund = $("#renewalidrefund").val();
    const campaignidrefund = $("#campaignidrefund").val();
    // alert(text);
    Swal.fire({
        title: "Are you sure?",
        text: "You want to delete this record..!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Approve!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "GET",
                url: `refundapprove/${renewalidrefund}/${campaignidrefund}`,
                success: function (data) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Your file has beed Deleted..",
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    location.reload();
                },
                error: function (data) {
                    Swal.fire(
                        "Deleted!",
                        "Your file has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
});

$(document).on("submit", "#updateoutstandingproduct", function () {
    var d = new FormData(this);
    // alert(d);
    $.ajax({
        type: "POST",
        url: "/outstandingproductpaymentupdate",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Payment Added Successfully..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(".mandatoryfield").hide();
$(".mandatoryfieldfollowup").hide();
$(".totalcostmandatory").hide();
$(".expectedcostmandatory").hide();
$(".followupmandatoryfield").hide();
$(document).ready(function () {
    var statuscheck = $(this).val();

    if (statuscheck == "HOT" || statuscheck == "FOLLOW UP") {
        $(".statuserror").text("Please fill Total Cost & Expected Amount");
        $(".TotalCost").prop("required", true);
        $(".TotalCost").val("");
        $(".ReceivedCost").val("");
        $(".leadproductpendingcost").val("");
        $(".ExpectedAmount").prop("required", true);
        $(".ExpectedAmount").val("");
        $(".totmandatoryfield").show();
        $(".totalcostmandatory").show();
        $(".expectedcostmandatory").show();
        $(".receivedcostmandatory").hide();
        $(".paymentrequiredalert13").hide();
        $(".paymentamount13").prop("required", false);
        $(".paymentrequiredalert15").hide();
        $(".paymentamount15").prop("required", false);
        $(".FollowupDate").prop("required", true);
        $(".mandatoryfieldfollowup").show();
        $(".leadproductreceivedcostedit").val("");
    } else {
        // $(".statuserror").text("");
        $(".TotalCost").prop("required", false);
        $(".ExpectedAmount").prop("required", false);
        $(".totmandatoryfield").hide();
    }

    if (
        statuscheck == "WARM" ||
        statuscheck == "NEW" ||
        statuscheck == "COLD"
    ) {
        $(".statuserror").text("");
        $(".TotalCost").val("");
        $(".totalcostmandatory").hide();
        $(".expectedcostmandatory").hide();
        $(".receivedcostmandatory").hide();
        $(".paymentrequiredalert13").hide();
        $(".paymentamount13").prop("required", false);
        $(".paymentrequiredalert15").hide();
        $(".paymentamount15").prop("required", false);
        $(".FollowupDate").prop("required", false);
        $(".mandatoryfieldfollowup").hide();
    }

    if (statuscheck == "CONVERTED") {
        $(".statuserror").text("Please Mention Converted Date");
        $(".ExpectedAmount").val("");
        $(".TotalCost").val("");
        $(".ExpectedAmount").prop("readonly", true);
        $(".ReceivedCost").prop("required", true);
        $(".ConvertedDate").prop("required", true);
        $(".TotalCost").prop("required", true);
        $(".mandatoryfield").show();
        $(".totmandatoryfield").show();
        $(".totalcostmandatory").show();
        $(".receivedcostmandatory").show();
        $(".ReceivedCost").val("");
        $(".paymentamount15").val("");
        $(".paymentrequiredalert13").hide();
        $(".paymentamount13").prop("required", false);
        $(".paymentrequiredalert15").hide();
        $(".paymentamount15").prop("required", false);
        $(".FollowupDate").prop("required", false);
        $(".mandatoryfieldfollowup").hide();
    } else {
        // $(".statuserror").text("");
        $(".ExpectedAmount").prop("readonly", false);
        $(".ConvertedDate").prop("required", false);
        $(".ReceivedCost").prop("required", false);
        $(".mandatoryfield").hide();
    }

    if (statuscheck == "FOLLOW UP") {
        // $(".statuserror").text("Please Mention Converted Date");
        $(".FollowupDate").prop("required", true);
        $(".mandatoryfieldfollowup").show();
        $(".totalcostmandatory").show();
        $(".expectedcostmandatory").show();
        $(".receivedcostmandatory").hide();
        $(".paymentrequiredalert13").hide();
        $(".paymentamount13").prop("required", false);
        $(".paymentrequiredalert15").hide();
        $(".paymentamount15").prop("required", false);
    } else {
        // $(".statuserror").text("");
        // $(".FollowupDate").prop("required",false);
        // $(".mandatoryfieldfollowup").hide();
    }
});

$(".mandatoryfield").hide();
$(".totmandatoryfield").hide();
$(".mandatoryfieldfollowup").hide();
$(".receivedcostmandatory").hide();
$(".manClosureDate").hide();
$(".paymandatoryfield").hide();

$(document).on("change", ".statuscheck", function () {
    var statuscheck = $(this).val();

    if (statuscheck == "HOT" || statuscheck == "FOLLOW UP") {
        $(".statuserror").text("Please fill Total Cost & Expected Amount");
        $(".TotalCost").prop("required", true);
        $(".TotalCost").val("");
        $(".ReceivedCost").val("");
        $(".ReceivedCost").prop("readonly", true);
        $(".leadproducttotalcost").prop("readonly", false);
        $(".ExpectedAmount").prop("readonly", false);
        $(".FollowupDate").prop("readonly", false);
        $(".leadproducttotalcostedit").prop("readonly", false);
        $(".leadproductpendingcost").val("");
        // $(".leadproducttotalcostedit").val('');
        $(".leadproducttotalcostedit").prop("required", true);
        // $(".expectedcostedit").val('');
        // $(".followupdatedit").val('');
        // $(".leadproductpendingcostedit").val('');
        $(".paymentamount15").val("");
        // $(".FollowupDate").val('');
        $(".paymentamount13").val("");
        $(".ExpectedAmount").val("");
        $(".ExpectedAmount").prop("required", true);
        // $(".ExpectedAmount").val('');
        $(".ClosureDate").val("");
        $(".ClosureDate").prop("readonly", false);
        $(".ClosureDate").prop("required", true);
        $(".PaymentDate").val("");
        $(".PaymentDate").prop("readonly", true);
        $(".PaymentDate").prop("required", false);

        $(".manClosureDate").show();
        $(".totmandatoryfield").show();
        $(".totalcostmandatory").show();
        $(".expectedcostmandatory").show();
        $(".ConvertedDate").val("");
        $(".receivedcostmandatory").hide();
        $(".paymentrequiredalert13").hide();
        $(".paymentamount13").prop("required", false);
        $(".paymentrequiredalert15").hide();
        $(".paymentamount15").prop("required", false);
        $(".FollowupDate").prop("required", true);
        $(".mandatoryfieldfollowup").show();
        $(".leadproductreceivedcostedit").val("");
        $(".leadproductreceivedcostedit").prop("required", false);
        $(".leadproductreceivedcost").prop("readonly", true);
        $(".ConvertedDate").prop("readonly", true);
        $(".paymentamount13").prop("readonly", true);
        $(".leadproductreceivedcostedit").prop("readonly", true);
        $(".paymentamount15").prop("readonly", true);
    } else {
        // $(".statuserror").text("");
        $(".TotalCost").prop("required", false);
        $(".ExpectedAmount").prop("required", false);
        $(".totmandatoryfield").hide();
    }

    if (statuscheck == "WARM" || statuscheck == "COLD") {
        $(".statuserror").text("");
        // $(".TotalCost").val('');
        $(".totalcostmandatory").hide();
        $(".expectedcostmandatory").hide();
        $(".receivedcostmandatory").hide();
        // $(".ExpectedAmount").val('');
        $(".paymentrequiredalert13").hide();
        // $(".leadproducttotalcostedit").val('');
        // $(".expectedcostedit").val('');
        // $(".leadproductpendingcostedit").val('');
        // $(".leadproductreceivedcostedit").val('');
        $(".paymentamount15").val("");
        $(".FollowupDate").val("");
        $(".ConvertedDate").val("");
        // $(".leadproductpendingcost").val('');
        // $(".ReceivedCost").val('');
        $(".paymentamount13").val("");
        $(".paymentamount13").prop("required", false);
        $(".paymentrequiredalert15").hide();
        $(".paymentamount15").prop("required", false);
        $(".FollowupDate").prop("required", false);
        $(".mandatoryfieldfollowup").hide();
        $(".PaymentDate").val("");
        $(".PaymentDate").prop("readonly", true);
        $(".PaymentDate").prop("required", false);
        // $(".leadproductreceivedcost").prop("readonly",true)
        // $(".ConvertedDate").prop("readonly",true);
        // $(".paymentamount13").prop("readonly",true);
        // $(".leadproducttotalcost").prop("readonly",true);
        // $(".ExpectedAmount").prop("readonly",true);
        // $(".FollowupDate").prop("readonly",true);
        // $(".leadproducttotalcostedit").prop("readonly",true);
        // $(".leadproductreceivedcostedit").prop("readonly",true);
        // $(".paymentamount15").prop("readonly",true);
    }
    if (statuscheck == "NEW") {
        $(".statuserror").text("");
        // $(".TotalCost").val('');
        $(".totalcostmandatory").hide();
        $(".expectedcostmandatory").hide();
        $(".receivedcostmandatory").hide();
        // $(".ExpectedAmount").val('');
        $(".paymentrequiredalert13").hide();
        // $(".leadproducttotalcostedit").val('');
        // $(".expectedcostedit").val('');
        // $(".leadproductpendingcostedit").val('');
        // $(".leadproductreceivedcostedit").val('');
        $(".paymentamount15").val("");
        $(".FollowupDate").val("");
        $(".ConvertedDate").val("");
        $(".ConvertedDate").prop("readonly", false);
        $(".ClosureDate").val("");
        // $(".leadproductpendingcost").val('');
        $(".ReceivedCost").val("");
        $(".ExpectedAmount").val("");
        $(".ExpectedAmount").prop("readonly", false);
        $(".paymentamount13").val("");
        $(".paymentamount13").prop("required", false);
        $(".paymentrequiredalert15").hide();
        $(".paymentamount15").prop("required", false);
        $(".FollowupDate").prop("required", false);
        $(".mandatoryfieldfollowup").hide();
        $(".ReceivedCost").prop("readonly", true);
        $(".ClosureDate").prop("readonly", true);
        $(".FollowupDate").prop("readonly", false);
        $(".PaymentDate").val("");
        $(".PaymentDate").prop("readonly", true);
        $(".PaymentDate").prop("required", false);
        // $(".ClosureDate").prop("required",false);
        $(".manClosureDate").hide();
        // $(".leadproductreceivedcost").prop("readonly",true)
        // $(".ConvertedDate").prop("readonly",true);
        // $(".paymentamount13").prop("readonly",true);
        // $(".leadproducttotalcost").prop("readonly",true);
        // $(".ExpectedAmount").prop("readonly",true);
        // $(".FollowupDate").prop("readonly",true);
        // $(".leadproducttotalcostedit").prop("readonly",true);
        // $(".leadproductreceivedcostedit").prop("readonly",true);
        // $(".paymentamount15").prop("readonly",true);
    }

    if (statuscheck == "CONVERTED") {
        $(".statuserror").text("Please Mention Converted Date");
        // $(".ExpectedAmount").val('');
        $(".TotalCost").val("");
        $(".ExpectedAmount").prop("readonly", true);
        $(".ReceivedCost").prop("required", true);
        $(".ConvertedDate").prop("required", true);
        $(".TotalCost").prop("required", true);
        // $(".FollowupDate").val('');
        $(".mandatoryfield").show();
        $(".paymandatoryfield").show();
        $(".totmandatoryfield").show();
        $(".totalcostmandatory").show();
        $(".receivedcostmandatory").show();
        $(".ReceivedCost").val("");
        $(".ClosureDate").val("");
        $(".ClosureDate").prop("readonly", true);
        $(".ClosureDate").prop("required", false);
        $(".PaymentDate").val("");
        $(".PaymentDate").prop("readonly", false);
        $(".PaymentDate").prop("required", true);
        $(".manClosureDate").hide();
        $(".paymentrequiredalert13").hide();
        $(".paymentamount13").prop("required", false);
        $(".paymentrequiredalert15").hide();
        $(".paymentamount15").prop("required", false);
        $(".FollowupDate").prop("required", false);
        $(".mandatoryfieldfollowup").hide();
        $(".expectedcostmandatory").hide();
        $(".leadproductreceivedcostedit").prop("required", true);
        $(".ReceivedCost").prop("readonly", false);
        $(".leadproducttotalcost").prop("readonly", false);
        $(".leadproductreceivedcost ").prop("readonly", false);
        $(".ConvertedDate").prop("readonly", false);
        $(".paymentamount13").prop("readonly", false);
        $(".leadproducttotalcostedit").prop("readonly", false);
        $(".leadproductreceivedcostedit").prop("readonly", false);
        $(".followupdatedit").prop("readonly", true);
        $(".FollowupDate").prop("readonly", true);
        $(".paymentamount15").prop("readonly", false);
    } else {
        // $(".statuserror").text("");
        // $(".ExpectedAmount").prop("readonly",false);
        // $(".ConvertedDate").prop("required",false);
        // $(".ReceivedCost").prop("required",false);
        $(".mandatoryfield").hide();
        $(".paymandatoryfield").hide();
    }

    if (
        statuscheck == "NO REQUIREMENT" ||
        statuscheck == "NO RESPONSE" ||
        statuscheck == "NOT REACHABLE" ||
        statuscheck == "IRRELEVANT"
    ) {
        $(".PaymentDate").val("");
        $(".PaymentDate").prop("readonly", true);
        $(".PaymentDate").prop("required", false);
    } else {
    }

    if (statuscheck == "FOLLOW UP") {
        // $(".statuserror").text("Please Mention Converted Date");
        $(".FollowupDate").prop("required", true);
        $(".mandatoryfieldfollowup").show();
        $(".totalcostmandatory").show();
        $(".expectedcostmandatory").show();
        $(".receivedcostmandatory").hide();
        $(".paymentrequiredalert13").hide();
        $(".paymentamount13").val("");
        $(".paymentamount13").prop("required", false);
        $(".paymentrequiredalert15").hide();
        $(".paymentamount15").prop("required", false);
    } else {
        // $(".statuserror").text("");
        // $(".FollowupDate").prop("required",false);
        // $(".mandatoryfieldfollowup").hide();
    }
});

$(document).ready(function () {
    var d = new Date();
    var todayDate = d.toISOString().split("T")[0];

    $("#statusupdatedate2").val(todayDate);
});

$(document).on("change", ".statuscheck", function () {
    var d = new Date();
    var todayDate = d.toISOString().split("T")[0];

    $("#statusupdatedate2").val(todayDate);
});

$(document).on("submit", "#convertrenewals", function () {
    $.ajax({
        type: "POST",
        url: "/converttorenewals",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,

        success: function (data) {
            if ($.isEmptyObject(data.error)) {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Lead Converted..!",
                    showConfirmButton: false,
                    timer: 1500,
                });

                location.reload();
            } else {
                printErrorMsg(data.error);
            }
        },
    });
});

$(document).on("submit", "#saveleadsproduct", function () {
    $.ajax({
        type: "POST",
        url: "/saveleadsproduct",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,

        success: function (data) {
            if ($.isEmptyObject(data.error)) {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Product Saved Successfully..!",
                    showConfirmButton: false,
                    timer: 1500,
                });

                location.reload();
            } else {
                printErrorMsg(data.error);
            }
        },
    });
});

$(document).on("input", ".leadproductreceivedcostedit", function () {
    var receivedCost = parseFloat($(this).val());
    var totalCost = parseFloat($(".leadproducttotalcostedit").val());

    if (receivedCost > totalCost) {
        // alert("Received cost is greater than total cost.");
        $(".error-text").text("Received cost is greater than total cost.");
        // alert("Letters only allowed don't use numbers & special char");
        $(".leadproductreceivedcostedit").focus();
    }
    var pendingamt = parseInt(totalCost) - parseInt(receivedCost);
    $(".leadproductpendingcostedit").val(pendingamt);
});

$(document).on("input", ".leadproductreceivedcost", function () {
    var receivedCost = parseFloat($(this).val());
    var totalCost = parseFloat($(".leadproducttotalcost").val());

    if (receivedCost > totalCost) {
        // alert("Received cost is greater than total cost.");
        $(".error-text").text("Received cost is greater than total cost.");
        // alert("Letters only allowed don't use numbers & special char");
        $(".leadproductreceivedcost").focus();
    }

    var pendingamt = parseInt(totalCost) - parseInt(receivedCost);
    $(".leadproductpendingcost").val(pendingamt);
});

$(document).ready(function () {
    var leadproducttotalcost = $(".leadproducttotalcost").val();

    $(".leadproductpendingcost").val(leadproducttotalcost);
});

$(document).on("input", ".leadproducttotalcost", function () {
    var leadproducttotalcost = $(this).val();
    var leadreceivedcost = $(".leadproductreceivedcost").val();
    $(".leadproductreceivedcost").prop("max", leadproducttotalcost);
    $(".leadproductpendingcost").val(leadproducttotalcost - leadreceivedcost);
});

$(document).ready(function () {
    var leadproducttotalcostedit = $(".leadproducttotalcostedit").val();
    $(".leadproductreceivedcostedit").prop("max", leadproducttotalcostedit);
    $(".leadproductpendingcostedit").val(leadproducttotalcostedit);
});

$(document).on("input", ".leadproducttotalcostedit", function () {
    var leadproducttotalcost = $(this).val();
    $(".leadproductreceivedcostedit").prop("max", leadproducttotalcost);
    var leadreceivedcost = $(".leadproductreceivedcostedit").val();

    $(".leadproductpendingcostedit").val(
        leadproducttotalcost - leadreceivedcost
    );
});

$(document).on("click", ".leadaddpayment", function () {
    var leadid = $(this).data("leadid");
    var totpayment = $(this).data("totpayment");
    var receivedcost = $(this).data("receivedcost");
    var pendingcost = $(this).data("pendingcost");
    var productid = $(this).data("productid");

    $("#addpaymentmodal").modal("show");

    $("#totalcost").val(totpayment);
    $("#pendingpayment").val(pendingcost);
    $("#addpaymentinput").attr("max", pendingcost);
    $("#productid").val(productid);
});

$(document).on("click", ".paymenthistory", function () {
    var leadid = $(this).data("leadid");
    var productid = $(this).data("productid");

    $.ajax({
        type: "GET",
        url: "/getproductwisepaymenthistory/" + leadid + "/" + productid,
        // async: false,
        cache: true,
        contentType: false,
        processData: false,

        success: function (data) {
            $("#paymenthistory").modal("show");

            $(".productpaymenthistory tbody").empty();
            $.each(data, function (key, item) {
                if (item.paymentdate != null) {
                    var paymentdate = item.paymentdate;
                } else {
                    var paymentdate = "-";
                }
                if (item.refundamount != null) {
                    var refundamt = item.refundamount;
                } else {
                    var refundamt = "-";
                }
                if (item.refunddate != null) {
                    var refunddate = item.refunddate;
                } else {
                    var refunddate = "-";
                }
                $(".productpaymenthistory tbody").append(
                    '<tr>\
                    <td class="text-center">' +
                        item.receivedamount +
                        '</td>\
                    <td class="text-center">' +
                        paymentdate +
                        '</td>\
                    <td class="text-center">' +
                        refundamt +
                        '</td>\
                    <td class="text-center">' +
                        refunddate +
                        "</td>\
                 </tr>"
                );
            });
        },
    });
});

$(document).on("click", ".editleadproduct", function () {
    var productname = $(this).data("productname");
    var productid = $(this).data("productid");
    var leadid = $(this).data("leadid");
    var pendingcost = $(this).data("pendingcost");

    var receivedcost = $(this).data("receivedcost");
    var expectedamount = $(this).data("expectedamount");
    var totalcost = $(this).data("totalcost");
    var paymentdate = $(this).data("paymentdate");
    var status = $(this).data("status");
    var converteddate = $(this).data("converteddate");
    var statusupdatedate = $(this).data("statusupdatedate");
    var closuredate = $(this).data("closuredate");
    var followupdate = $(this).data("followupdate");
    var expectedamt = $(this).data("expectedamt");

    $("#productedit").modal("show");

    $("#productname").val(productname);
    $("#productidedit").val(productid);
    $("#leadid").val(leadid);
    $("#pendingcost").val(pendingcost);
    $("#receivedcost").val(receivedcost);

    $("#expectedcostedit").val(expectedamt);
    $("#totalcostedit").val(totalcost);
    $("#paymentdate").val(paymentdate);
    $("#status").val(status);
    $("#converteddate").val(converteddate);
    $("#statusupdatedate2").val(statusupdatedate);
    $(".followupdatedit").val(followupdate);
    $(".closerdatedit").val(closuredate);

    $(".leadproductreceivedcostedit").prop("max", totalcost);

    if (status == "HOT" || status == "FOLLOW UP") {
        $(".statuserror").text("Please fill Total Cost & Expected Amount");
        $(".TotalCost").prop("required", true);
        $(".TotalCost").val("");
        $(".ReceivedCost").val("");
        $(".leadproducttotalcost").prop("readonly", false);
        $(".leadproducttotalcostedit").prop("readonly", false);
        $(".ExpectedAmount").prop("readonly", false);
        $(".FollowupDate").prop("readonly", false);

        $(".leadproductpendingcost").val("");
        // $(".leadproducttotalcostedit").val('');
        $(".leadproducttotalcostedit").prop("required", true);
        // $(".expectedcostedit").val('');
        // $(".followupdatedit").val('');
        // $(".leadproductpendingcostedit").val('');
        $(".paymentamount15").val("");
        // $(".FollowupDate").val('');
        $(".paymentamount13").val("");
        $(".ExpectedAmount").prop("required", true);
        // $(".ExpectedAmount").val('');
        $(".totmandatoryfield").show();
        $(".totalcostmandatory").show();
        $(".expectedcostmandatory").show();
        $(".ConvertedDate").val("");
        $(".receivedcostmandatory").hide();
        $(".paymentrequiredalert13").hide();
        $(".paymentamount13").prop("required", false);
        $(".paymentrequiredalert15").hide();
        $(".paymentamount15").prop("required", false);
        $(".FollowupDate").prop("required", true);
        $(".mandatoryfieldfollowup").show();
        $(".leadproductreceivedcostedit").val("");
        $(".leadproductreceivedcostedit").prop("required", false);
        $(".leadproductreceivedcost").prop("readonly", true);
        $(".ConvertedDate").prop("readonly", true);
        $(".paymentamount13").prop("readonly", true);
        $(".leadproductreceivedcostedit").prop("readonly", true);
        $(".paymentamount15").prop("readonly", true);
    } else {
        // $(".statuserror").text("");
        $(".TotalCost").prop("required", false);
        $(".ExpectedAmount").prop("required", false);
        $(".totmandatoryfield").hide();
    }

    if (status == "WARM" || status == "NEW" || status == "COLD") {
        $(".statuserror").text("");
        // $(".TotalCost").val('');
        $(".totalcostmandatory").hide();
        $(".expectedcostmandatory").hide();
        $(".receivedcostmandatory").hide();
        // $(".ExpectedAmount").val('');

        $(".paymentrequiredalert13").hide();
        // $(".leadproducttotalcostedit").val('');
        // $(".expectedcostedit").val('');
        // $(".leadproductpendingcostedit").val('');
        // $(".leadproductreceivedcostedit").val('');
        $(".paymentamount15").val("");
        $(".FollowupDate").val("");
        $(".ConvertedDate").val("");
        // $(".leadproductpendingcost").val('');
        // $(".ReceivedCost").val('');
        $(".paymentamount13").val("");
        $(".paymentamount13").prop("required", false);
        $(".paymentrequiredalert15").hide();
        $(".paymentamount15").prop("required", false);
        $(".FollowupDate").prop("required", false);
        $(".mandatoryfieldfollowup").hide();
        // $(".leadproductreceivedcost").prop("readonly",true)
        // $(".ConvertedDate").prop("readonly",true);
        // $(".paymentamount13").prop("readonly",true);
        // $(".leadproducttotalcost").prop("readonly",true);
        // $(".ExpectedAmount").prop("readonly",true);
        // $(".FollowupDate").prop("readonly",true);
        // $(".leadproducttotalcostedit").prop("readonly",true);
        // $(".leadproductreceivedcostedit").prop("readonly",true);
        // $(".paymentamount15").prop("readonly",true);
    }

    if (status == "CONVERTED") {
        $(".statuserror").text("Please Mention Converted Date");
        $(".ExpectedAmount").val("");
        $(".TotalCost").val("");
        $(".ExpectedAmount").prop("readonly", true);
        $(".ReceivedCost").prop("required", true);
        $(".ConvertedDate").prop("required", true);
        $(".TotalCost").prop("required", true);
        $(".FollowupDate").val("");
        $(".mandatoryfield").show();
        $(".totmandatoryfield").show();
        $(".totalcostmandatory").show();
        $(".receivedcostmandatory").show();
        $(".ReceivedCost").val("");
        $(".paymentrequiredalert13").hide();
        $(".paymentamount13").prop("required", false);
        $(".paymentrequiredalert15").hide();
        $(".paymentamount15").prop("required", false);
        $(".FollowupDate").prop("required", false);
        $(".mandatoryfieldfollowup").hide();
        $(".expectedcostmandatory").hide();
        $(".leadproductreceivedcostedit").prop("required", true);

        $(".leadproducttotalcost").prop("readonly", false);
        $(".leadproductreceivedcost ").prop("readonly", false);
        $(".ConvertedDate").prop("readonly", false);
        $(".paymentamount13").prop("readonly", false);
        $(".leadproducttotalcostedit").prop("readonly", false);
        $(".leadproductreceivedcostedit").prop("readonly", false);
        $(".followupdatedit").prop("readonly", true);
        $(".paymentamount15").prop("readonly", false);
    } else {
        // $(".statuserror").text("");
        // $(".ExpectedAmount").prop("readonly",false);
        // $(".ConvertedDate").prop("required",false);
        // $(".ReceivedCost").prop("required",false);
        $(".mandatoryfield").hide();
    }

    if (status == "FOLLOW UP") {
        // $(".statuserror").text("Please Mention Converted Date");
        $(".FollowupDate").prop("required", true);
        $(".mandatoryfieldfollowup").show();
        $(".totalcostmandatory").show();
        $(".expectedcostmandatory").show();
        $(".receivedcostmandatory").hide();
        $(".paymentrequiredalert13").hide();
        $(".paymentamount13").val("");
        $(".paymentamount13").prop("required", false);
        $(".paymentrequiredalert15").hide();
        $(".paymentamount15").prop("required", false);
    } else {
        // $(".statuserror").text("");
        // $(".FollowupDate").prop("required",false);
        // $(".mandatoryfieldfollowup").hide();
    }
});

$(document).on("submit", "#updateleadsproduct", function () {
    $.ajax({
        type: "POST",
        url: "/updateleadsproduct",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,

        success: function (data) {
            if ($.isEmptyObject(data.error)) {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Product Saved Successfully..!",
                    showConfirmButton: false,
                    timer: 1500,
                });

                location.reload();
            } else {
                printErrorMsg(data.error);
            }
        },
    });
});

$(document).on("click", ".leadproductdelete", function () {
    const productid = $(this).data("productid");
    const leadid = $(this).data("leadid");
    // alert(text);
    Swal.fire({
        title: "Are you sure?",
        text: "You want to delete this record..!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "GET",
                url: `leadproductdelete/${productid}/${leadid}`,
                success: function (data) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Your file has beed Deleted..",
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    location.reload();
                },
                error: function (data) {
                    Swal.fire(
                        "Deleted!",
                        "Your file has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
});

$(document).on("click", ".deleterenewalcampaign", function () {
    const renewalid = $(this).data("renewalid");
    const campaignid = $(this).data("campaignid");
    // alert(text);
    Swal.fire({
        title: "Are you sure?",
        text: "You want to delete this record..!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "GET",
                url: `deletecampaign/${renewalid}/${campaignid}`,
                success: function (data) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Your file has beed Deleted..",
                        showConfirmButton: false,
                        timer: 1500,
                    });

                    location.reload();
                },
                error: function (data) {
                    Swal.fire(
                        "Deleted!",
                        "Your file has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
});

$(".requiredalert").hide();
$(".paymentrequiredalert").hide();
$(document).on("blur", ".renewalallreceivedamt", function () {
    var pendingamt = $(".renewalallpendingamt").val();
    var expecteddate = $(".renewalallexpecteddate").val();
    var receivedamt = $(this).val();
    var paymentamount1 = $(".paymentamount1").val();

    if (pendingamt != 0) {
        $(".requiredalert").show();
        $(".renewalallexpecteddate").prop("required", true);
    } else {
        $(".renewalallexpecteddate").prop("required", false);
        $(".requiredalert").hide();
    }

    if (receivedamt != 0) {
        $(".paymentrequiredalert").show();
        $(".paymentamount1").prop("required", true);
    } else {
        $(".paymentrequiredalert").hide();
        $(".paymentamount1").prop("required", false);
    }
});

$(".requiredalert2").hide();
$(".paymentrequiredalert2").hide();
$(document).on("blur", ".renewalallreceivedamt2", function () {
    var pendingamt = $(".renewalallpendingamt2").val();
    var expecteddate = $(".renewalallexpecteddate2").val();
    var receivedamt = $(this).val();

    if (pendingamt != 0) {
        $(".requiredalert2").show();
        $(".renewalallexpecteddate2").prop("required", true);
    } else {
        $(".renewalallexpecteddate2").prop("required", false);
        $(".requiredalert2").hide();
    }

    if (receivedamt != 0) {
        $(".paymentrequiredalert2").show();
        $(".paymentamount2").prop("required", true);
    } else {
        $(".paymentrequiredalert2").hide();
        $(".paymentamount2").prop("required", false);
    }
});

$(".requiredalert3").hide();
$(".paymentrequiredalert3").hide();
$(document).on("blur", ".renewalallreceivedamt3", function () {
    var pendingamt = $(".renewalallpendingamt3").val();
    var expecteddate = $(".renewalallexpecteddate3").val();
    var receivedamt = $(this).val();

    if (pendingamt != 0) {
        $(".requiredalert3").show();
        $(".renewalallexpecteddate3").prop("required", true);
    } else {
        $(".renewalallexpecteddate3").prop("required", false);
        $(".requiredalert3").hide();
    }

    if (receivedamt != 0) {
        $(".paymentrequiredalert3").show();
        $(".paymentamount3").prop("required", true);
    } else {
        $(".paymentrequiredalert3").hide();
        $(".paymentamount3").prop("required", false);
    }
});

$(".requiredalert4").hide();
$(".paymentrequiredalert4").hide();
$(document).on("blur", ".renewalallreceivedamt4", function () {
    var pendingamt = $(".renewalallpendingamt4").val();
    var expecteddate = $(".renewalallexpecteddate4").val();
    var receivedamt = $(this).val();

    if (pendingamt != 0) {
        $(".requiredalert4").show();
        $(".renewalallexpecteddate4").prop("required", true);
    } else {
        $(".renewalallexpecteddate4").prop("required", false);
        $(".requiredalert4").hide();
    }

    if (receivedamt != 0) {
        $(".paymentrequiredalert4").show();
        $(".paymentamount4").prop("required", true);
    } else {
        $(".paymentrequiredalert4").hide();
        $(".paymentamount4").prop("required", false);
    }
});

$(".requiredalert5").hide();
$(".paymentrequiredalert5").hide();
$(document).on("blur", ".renewalallreceivedamt5", function () {
    var pendingamt = $(".renewalallpendingamt5").val();
    var expecteddate = $(".renewalallexpecteddate5").val();
    var receivedamt = $(this).val();

    if (pendingamt != 0) {
        $(".requiredalert5").show();
        $(".renewalallexpecteddate5").prop("required", true);
    } else {
        $(".renewalallexpecteddate5").prop("required", false);
        $(".requiredalert5").hide();
    }

    if (receivedamt != 0) {
        $(".paymentrequiredalert5").show();
        $(".paymentamount5").prop("required", true);
    } else {
        $(".paymentrequiredalert5").hide();
        $(".paymentamount5").prop("required", false);
    }
});

$(".requiredalert6").hide();
$(".paymentrequiredalert6").hide();
$(document).on("blur", ".renewalallreceivedamt6", function () {
    var pendingamt = $(".renewalallpendingamt6").val();
    var expecteddate = $(".renewalallexpecteddate6").val();
    var receivedamt = $(this).val();

    if (pendingamt != 0) {
        $(".requiredalert6").show();
        $(".renewalallexpecteddate6").prop("required", true);
    } else {
        $(".renewalallexpecteddate6").prop("required", false);
        $(".requiredalert6").hide();
    }

    if (receivedamt != 0) {
        $(".paymentrequiredalert6").show();
        $(".paymentamount6").prop("required", true);
    } else {
        $(".paymentrequiredalert6").hide();
        $(".paymentamount6").prop("required", false);
    }
});

$(".requiredalert7").hide();
$(".paymentrequiredalert7").hide();
$(document).on("blur", ".renewalallreceivedamt7", function () {
    var pendingamt = $(".renewalallpendingamt7").val();
    var expecteddate = $(".renewalallexpecteddate7").val();
    var receivedamt = $(this).val();

    if (pendingamt != 0) {
        $(".requiredalert7").show();
        $(".renewalallexpecteddate7").prop("required", true);
    } else {
        $(".renewalallexpecteddate7").prop("required", false);
        $(".requiredalert7").hide();
    }

    if (receivedamt != 0) {
        $(".paymentrequiredalert7").show();
        $(".paymentamount7").prop("required", true);
    } else {
        $(".paymentrequiredalert7").hide();
        $(".paymentamount7").prop("required", false);
    }
});

$(".requiredalert8").hide();
$(".paymentrequiredalert8").hide();
$(document).on("blur", ".renewalallreceivedamt8", function () {
    var pendingamt = $(".renewalallpendingamt8").val();
    var expecteddate = $(".renewalallexpecteddate8").val();
    var receivedamt = $(this).val();

    if (pendingamt != 0) {
        $(".requiredalert8").show();
        $(".renewalallexpecteddate8").prop("required", true);
    } else {
        $(".renewalallexpecteddate8").prop("required", false);
        $(".requiredalert8").hide();
    }

    if (receivedamt != 0) {
        $(".paymentrequiredalert8").show();
        $(".paymentamount8").prop("required", true);
    } else {
        $(".paymentrequiredalert8").hide();
        $(".paymentamount8").prop("required", false);
    }
});

$(".requiredalert9").hide();
$(".paymentrequiredalert9").hide();
$(document).on("input", ".renewalallreceivedamt9", function () {
    var renewalamt = $(".campaignrenewalamtedit").val();
    var pendingamt = $(".renewalallpendingamt9").val();
    var expecteddate = $(".renewalallexpecteddate9").val();
    var receivedamt = $(this).val();

    $(this).prop("max", renewalamt);

    if (pendingamt != 0) {
        $(".requiredalert9").show();
        $(".renewalallexpecteddate9").prop("required", true);
    } else {
        $(".renewalallexpecteddate9").prop("required", false);
        $(".requiredalert9").hide();
    }

    if (receivedamt != 0) {
        $(".paymentrequiredalert9").show();
        $(".paymentamount9").prop("required", true);
    } else {
        $(".paymentrequiredalert9").hide();
        $(".paymentamount9").prop("required", false);
    }
});

$(".requiredalert10").hide();
$(".paymentrequiredalert10").hide();
$(document).on("blur", ".renewalallreceivedamt10", function () {
    var pendingamt = $(".renewalallpendingamt10").val();
    var expecteddate = $(".renewalallexpecteddate10").val();
    var receivedamt = $(this).val();

    if (pendingamt != 0) {
        $(".requiredalert10").show();
        $(".renewalallexpecteddate10").prop("required", true);
    } else {
        $(".renewalallexpecteddate10").prop("required", false);
        $(".requiredalert10").hide();
    }

    if (receivedamt != 0) {
        $(".paymentrequiredalert10").show();
        $(".paymentamount10").prop("required", true);
    } else {
        $(".paymentrequiredalert10").hide();
        $(".paymentamount10").prop("required", false);
    }
});

$(".requiredalert11").hide();
$(".paymentrequiredalert11").hide();
$(document).on("blur", ".renewalallreceivedamt11", function () {
    var pendingamt = $(".renewalallpendingamt11").val();
    var expecteddate = $(".renewalallexpecteddate11").val();
    var receivedamt = $(this).val();

    if (pendingamt != 0) {
        $(".requiredalert11").show();
        $(".renewalallexpecteddate11").prop("required", true);
    } else {
        $(".renewalallexpecteddate11").prop("required", false);
        $(".requiredalert11").hide();
    }

    if (receivedamt != 0) {
        $(".paymentrequiredalert11").show();
        $(".paymentamount11").prop("required", true);
    } else {
        $(".paymentrequiredalert11").hide();
        $(".paymentamount11").prop("required", false);
    }
});

$(".requiredalert12").hide();
$(".paymentrequiredalert12").hide();
$(document).on("blur", ".renewalallreceivedamt12", function () {
    var pendingamt = $(".renewalallpendingamt12").val();
    var expecteddate = $(".renewalallexpecteddate12").val();
    var receivedamt = $(this).val();

    if (pendingamt != 0) {
        $(".requiredalert12").show();
        $(".renewalallexpecteddate12").prop("required", true);
    } else {
        $(".renewalallexpecteddate12").prop("required", false);
        $(".requiredalert12").hide();
    }

    if (receivedamt != 0) {
        $(".paymentrequiredalert12").show();
        $(".paymentamount12").prop("required", true);
    } else {
        $(".paymentrequiredalert12").hide();
        $(".paymentamount12").prop("required", false);
    }
});

$(".paymentrequiredalert13").hide();
$(document).on("blur", ".leadproductreceivedcost", function () {
    var receivedamt = $(this).val();

    if (receivedamt != 0) {
        $(".paymentrequiredalert13").show();
        $(".paymentamount13").prop("required", true);
    } else {
        $(".paymentrequiredalert13").hide();
        $(".paymentamount13").prop("required", false);
    }
});

$(".paymentrequiredalert15").hide();
$(document).on("blur", ".leadproductreceivedcostedit", function () {
    var receivedamt = $(this).val();

    if (receivedamt != 0) {
        $(".paymentrequiredalert15").show();
        $(".paymentamount15").prop("required", true);
    } else {
        $(".paymentrequiredalert15").hide();
        $(".paymentamount15").prop("required", false);
    }
});

$(document).ready(function () {
    var pendingamt = $(".renewalallpendingamt9").val();
    var expecteddate = $(".renewalallexpecteddate9").val();
    var receivedamt = $(".renewalallreceivedamt9").val();

    if (pendingamt != 0) {
        $(".requiredalert9").show();
        $(".renewalallexpecteddate9").prop("required", true);
    } else {
        $(".renewalallexpecteddate9").prop("required", false);
        $(".requiredalert9").hide();
    }

    if (receivedamt != 0) {
        $(".paymentrequiredalert9").show();
        $(".paymentamount9").prop("required", true);
    } else {
        $(".paymentrequiredalert9").hide();
        $(".paymentamount9").prop("required", false);
    }
});

$(".requiredalert10").hide();
$(".paymentrequiredalert10").hide();
$(document).ready(function () {
    var pendingamt = $(".renewalallpendingamt10").val();
    var expecteddate = $(".renewalallexpecteddate10").val();

    var receivedamt = $(".renewalallreceivedamt10").val();

    if (pendingamt != 0) {
        $(".requiredalert10").show();
        $(".renewalallexpecteddate10").prop("required", true);
    } else {
        $(".renewalallexpecteddate10").prop("required", false);
        $(".requiredalert10").hide();
    }

    if (receivedamt != 0) {
        $(".paymentrequiredalert10").show();
        $(".paymentamount10").prop("required", true);
    } else {
        $(".paymentrequiredalert10").hide();
        $(".paymentamount10").prop("required", false);
    }
});

$(".requiredalert11").hide();
$(".paymentrequiredalert11").hide();
$(document).ready(function () {
    var pendingamt = $(".renewalallpendingamt11").val();
    var expecteddate = $(".renewalallexpecteddate11").val();
    var receivedamt = $(".renewalallreceivedamt11").val();

    if (pendingamt != 0) {
        $(".requiredalert11").show();
        $(".renewalallexpecteddate11").prop("required", true);
    } else {
        $(".renewalallexpecteddate11").prop("required", false);
        $(".requiredalert11").hide();
    }

    if (receivedamt != 0) {
        $(".paymentrequiredalert11").show();
        $(".paymentamount11").prop("required", true);
    } else {
        $(".paymentrequiredalert11").hide();
        $(".paymentamount11").prop("required", false);
    }
});

// $(document).on("change",".callinfo",function(){

//     var callinfo = $(this).val();

//     $.ajax({
//         type:"GET",
//         url: "/getcallinfo/"+callinfo,
//         // async: false,
//         cache: true,
//         contentType: false,
//         processData: false,

//         success: function(data) {
//             $(".callinfodetails").val(data.callstatusinfo);
//             $(".callinfodetails").trigger("input");
//         }});
// });

// $(document).on("change",".selectall",function(){

//     var isChecked = $(this).prop('checked');
//     alert(isChecked);
//     if(isChecked == true)
//     {
//         $(".callinfocheckbox").prop("checked",true);
//     }
//     else
//     {
//         $(".callinfocheckbox").prop("checked",false);
//     }

// });

$(document).on("change", "#callinfoedit", function () {
    var callinfoedit = $(this).val();

    $.ajax({
        type: "GET",
        url: "/getcallinfo/" + callinfoedit,
        // async: false,
        cache: false,
        contentType: false,
        processData: false,

        success: function (data) {
            $("#leadsdata").val(data.callstatusinfo);
        },
    });
});

$(document).on("change", "#renewalcallinfoedit", function () {
    var callinfoedit = $(this).val();

    $.ajax({
        type: "GET",
        url: "/getcallinfo/" + callinfoedit,
        // async: false,
        cache: false,
        contentType: false,
        processData: false,

        success: function (data) {
            $("#renewalcallinfoedit").val(data.callstatusinfo);
        },
    });
});

// $(document).on("blur",".mblduplicatecheck",function(){

//     var mblnum = $(this).val();

//     $.ajax({
//         type:"GET",
//         url: "/getduplicatemblno/"+mblnum,
//         // async: false,
//         cache: true,
//         contentType: false,
//         processData: false,

//         success: function(data) {

//             $(".mobileerror").text(data);

//             $(this).focus();

//         }});

// });

$(document).on("blur", ".ReceivedCost", function () {
    var ReceivedCost = $(this).val();

    var TotalCost = $(".TotalCost").val();

    var PendingAmount = $(".PendingAmount").val();

    var pending = parseInt(TotalCost) - parseInt(ReceivedCost);

    $(".PendingAmount").val(pending);
});

$(document).on("click", ".converttorenewal", function () {
    var productname = $(this).data("productname");
    var productid = $(this).data("productid");
    var totalcost = $(this).data("totcost");
    var receivedamt = $(this).data("receivedamt");
    var pendingamt = $(this).data("pendingamt");
    $("#convertlead").modal("show");
    $("#convertproductname").val(productname);
    $("#convertproductid").val(productid);
    $(".leadconverttotalcost").val(totalcost);
    $(".leadconvertreceivedcost").val(receivedamt);
    $(".leadconvertpendingcost").val(pendingamt);
});

$(document).on("change", "#dashboardfromdate", function () {
    var dashboardfromdate = $(this).val();
    $("#dashboardtodate").prop("min", dashboardfromdate);
});

$(document).on("keyup", ".leadconvertreceivedcost", function () {
    var leadconverttotalcost = parseInt($(".leadconverttotalcost").val());
    var leadconvertreceivedcost = parseInt($(".leadconvertreceivedcost").val());
    var leadconvertpendingcost = parseInt($(".leadconvertpendingcost").val());

    var pending =
        parseInt(leadconverttotalcost) - parseInt(leadconvertreceivedcost);

    $(".leadconvertpendingcost").val(pending);
});
$(document).on("input", ".producttotalcostedit", function () {
    var totalcost = $(this).val();
    var productreceivedcostedit = $(".productreceivedcostedit").val();
    $(".productpendingcostedit").val(totalcost - productreceivedcostedit);
});

$(document).on("change", "#filterfromdate", function () {
    var filterfromdate = $(this).val();
    $("#filtertodate").prop("min", filterfromdate);
});

$(document).on("input", ".productpendingpayment", function () {
    var pendingpayment = $("#pendingpayment").val();
    $(this).prop("max", pendingpayment);
});

$(document).on("click", ".cancelproduct", function () {
    var productid = $(this).data("productid");
    var leadid = $(this).data("leadid");
    var totpayment = $(this).data("totpayment");
    var receivedcost = $(this).data("receivedcost");
    var pendingcost = $(this).data("pendingcost");

    $("#cancelproduct").modal("show");

    $("#canceltotpayment").val(totpayment);
    $("#cancelreceivedamount").val(receivedcost);
    $("#cancelrefundamount").val(receivedcost);
    $("#cancelproductid").val(productid);
});

// $(document).on("click",".addleadpayment",function(){

//     var totpayment = $(this).data("totpayment");
//     var receivedcost = $(this).data("receivedcost");
//     var pendingcost = $(this).data("pendingcost");

//     $("#addpaymentmodal").modal('show');
//     $("#totalcost").val(totpayment);
//     $("#pendingpayment").val(pendingcost);
//     $("#totreceivedcost").val(receivedcost);
//     $("#addpaymentinput").attr("max",pendingcost);
// });

$(document).on("submit", "#cancelleadproduct", function () {
    $.ajax({
        type: "POST",
        url: "/cancelleadproduct",
        data: new FormData(this),
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#responseloader").fadeIn();
        },
        success: function (data) {
            $("#responseloader").fadeOut();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Refund Added..",
                showConfirmButton: false,
                timer: 1500,
            });

            location.reload();
        },
        error: function (data) {
            Swal.fire("Error!", "Error.", "success");
        },
    });
});

$(".paymentrequiredalert14").hide();
$(document).on("blur", ".leadconvertreceivedcost", function () {
    var receivedamt = $(this).val();

    if (receivedamt != 0) {
        $(".paymentrequiredalert14").show();
        $(".paymentdate14").prop("required", true);
    } else {
        $(".paymentrequiredalert14").hide();
        $(".paymentdate14").prop("required", false);
    }
});

$(document).ready(function () {
    // Function to show the modal
    function showModal() {
        $("#followupoverdue").modal({
            backdrop: "static",
            keyboard: false,
        });
        $("#followupoverdue").modal("show");
    }

    // Initial call to show modal on document ready
    showModal();
});

$(document).ready(function () {
    // Select the button with the class "btn-close" using jQuery
    var btnClose = $(".not_close");

    // Add a click event listener to the button
    btnClose.click(function () {
        // Display a SweetAlert when the button is clicked
        Swal.fire({
            title: "Followup Overdue",
            text: "Please complete the followup overdue.",
            icon: "warning",
            confirmButtonText: "OK",
        });
    });
});

$("#addUserButton").on("click", function () {
    // Simulating the user already exists scenario (replace with your actual logic)
    var userExists = true;

    if (userExists) {
        // Show SweetAlert with a warning
        Swal.fire({
            icon: "warning",
            title: "Warning",
            text: "Maximum User Limit Reached ",
            // confirmButtonColor: "#F9A100",
        });
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            },
        });

        Toast.fire({
            icon: "warning",
            title: "Maximum User Limit Reached",
        });

        // setTimeout(function () {
        //     window.location.href = "/";
        // }, 1500);
    } else {
        // Your logic for adding the user goes here
        // ...

        // Show success message (replace with your actual success message)
        Swal.fire({
            icon: "success",
            title: "Success",
            text: "User added successfully",
            confirmButtonColor: "#F9A100",
        });

        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            },
        });

        Toast.fire({
            icon: "success",
            title: "User added successfully",
        });

        // setTimeout(function () {
        //     window.location.href = "/";
        // }, 1500);
    }
});

$("#restrictmeet").on("click", function () {
    // Simulating the user already exists scenario (replace with your actual logic)
    var userExists = true;

    if (userExists) {
        // Show SweetAlert with a warning
        Swal.fire("Warning", "A meet is already scheduled", "warning");

        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            },
        });

        Toast.fire({
            icon: "warning",
            title: "A meet is already scheduled",
        });
    } else {
        // Your logic for adding the user goes here
        // ...

        // Show success message (replace with your actual success message)
        Swal.fire({
            icon: "success",
            title: "Success",
            text: "User added successfully",
            confirmButtonColor: "#F9A100",
        });
    }
});
$("#restrictcall").on("click", function () {
    // Simulating the user already exists scenario (replace with your actual logic)
    var userExists = true;

    if (userExists) {
        // Show SweetAlert with a warning
        Swal.fire("warning", "Warning", "A call is already scheduled");

        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            },
        });

        Toast.fire({
            icon: "warning",
            title: "A call is already scheduled",
        });
    } else {
        // Your logic for adding the user goes here
        // ...

        // Show success message (replace with your actual success message)
        Swal.fire({
            icon: "success",
            title: "Success",
            text: "User added successfully",
            confirmButtonColor: "#F9A100",
        });
    }
});

// Allwyn
// LOGOUT
$(document).ready(function () {
    $(document).on("click", "#user_Log_off", function () {
        Swal.fire({
            title: "Are you sure?",
            text: "You want to Log Out?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Log Out",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                });
                $.ajax({
                    url: "/logout",
                    method: "GET",
                    success: function (response) {
                        swal.fire(
                            "Success",
                            "Logged Out Succesfully",
                            "success"
                        );

                        const Toast = Swal.mixin({
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            },
                        });

                        Toast.fire({
                            icon: "success",
                            title: "Logged Out Succesfully",
                        });

                        setTimeout(function () {
                            window.location.href = "/";
                        }, 1500);
                    },
                    error: function (data) {
                        Swal.fire("Error!", "Log Out failed", "success");
                    },
                });
            }
        });
    });
});

$(document).ready(function () {
    $(".activitydate ").on("keydown", function (event) {
        event.preventDefault();
    });
});

$(document).ready(function () {
    $(".activitydate ").on("change", function (event) {
        currentDate = $(".activitydate").val();

        $(".endtime").attr("min", currentDate);
    });
});

$(document).ready(function () {
    $(document).on("click", ".reportajax", function () {
        var userid = $(this).data("userid");
        var weekid = $(this).data("weekid");

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "/reportajaxx",
            method: "post",
            data: { user_id: userid, weekid: weekid },
            success: function (response) {
                var data = response.data; // Access the 'data' object
                console.log("lastdata", data);

                var tableBody = $(".table .reportdata");
                tableBody.empty(); // Clear existing rows

                $.each(data.status_counts, function (status, count) {
                    var amount = data.status_total_amounts[status];

                    var row =
                        "<tr>" +
                        "<td>" +
                        status +
                        "</td>" +
                        "<td>" +
                        count +
                        "</td>" +
                        "<td>" +
                        amount +
                        "</td>" +
                        '<td><a class="reportsinajax" data-bs-toggle="modal" data-bs-target="#erpid" data-userid="' +
                        userid +
                        '" data-weekid="' +
                        weekid +
                        '" data-status="' +
                        status +
                        '"><i class="fa fa-eye" style="color: #000;" aria-hidden="true"></i></a></td>' +
                        "</tr>";

                    tableBody.append(row);
                });
            },
        });
    });
    $(document).on("click", ".reportsinajax", function () {
        var userId = $(this).data("userid");
        var weekId = $(this).data("weekid");
        var status = $(this).data("status");

        // Make an AJAX request to fetch the data associated with the count
        $.ajax({
            url: "/reportajaxdata",
            method: "post",
            data: { userId: userId, weekId: weekId, status: status },
            success: function (response) {
                // Clear existing rows
                $(".ededdd").empty();

                // Loop through the response and generate table rows
                $.each(response.report, function (index, item) {
                    // var productName = item.actual_productname ? item.actual_productname : (item.indiamart_proname ? item.indiamart_proname : '');
                    var row =
                        "<tr>" +
                        "<td>" +
                        item.productname +
                        "</td>" +
                        "<td>" +
                        item.ClientName +
                        "</td>" +
                        "<td>" +
                        item.MobileNumber +
                        "</td>" +
                        '<td><a style="cursor:pointer" onclick="getleadinfo(' +
                        item.leadid +
                        ')" ><i class="fa fa-eye" aria-hidden="true"></i></a></td>' +
                        "</tr>";
                    $(".ededdd").append(row);
                });

                // Show the modal
                $("#erpid").modal("show");
            },
        });
    });
    // wire:click.prevent="getleadinfo(' + item.leadid + ')"
});
function getleadinfo(id) {
    Livewire.emit("getleadinfo", id);
}

$(".filtersidebartri").click(function () {
    $(".filtertri").toggleClass("show");
    // alert("data");
});

$(document).ready(function () {
    var today = new Date().toISOString().split("T")[0];
    $("#payment_0date").attr("max", today);
});

$(document).ready(function () {
    $("#deletionForm").on("submit", function (e) {
        e.preventDefault(); // Prevent the default form submission

        $.ajax({
            method: "POST",
            url: $(this).attr("action"), // Use the form's action URL
            data: $(this).serialize(), // Serialize form data
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500,
                    }).then(() => {
                        window.location.href = "/form"; // Redirect after success
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: response.message,
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Oops!",
                    text: "Something went wrong. Please try again.",
                });
            },
        });
    });
});

// FB LEADS INTEGRATION SCRIPTS

$(document).ready(function () {
    $(document).on("click", "#fbinitial", function (event) {
        event.preventDefault(); // Prevent form submission

        var adid = $("#adid").val();
        var accesstoken = $("#accessToken").val();

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            // url: "/connectfb",
            url: "/multiple_campaigns",
            method: "post",
            data: { adid: adid, accesstoken: accesstoken },
            success: function (response) {
                if (response) {
                    $(".fbloginaccess").hide(); // Hide the login fields
                    $(".man").html(response.view); // Insert the rendered view into the element with class 'man'
                } else {
                    console.error("Unexpected response format:", response);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX request failed:", status, error);
            },
        });
    });

    $(document).on("click", "#chooseCampaigns", function (event) {
        event.preventDefault(); // Prevent form submission

        var selectedCampaigns = [];
        $(".campaign_select:checked").each(function () {
            selectedCampaigns.push($(this).val());
        });

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            // url: "/connectfb",
            url: "/choose_camps",
            method: "post",
            data: {
                selectedCampaigns: selectedCampaigns,
            },
            success: function (response) {
                if (response) {
                    $("#multipleCampaignSelect").hide();
                    $(".man").html(response.view);
                } else {
                    console.error("Unexpected response format:", response);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX request failed:", status, error);
            },
        });
    });

    $(document).on("click", "#map_multiple_fields", function (event) {
        event.preventDefault(); // Prevent form submission

        var adid = $("#map_ad_id").val();
        var accesstoken = $("#map_access_token").val();
        var cam_id = $("#map_campaign_id").val();

        // Collect CRM and Campaign field pairs
        var fieldMappings = [];
        $(".crm-field-select").each(function (index) {
            var crmFieldId = $(this).val();
            var campaignFieldId = $(".campaign-field-select").eq(index).val();
            fieldMappings.push({
                crmFieldId: crmFieldId,
                campaignFieldId: campaignFieldId,
            });
        });

        let campaignsArray = [];

        $(".map_campaigns").each(function () {
            campaignsArray.push($(this).val());
        });

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "/mapfields",
            method: "post",
            data: {
                adid: adid,
                accesstoken: accesstoken,
                fieldMappings: fieldMappings,
                cam_id: cam_id,
                cams: campaignsArray,
            },
            success: function (response) {
                if (response && response.cam_id) {
                    $(".fbloginaccess").hide(); // Hide the login fields
                    $(".man").html(response.view); // Insert the rendered view into the element with class 'man'
                } else {
                    console.error("Unexpected response format:", response);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX request failed:", status, error);
            },
        });
    });

    $(document).on("click", "#map_fb_fields", function (event) {
        event.preventDefault(); // Prevent form submission

        var adid = $("#map_ad_id").val();
        var accesstoken = $("#map_access_token").val();
        var cam_id = $("#map_campaign_id").val();

        // Collect CRM and Campaign field pairs
        var fieldMappings = [];
        $(".crm-field-select").each(function (index) {
            var crmFieldId = $(this).val();
            var campaignFieldId = $(".campaign-field-select").eq(index).val();
            fieldMappings.push({
                crmFieldId: crmFieldId,
                campaignFieldId: campaignFieldId,
            });
        });

        console.log(campaignsArray);

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "/mapfields",
            method: "post",
            data: {
                adid: adid,
                accesstoken: accesstoken,
                fieldMappings: fieldMappings,
                cam_id: cam_id,
                cams: campaignsArray,
            },
            success: function (response) {
                if (response) {
                    $(".fbloginaccess").hide(); // Hide the login fields
                    $(".man").html(response.view); // Insert the rendered view into the element with class 'man'
                } else {
                    console.error("Unexpected response format:", response);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX request failed:", status, error);
            },
        });
    });

    $(document).on("click", "#getfacebookintegration", function (event) {
        event.preventDefault(); // Prevent form submission

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "/facebook_integration",
            method: "get",
            success: function (response) {
                if (response) {
                    $("#fbfieldsappend").html(response.view); // Insert the rendered view into the element with class 'man'
                } else {
                    console.error("Unexpected response format:", response);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX request failed:", status, error);
            },
        });
    });

    // BACK FUNCTIONALITY
    $(document).on("click", ".pinnala_poganu", function (event) {
        event.preventDefault(); // Prevent form submission

        window.history.back();
    });

    $(document).on("click", ".leadmapsubmit", function (event) {
        event.preventDefault();

        // var assigned = $("#fbassignleads").val();
        // var camid = $("#leadmappingcamid").val();

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        var camArray = [];

        $(".leadmappingcamid").each(function (index) {
            var campaignId = $(this).val();
            var assignedUsers = $("select[name='fbassignleads[]']")
                .eq(index)
                .val();
            camArray.push({
                campaignId: campaignId,
                assigned: assignedUsers || [], // Ensure assignedUsers is an array
            });
        });

        $.ajax({
            url: "/fbassignleads",
            method: "post",
            data: {
                // assigned: assigned,
                // camid: camid,
                cams: camArray,
            },
            success: function (response) {
                if (response) {
                    $("#fbfieldsappend").html(response.view); // Insert the rendered view into the element with class 'man'
                } else {
                    console.error("Unexpected response format:", response);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX request failed:", status, error);
            },
        });
    });

    $(document).on("click", "#getfacebookassignlead", function () {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "/viewassigned",
            method: "get",
            success: function (response) {
                if (response) {
                    $("#fbfieldsappend").html(response.view); // Insert the rendered view into the element with class 'man'
                } else {
                    console.error("Unexpected response format:", response);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX request failed:", status, error);
            },
        });
    });
});


$(document).ready(function () {
    $('#lead_state_list').on('change', function () {
        var state_id = $(this).val();  // Use 'state_id' instead of 'company_id'

        // Retrieve CSRF token from the meta tag
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        if (state_id) {
            $.ajax({
                type: 'POST',
                url: '/getfandfreports',
                data: {
                    state_id: state_id  // Make sure to send 'state_id'
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (data) {
                    $('[name="citylist"]').html(data.citylist);
                }
            });
        } else {
            $('[name="citylist"]').html('<option value="">-- Choose City --</option>');
        }
    });
});


$(document).ready(function () {
    $('#state_listsh').on('change', function () {
        var state_id = $(this).val();  // Use 'state_id' instead of 'company_id'

        // Retrieve CSRF token from the meta tag
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        if (state_id) {
            $.ajax({
                type: 'POST',
                url: '/shifandfreports',
                data: {
                    state_id: state_id  // Make sure to send 'state_id'
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (data) {
                    let currentTarget = $('#shicity_list').attr('data-selected') || $('#shicity_list').val();
                    $('[name="shcitylist"]').html(data.shcitylist);
                    if (currentTarget) {
                        $('#shicity_list').val(currentTarget);
                    }
                }
            });
        } else {
            $('[name="shcitylist"]').html('<option value="">-- Choose City --</option>');
        }
    });
});


    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.edit-btnb').forEach(button => {
            button.addEventListener('click', function() {
                let productId = this.getAttribute('data-productid');
                let statusLead = this.getAttribute('data-status');
                let leadId = this.getAttribute('data-leadid');

                // Set modal values
                document.getElementById('productid').value = productId;
                document.getElementById('leadid').value = leadId;

                // Set status dropdown
                let statusDropdown = document.getElementById('statuslead');
                if (statusDropdown) {
                    for (let option of statusDropdown.options) {
                        if (option.value == statusLead) {
                            option.selected = true;
                            break;
                        }
                    }
                }
            });
        });
    });


    $(document).ready(function () {
        $('#catepro').on('change', function () {
            var category = $(this).val();

            // Retrieve CSRF token from the meta tag
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            if (category) {
                $.ajax({
                    type: 'POST',
                    url: '/getProductGroup',
                    data: {
                        category: category
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (resopnse) {
                        $('#lead_sub_category_id').html(resopnse.data);
                    }
                });
            } else {
                $('#lead_sub_category_id').html('<option value="">-- Not Available --</option>');
            }
        });
        $('#lead_sub_category_id').on('change', function () {
            var category = $("#catepro").val();
            var subCategory = $(this).val();

            // Retrieve CSRF token from the meta tag
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            if (category) {
                $.ajax({
                    type: 'POST',
                    url: '/getfandfrep',
                    data: {
                        category: category,
                        subCategory: subCategory
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (data) {
                        $('[name="products"]').html(data.products);
                    }
                });
            } else {
                $('[name="products"]').html('<option value="">-- Choose Products --</option>');
            }
        });
    });


    $(document).ready(function () {
        function loadCustomerCities(state) {

            // Retrieve CSRF token from the meta tag
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            if (state) {
                $.ajax({
                    type: 'POST',
                    url: '/getfandfrepor',
                    data: {
                        state: state  // Make sure to send 'state_id'
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (data) {
                        $('[name="city"]').html(data.city).trigger('change.select2');
                    }
                });
            } else {
                $('[name="city"]').html('<option value="">-- Choose City --</option>').trigger('change.select2');
            }
        }

        $('#state_listt').on('change', function () {
            loadCustomerCities($(this).val());
        });

        if ($('#state_listt').val()) {
            loadCustomerCities($('#state_listt').val());
        }
    });

    // var updateStatusUrl = "{{ route('lead.updateStatusdf') }}";

    // $(document).ready(function() {
    //     // Open modal and set lead ID
    //     $(document).on('click', '.change-status-btn', function() {
    //         var leadId = $(this).data('id');
    //         $('#lead_id').val(leadId);
    //         $('#statusModal').modal('show');
    //     });

    //     // Submit the status change form
    //     $('#changeStatusForm').on('submit', function(e) {
    //         e.preventDefault();

    //         $.ajax({
    //             url: updateStatusUrl,
    //             method: "POST",
    //             data: $(this).serialize(),
    //             success: function(response) {
    //                 if (response.success) {
    //                     alert(response.message);
    //                     $('#statusModal').modal('hide');
    //                     $('#leads').DataTable().ajax.reload(); // Reload DataTable
    //                 } else {
    //                     alert(response.message);
    //                 }
    //             },
    //             error: function(xhr) {
    //                 console.log(xhr.responseText);
    //             }
    //         });
    //     });
    // });


    // document.addEventListener("DOMContentLoaded", function () {
    //     let statusDropdown = document.getElementById("status");
    //     let convertedOptionsDiv = document.getElementById("convertedOptionsDiv");

    //     statusDropdown.addEventListener("change", function () {
    //         let selectedOption = statusDropdown.options[statusDropdown.selectedIndex];
    //         let selectedText = selectedOption.getAttribute("data-status");

    //         if (selectedText === "CONVERTED") {
    //             convertedOptionsDiv.style.display = "block";
    //         } else {
    //             convertedOptionsDiv.style.display = "none";
    //         }
    //     });
    // });


document.addEventListener("DOMContentLoaded", function () {
    let statusDropdown = document.getElementById("leadstatus");
    let convertedOptionsDiv = document.getElementById("convertedOptionsDiv");
    let categoryDropdown = document.getElementById("catepro");
    let specialField = document.getElementById("specialField");

    let convertedDate = document.getElementById("converteddate");
    let pprod = document.getElementById("pprod");
    let quantity = document.getElementById("quantity");

    // Helper function to toggle 'required' attribute
    function toggleRequiredFields(isRequired) {
        convertedDate.required = isRequired;
        categoryDropdown.required = isRequired;
        pprod.required = isRequired;
        quantity.required = isRequired;
    }

    // Show converted options and apply 'required' conditionally
    statusDropdown.addEventListener("change", function () {
        let selectedOption = statusDropdown.options[statusDropdown.selectedIndex];
        let selectedText = selectedOption.getAttribute("data-status");

        if (selectedText === "WON") {
            convertedOptionsDiv.style.display = "block";
            toggleRequiredFields(true);
        } else {
            convertedOptionsDiv.style.display = "none";
            toggleRequiredFields(false);
            specialField.style.display = "none";
            document.getElementById("special_input").required = false;
        }
    });

    // Show special field and make it required if category is "special"
    categoryDropdown.addEventListener("change", function () {
        let selectedOption = categoryDropdown.options[categoryDropdown.selectedIndex];
        let subCategory = selectedOption.getAttribute("data-sub");

        if (subCategory === "special") {
            specialField.style.display = "block";
            document.getElementById("special_input").required = true;
        } else {
            specialField.style.display = "none";
            document.getElementById("special_input").required = false;
        }
    });
});





    document.addEventListener("DOMContentLoaded", function () {
        function calculateValues() {
            let quantity = parseFloat(document.querySelector("[name='quanty']").value) || 0;
            let rate = parseFloat(document.querySelector("[name='ratee']").value) || 0;
            // let allowdis = parseFloat(document.querySelector("[name='allowdis']").value) || 0;
            let requestdis = parseFloat(document.querySelector("[name='requestdis']").value) || 0;
            let taxPercent = parseFloat(document.querySelector("[name='tax']").value) || 0;

            let baseTotal = quantity * rate;
            let discount = (baseTotal * requestdis) / 100;
            // let discount = 0;
            // if (requestdis > 0) {
            //     discount = (baseTotal * requestdis) / 100;
            // } else if (allowdis > 0) {
            //     discount = (baseTotal * allowdis) / 100;
            // }

            let lineBasicTotal = baseTotal - discount;
            let taxAmount = (lineBasicTotal * taxPercent) / 100;
            let grandTotal = lineBasicTotal + taxAmount;

            document.querySelector("[name='lbt']").value = lineBasicTotal.toFixed(2);
            document.querySelector("[name='taxamt']").value = taxAmount.toFixed(2);
            document.querySelector("[name='grandtotal']").value = grandTotal.toFixed(2);
        }

        // Initial calculation when the page loads
        calculateValues();

        // Event listeners for live calculation
        document.querySelector("[name='quanty']").addEventListener("keyup", calculateValues);
        document.querySelector("[name='requestdis']").addEventListener("keyup", calculateValues);
        // document.querySelector("[name='allowdis']").addEventListener("keyup", calculateValues);
    });



    // function copyAddress() {
    //     const billingAddress = document.getElementById('billingAddress').value;
    //     document.getElementById('shippingAddress').value = billingAddress;
    // }
function copyAddress() {
    // Copy address fields
    document.getElementById('shippingAddress').value = document.getElementById('billingAddress').value;
    document.getElementById('shipcountry').value = document.getElementById('country').value;

    const billingState = document.getElementById('state_list').value;
    const billingCity = document.getElementById('city_list').value;

    // Set state and trigger AJAX for cities
    $('#state_listsh').val(billingState).trigger('change');

    // Wait for AJAX to complete before setting the city
    $(document).one('ajaxStop', function () {
        $('#shicity_list').val(billingCity);
    });

    // Copy postal code and GST
    document.getElementById('shippostalcode').value = document.getElementById('postalcode').value;
    document.getElementById('gstnum').value = document.getElementById('billgs').value;
}

    // function copyAddress() {
    //     // Copy address
    //     const billingAddress = document.getElementById('billingAddress').value;
    //     document.getElementById('shippingAddress').value = billingAddress;

    //     // Copy country
    //     const billingCountry = document.getElementById('country').value;
    //     document.getElementById('shipcountry').value = billingCountry;

    //     // Copy state
    //     const billingState = document.getElementById('state_list').value;
    //     document.getElementById('state_listsh').value = billingState;

    //     // Trigger change to load the corresponding cities dynamically
    //     $('#state_listsh').trigger('change');

    //     // Copy city after slight delay to ensure cities are loaded
    //     setTimeout(() => {
    //         const billingCity = document.getElementById('city_list').value;
    //         document.getElementById('shicity_list').value = billingCity;
    //     }, 500);

    //     // Copy postal code
    //     const billingPostalCode = document.getElementById('postalcode').value;
    //     document.getElementById('shippostalcode').value = billingPostalCode;

    //     // Copy GST number
    //     const billinggst = document.getElementById('billgs').value;
    //     document.getElementById('gstnum').value = billinggst;
    // }

    function clearAddress() {
        // Clear all shipping fields
        document.getElementById('shippingAddress').value = '';
        document.getElementById('shipcountry').value = '';
        document.getElementById('state_listsh').value = '';
        document.getElementById('shicity_list').value = '';
        document.getElementById('shippostalcode').value = '';
        document.getElementById('gstnum').value = '';
    }

    function toggleAddress() {
        const checkbox = document.getElementById('sameAsBilling');
        if (checkbox.checked) {
            copyAddress();  // Copy address if checkbox is checked
        } else {
            clearAddress();  // Clear fields if checkbox is unchecked
        }
    }




    // document.querySelector('.add-more').addEventListener('click', function() {
    //     const fieldGroup = document.querySelector('.field-group');
    //     const newFieldGroup = fieldGroup.cloneNode(true);

    //     // Clear the values for cloned inputs
    //     newFieldGroup.querySelectorAll('input').forEach(input => input.value = '');

    //     // Remove the add button if exists
    //     const addButton = newFieldGroup.querySelector('.add-more');
    //     if (addButton) addButton.remove();

    //     // Create the remove button
    //     const removeBtnDiv = document.createElement('div');
    //     removeBtnDiv.classList.add('col-lg-1', 'mt-2',);

    //     const removeBtn = document.createElement('button');
    //     removeBtn.type = 'button';
    //     removeBtn.classList.add('remove-field', 'btn', 'btn-danger', 'w-100');
    //     removeBtn.textContent = '-';

    //     removeBtnDiv.appendChild(removeBtn);
    //     newFieldGroup.appendChild(removeBtnDiv);

    //     document.getElementById('dynamic-fields').appendChild(newFieldGroup);
    // });

    // // Handle remove button click
    // document.addEventListener('click', function(e) {
    //     if (e.target && e.target.classList.contains('remove-field')) {
    //         if (document.querySelectorAll('.field-group').length > 1) {
    //             e.target.closest('.field-group').remove();
    //         }
    //     }
    // });

// Function to calculate and update the price value








