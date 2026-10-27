$(document).ready(function () {
    $(".designedit").on("click", function () {
        var id = $(this).data("id");
        var designation = $(this).data("designatio");
        var start_time = $(this).data("start_time");
        var end_time = $(this).data("end_time");
        var morning_buffer_time = $(this).data("morning_buffer_time");
        var evening_buffer_time = $(this).data("evening_buffer_time");

        function convertTo24Hour(time) {
            if (!time) return ""; // Handle undefined or empty time values
            const [timePart, modifier] = time.split(" ");
            let [hours, minutes] = timePart.split(":");

            hours = parseInt(hours, 10); // Convert hours to integer for comparison
            if (modifier === "PM" && hours !== 12) {
                hours += 12;
            } else if (modifier === "AM" && hours === 12) {
                hours = 0;
            }

            hours = String(hours); // Ensure hours is a string before padding
            console.log("Converted Hours: ", hours);

            return `${hours.padStart(2, "0")}:${minutes}`;
        }


        function extractTimeOnly(datetimeStr) {
                if (!datetimeStr) return "";
                const parts = datetimeStr.split(" ");
                return parts.length === 2 ? parts[1].substring(0, 5) : "";
            }


        $(".editids").val(id);
        $(".designa").val(designation);
            $("#starttimeu").val(extractTimeOnly(start_time));
            $("#endtimeu").val(extractTimeOnly(end_time));
        $("#startbuftimeu").val(convertTo24Hour(morning_buffer_time));
        $("#endbuftimeu").val(convertTo24Hour(evening_buffer_time));
    });
});

$(document).ready(function () {
    $(".esipfedit").on("click", function () {
        var id = $(this).data("idd");
        var esi = $(this).data("esi");
        var epf = $(this).data("epf");
        var esiyer = $(this).data("esiyer");
        var epfer = $(this).data("epfer");

        $("#pfid").val(id);
        $("#esiid").val(esi);
        $("#epfid").val(epf);
        $("#esiyer").val(esiyer);
        $("#erepfid").val(epfer);
    });
});

$(document).on("click", "#copyaddress", function () {
    var temaddress = $("#temp_address").val();

    $("#per_address").val(temaddress);
});

$(document).on("click", "#copyaddressedit", function () {
    var temaddress = $("#temp_addressedit").val();

    $("#per_addressedit").val(temaddress);
});

$(document).ready(function () {
    var html = `
        <tr>
        <td>
            <input class="form-control f_name" type="text" name="f_name[]"  placeholder="Name">
        </td>
        <td>
            <input class="form-control f_relation" type="text" name="f_relation[]"  placeholder="Relation">
        </td>
        <td>
            <input class="form-control f_mobile" type="text" name="f_mobile[]"  placeholder="Mobile Number">
        </td>
        <td>
            <input class="form-control f_email" type="text" name="f_email[]"  placeholder="Email">
        </td>
        <td>
            <button class="btn btn-danger remove">X</button>
        </td>
        </tr>`;
    $("#addProduct1").click(function () {
        // alert("Hellow");
        $("#app1").append(html);
    });

    $(document).on("click", ".remove", function () {
        $(this).parents("tr").remove();
    });

    var html2 = `<tr>
<td>
<select class="form-control" name="level[]">
<option value="">Choose Level</option>
<option value="Tenth">Tenth</option>
<option value="Twelth">Twelth</option>
<option value="UG">UG</option>
<option value="PG">PG</option>
<option value="Others">Others</option>
</select>
</td>
<td>
<input class="form-control" type="text" name="degree[]" placeholder="Board" >
</td>
<td>
<input class="form-control" type="text" name="university[]"	placeholder="Institure">
</td>
<td>
<input class="form-control" type="text" name="passedout[]" placeholder="Pass out"></td>
<td>
<input class="form-control" type="file" name="document[]" placeholder="Pass out" >
</td>
<td>
<button type="button" class="btn btn-danger remove2">X</button>
</td>
</tr>`;
    $("#addProduct").click(function () {
        $("#app2").append(html2);
    });

    $(document).on("click", ".remove2", function () {
        $(this).parents("tr").remove();
    });

    var html3 =
        "<tr>" +
        '<td><input class="form-control exp_start_date" type="date" name="exp_start_date[]" placeholder=""></td>' +
        '<td><input class="form-control exp_end_date" type="date" name="exp_end_date[]" placeholder=""></td>' +
        '<td><input class="form-control perioed" type="text" name="period[]" placeholder="Period"></td>' +
        '<td><input class="form-control rrole" type="text" name="role[]" placeholder="Role"></td>' +
        '<td><input class="form-control companyy" type="text" name="company[]" placeholder="Company"></td>' +
        '<td><input class="form-control dedis" type="text" name="empdesignation[]" placeholder="Designation"></td>' +
        '<td><button type="button" class="btn btn-danger remove3">X</button></td>' +
        "</tr>";
    $("#addexperiencedetails").click(function () {
        $("#app3").append(html3);
    });

    $(document).on("click", ".remove3", function () {
        $(this).parents("tr").remove();
    });
});

$(document).ready(function () {
    $("#adddesignation1").submit(function (e) {
        e.preventDefault(); // Prevent the default form submission

        // Get form data
        var formData = $(this).serialize();

        // Send AJAX request
        $.ajax({
            type: "POST",
            url: "designationinsert", // Specify the URL to handle the form data
            data: formData,
            success: function (response) {
                // If successful, close the modal and update the select options
                $("#desimodal1").modal("hide");
                // var newDesignation = $('#designation_input').val();
                var newDesignation = response.designation;
                var newDesignationId = response.id;
                // // dd(newDesignation);
                $("#designation").append(
                    $("<option>", {
                        value: newDesignationId,
                        text: newDesignation,
                    })
                );
                alert("Department added successfully!");
            },
            error: function (data) {
                // If there's an error, handle it here
                console.log("Error:", data);
                alert("Error occurred while adding designation!");
            },
        });
    });
});

$(document).ready(function () {
    $("#advancetype1").submit(function (e) {
        e.preventDefault(); // Prevent the default form submission

        // Get form data
        var formData = $(this).serialize();

        // Send AJAX request
        $.ajax({
            type: "POST",
            url: "advancetypeinsert", // Specify the URL to handle the form data
            data: formData,
            success: function (response) {
                var newDesignation = response.advance_type;
                var newDesignationId = response.id;
                // If successful, close the modal and update the select options
                $("#advaentry").modal("hide");
                // var newDesignation = $('#advancetype_input').val();
                $("#select_adtype").append(
                    $("<option>", {
                        value: newDesignationId,
                        text: newDesignation,
                    })
                );
                alert("Advance Type added successfully!");
            },
            error: function (data) {
                // If there's an error, handle it here
                console.log("Error:", data);
                alert("Error occurred while adding Advance Type!");
            },
        });
    });
});

// var html1 =
//     `<tr>
//             <td>
//             <span id="msg"></span><select id="multiple-checkboxes" name="assets[]" class="form-control select2" style="margin-right:10px;">
//             <option>Please Select asset</option>

//             </select>
//             </td>
//             <td>
//             <input type="number" min="0" name="qty[]" class="form-control">
//             </td>
//             <td><button type="button" id="del_row" class="btn btn-danger remove1">X</button>
//             </td>
//             </tr>`;
// $("#add_row").click(function () {
//     alert("Hellow");
//     $('#assetr').append(html1);
// });


    let assetList = [];

    $(document).ready(function () {
        // Load initial asset list
        $.get("/gettsupprod", function (response) {
            assetList = response;
        });

        // Handle Add Asset form submit via AJAX
        $("#addasset1").submit(function (e) {
            e.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
                type: "POST",
                url: "addnewassetinsert",
                data: formData,
                success: function (response) {
                    $("#addnewasset").modal("hide");

                    // Add new asset to global asset list
                    assetList.push(response);

                    let serial = response.serial_number ? response.serial_number.trim() : "";
                    let displayText = serial && serial !== "|" ? `${response.asset_name} | ${serial}` : response.asset_name;

                    // Add new option to existing dropdowns
                    $("select[name='assets[]']").each(function () {
                        $(this).append(
                            $("<option>", {
                                value: response.id,
                                text: displayText,
                            })
                        );
                    });

                    // Reset the form
                    $("#addasset1")[0].reset();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Asset added successfully!',
                    });
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to add asset.',
                    });
                }
            });
        });

        // Template for new row
        const rowTemplate = `
            <tr>
                <td>
                    <span id="msg"></span>
                    <select name="assets[]" class="form-control common asss">
                        <option value="">-- Select Asset --</option>
                    </select>
                </td>
                <td>
                    <input type="number" min="1" name="qty[]" placeholder="QTY" class="form-control" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger remove1">X</button>
                </td>
            </tr>
        `;

        // Add row on button click
        $("#add_row").on("click", function () {
            const newRow = $(rowTemplate);
            $("#file_field").append(newRow);

            const assetSelect = newRow.find(".asss");
            assetSelect.empty().append(`<option value="">-- Select Asset --</option>`);

            const selectedValues = $(".common").map(function () {
                return this.value;
            }).get();

            assetList.forEach(function (element) {
                const id = element.id;
                const name = element.asset_name;
                const serial = element.serial_number ? element.serial_number.trim() : "";
                const displayText = serial && serial !== "|" ? `${name} | ${serial}` : name;
                const isDisabled = selectedValues.includes(id.toString());

                assetSelect.append(
                    `<option value="${id}" ${isDisabled ? 'disabled' : ''}>${displayText}</option>`
                );
            });
        });

        // Remove row
        $(document).on("click", ".remove1", function () {
            $(this).closest("tr").remove();
        });
    });



    $(document).on("click", "#remove", function () {
        const removedRow = $(this).closest(".row");
        const removedValue = removedRow.find(".common").val();
        removedRow.remove();

        $(".common").each(function () {
            $(this)
                .find("option")
                .each(function () {
                    if (this.value === removedValue) {
                        $(this).prop("disabled", false);
                    }
                });
        });
    });


$(document).on("change", ".common", function () {
    const currentSelectedValue = this.value;
    const parentElement = this;
    $(".common").each(function () {
        if (this == parentElement) {
            return;
        }
        $(this)
            .find("option")
            .each(function () {
                if (this.value === currentSelectedValue) {
                    $(this).prop("disabled", true);
                }
            });
    });
});

$(document).on('click', '.returnBtn', function () {
        let assetId = $(this).data('id');
        let assetName = $(this).data('name');
        let qty = $(this).data('qty');

        $('#modal_asset_id').val(assetId);
        $('#modal_asset_name').text(assetName);
        $('#modal_qty').text(qty);
        $('#return_qty').attr('max', qty);
    });

$(".edit").click(function () {
    var e_id = $(this).data("id");
    var e_qty = $(this).data("qty");
    var e_a = $(this).data("name");
    $("#e_Qty").val(e_qty);
    $("#e_Id").val(e_id);
    $("#e_Asset").val(e_a);
});

$(".return").click(function () {
    var e_id = $(this).data("id");
    var e_qty = $(this).data("Status");
    // var e_a = $(this).data('name');
    $(".emploid").val(e_qty);
    $(".returnid").val(e_id);
    // $("#e_Asset").val(e_a);
});

function confirmDelete() {
    return confirm("Are you sure you want to delete?");
}

// $('.assetdetail').on('click', function(){
$(document).on("click", ".assetdetail", function () {
    var empid3 = $(this).data("emp");
    $.ajax({
        type: "GET",
        url: "/employeeassetreport/" + empid3,
        success: function (data3) {
            $("#assetdetail1").empty();

            $.each(data3, function (index, asset) {
                var assetName = asset.asnam;

                // Add serial number in parentheses if it exists
                if (asset.serial_number) {
                    assetName += " (" + asset.serial_number + ")";
                }

                $("#assetdetail1").append(
                    "<tr><td><h6 style='font-size:16px;'>" + assetName +
                    "</h6></td><td><h6 style='font-size:16px;'>" + asset.a_qty +
                    "</h6></td><td><h6 style='font-size:16px;'>" + asset.Status +
                    "</h6></td></tr>"
                );
            });

        },
    });
});

$(document).ready(function () {
    $("#atten_de").on("change", function () {
        var designation = $(this).val();
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        if (designation) {
            $.ajax({
                type: "POST",
                url: "/getattenEmployees",
                data: {
                    _token: csrfToken,
                    designation: designation,
                },
                success: function (data) {
                    $("#attemp_id").html(data);
                },
            });
        } else {
            $("#attemp_id").html(
                '<option value="">-- Choose Employee --</option>'
            );
        }
    });
});

$(document).ready(function () {
    // Function to fetch and display table data
    function fetchTableData() {
        var date = $("#attenden_date").val();
        var designation = $("#atten_de").val();
        var emp_id = $("#attemp_id").val();
        var csrfToken = $('meta[name="csrf-token"]').attr("content");

        // Make AJAX request to fetch data
        $.ajax({
            type: "POST",
            url: "/fetchTableData",
            data: {
                _token: csrfToken,
                date: date,
                designation: designation,
                emp_id: emp_id,
            },
            success: function (data) {
                if (typeof data === 'string') {
                    try {
                        data = JSON.parse(data);
                    } catch(e) {
                        return;
                    }
                }
                if (!Array.isArray(data)) return;

                var tableRows = "";

                $.each(data, function (index, attendance) {
                    tableRows += "<tr>";
                    tableRows += "<td>" + attendance.a_date + "</td>";

                    if (
                        ["Y", "A", "W", "H", "P"].includes(
                            attendance.attendance_status
                        )
                    ) {
                        tableRows +=
                            '<td><div class="form-check" style="text-align:center;margin-bottom:0px;">';

                        tableRows +=
                            '<input style="display:none;" type="radio" id="radiop' +
                            attendance.id +
                            '" class="form-check-input attendance P" name="attendance' +
                            attendance.emp_id +
                            '" value="P"';
                        if (attendance.attendance_status === "P") {
                            tableRows += " checked";
                        }
                        tableRows += ">";
                        tableRows +=
                            '<label class="form-check-label" for="radiop' +
                            attendance.id +
                            '">';
                        tableRows +=
                            '<img src="images/man.png" class="img-responsive imgg" style="height: 70px; width: 70px; margin: 0px auto; display: block;cursor: pointer;';

                        if (attendance.attendance_status === "") {
                            tableRows += '">';
                        } else {
                            tableRows += ' filter: grayscale(100%);">';
                        }
                        tableRows += "</label>";
                        tableRows += "</div></td>";
                    } else {
                        tableRows +=
                            "<td>" + attendance.attendance_status + "</td>";
                    }

                    if (
                        ["Y", "A", "W", "H", "P"].includes(
                            attendance.attendance_status
                        )
                    ) {
                        tableRows +=
                            '<td><div class="form-check" style="text-align:center;margin-bottom:0px;">';

                        tableRows +=
                            '<input style="display:none;" type="radio" id="radioa' +
                            attendance.id +
                            '" class="form-check-input attendance A" name="attendance' +
                            attendance.emp_id +
                            '" value="A"';
                        if (attendance.attendance_status === "A") {
                            tableRows += " checked";
                        }
                        tableRows += ">";
                        tableRows +=
                            '<label class="form-check-label" for="radioa' +
                            attendance.id +
                            '">';
                        tableRows +=
                            '<img src="images/man.png" class="img-responsive imgg" style="height: 70px; width: 70px; margin: 0px auto; display: block;cursor: pointer;';

                        if (attendance.attendance_status === "") {
                            tableRows += '">';
                        } else {
                            tableRows += ' filter: grayscale(100%);">';
                        }
                        tableRows += "</label>";
                        tableRows += "</div></td>";
                    } else {
                        tableRows +=
                            "<td>" + attendance.attendance_status + "</td>";
                    }
                    if (
                        ["Y", "A", "W", "H", "P"].includes(
                            attendance.attendance_status
                        )
                    ) {
                        tableRows +=
                            '<td><div class="form-check" style="text-align:center;margin-bottom:0px;">';

                        tableRows +=
                            '<input style="display:none;" type="radio" id="radiow' +
                            attendance.id +
                            '" class="form-check-input attendance W" name="attendance' +
                            attendance.emp_id +
                            '" value="W"';
                        if (attendance.attendance_status === "W") {
                            tableRows += " checked";
                        }
                        tableRows += ">";
                        tableRows +=
                            '<label class="form-check-label" for="radiow' +
                            attendance.id +
                            '">';
                        tableRows +=
                            '<img src="images/man.png" class="img-responsive imgg" style="height: 70px; width: 70px; margin: 0px auto; display: block;cursor: pointer;';

                        if (attendance.attendance_status === "") {
                            tableRows += '">';
                        } else {
                            tableRows += ' filter: grayscale(100%);">';
                        }
                        tableRows += "</label>";
                        tableRows += "</div></td>";
                    } else {
                        tableRows +=
                            "<td>" + attendance.attendance_status + "</td>";
                    }
                    if (
                        ["Y", "A", "W", "H", "P"].includes(
                            attendance.attendance_status
                        )
                    ) {
                        tableRows +=
                            '<td><div class="form-check" style="text-align:center;margin-bottom:0px;">';

                        tableRows +=
                            '<input style="display:none;" type="radio" id="radioh' +
                            attendance.id +
                            '" class="form-check-input attendance H" name="attendance' +
                            attendance.emp_id +
                            '" value="H"';
                        if (attendance.attendance_status === "H") {
                            tableRows += " checked";
                        }
                        tableRows += ">";
                        tableRows +=
                            '<label class="form-check-label" for="radioh' +
                            attendance.id +
                            '">';
                        tableRows +=
                            '<img src="images/man.png" class="img-responsive imgg" style="height: 70px; width: 70px; margin: 0px auto; display: block;cursor: pointer;';

                        if (attendance.attendance_status === "") {
                            tableRows += '">';
                        } else {
                            tableRows += ' filter: grayscale(100%);">';
                        }
                        tableRows += "</label>";
                        tableRows += "</div></td>";
                    } else {
                        tableRows +=
                            "<td>" + attendance.attendance_status + "</td>";
                    }

                    var bgColor = "";
                    var statusText = "";
                    switch (attendance.attendance_status) {
                        case "Y":
                            bgColor = "#ff7600";
                            statusText = "Pending";
                            break;
                        case "P":
                            bgColor = "#4aa23c";
                            statusText = "Present";
                            break;
                        case "A":
                            bgColor = "#ed3833";
                            statusText = "Absent";
                            break;
                        case "W":
                            bgColor = "#f8b32d";
                            statusText = "Week off";
                            break;
                        case "H":
                            bgColor = "#e7368d";
                            statusText = "Holiday";
                            break;
                        default:
                            bgColor = "";
                            statusText = attendance.attendance_status;
                    }
                    tableRows +=
                        '<td style="text-align:center;"><span style="background-color: ' +
                        bgColor +
                        '; color: white; padding: 2px 5px;border-radius:10px;">' +
                        statusText +
                        "</span></td>";

                    tableRows +=
                        '<td><div class="form-group"><input type="text" class="form-control" name="remark' +
                        attendance.id +
                        '" value="' +
                        attendance.remark +
                        '"></div></td>';
                    tableRows += "</tr>";

                    $("#hiid_id").val(attendance.id);
                    let loginTime = attendance.login_time.replace(".", ":"); // Convert 10.30 AM to 10:30 AM
                    $("#log_in").val(loginTime);
                    let logoutTime = attendance.logout_time.replace(".", ":"); // Convert 10.30 AM to 10:30 AM
                    $("#log_out").val(logoutTime);
                    let dutime = attendance.du_time.replace(".", ":"); // Convert 10.30 AM to 10:30 AM
                    $("#du_time").val(dutime);

                    // $('#du_time').text(attendance.du_time);
                    // $('#log_out').text(attendance.logout_time);
                    $("#approval_status").val(attendance.approval_status);
                    if (attendance.permission == 1) {
                        $("#Permission").val("OD");
                    } else if (attendance.permission == 2) {
                        $("#Permission").val("Permission");
                    } else if (attendance.permission == null) {
                        $("#Permission").val("");
                    }
                    if (attendance.permistiming == 1) {
                        $("#Timing").val("Hours");
                    } else if (attendance.permistiming == 2) {
                        $("#Timing").val("Days");
                    } else if (attendance.permistiming == null) {
                        $("#Timing").val("");
                    } else if (attendance.permistiming == 3) {
                        $("#Timing").val("Hours");
                    }
                    // $('#Permission').val(attendance.permission);
                    // $('#Timing').val(attendance.permistiming);
                    $("#per_time").val(attendance.total_per_time);
                    $("#od_time").val(attendance.total_od_time);
                    $("#od_days").val(attendance.total_od_days);
                });

                $(".attendancetable").html(tableRows);
            },
        });
    }

    // Fetch table data on page load
    fetchTableData();

    // Event listener for date change
    $("#attenden_date, #atten_de, #attemp_id").on("change", function () {
        fetchTableData();
    });
});

$("form").submit(function () {
    // Find the checked radio button
    var checkedValue = $('input[name^="attendance"]:checked').val();
    // Update the hidden input field with the checked value
    $("#attendance_status").val(checkedValue);
});

$(".insudetail").click(function () {
    var ins = $(this).data("ins");
    var expiry = $(this).data("exp");
    var tabid = $(this).data("tabid");

    // Set values to the modal fields
    $("#renid").val(tabid);
    // Assuming you have inputs for insurance status and expiry date
    $("#insuranceStatus").val(ins);
    $("#expirydate").val(expiry);
});

$(".lateedit").click(function () {
    var date = $(this).data("date");
    var emploid = $(this).data("emploid");
    var id = $(this).data("id");
    var logintime = $(this).data("logintime");
    var approvalstatus = $(this).data("approvalstatus");
    $("#e_Qty").val(date);
    $(".emplo_iid").val(emploid);
    $(".lateid").val(id);
    $("#e_Asset").val(logintime);
    $("#e_Asset").val(approvalstatus);
});
$(".regisappro").click(function () {
    // var date = $(this).data('date');
    var emploid = $(this).data("emploid");
    var id = $(this).data("id");
    // var logintime = $(this).data('logintime');
    var approvalstatus = $(this).data("approvalstatus");
    // $("#e_Qty").val(date);
    $(".emplo_rid").val(emploid);
    $(".reregid").val(id);
    // $("#e_Asset").val(logintime);
    $("#e_Asset").val(approvalstatus);
});

$(document).on("click", ".penattedit", function () {
    var e_id = $(this).data("id");
    var editattenemid = $(this).data("editattenemid");
    var attendancestatus = $(this).data("attendancestatus");
    var atdate = $(this).data("atdate");
    var emnmae = $(this).data("emnmae");
    var remark = $(this).data("atremark");
    var login = $(this).data("login");
    var logout = $(this).data("logout");
    var perdateto = $(this).data("perdateto");
    var perdatefrom = $(this).data("perdatefrom");
    var pertimeend = $(this).data("pertimeend");
    var pertimestart = $(this).data("pertimestart");
    var permistiming = $(this).data("permistiming");
    var permission = $(this).data("permission");
    var odtimestart = $(this).data("odtimestart");
    var odtimeend = $(this).data("odtimeend");
    var year = $(this).data("year");
    var month = $(this).data("month");
    var day = $(this).data("day");
    var punchin_time = $(this).data("punchin_time");
    var punchout_time = $(this).data("punchout_time");
    var duty_time = $(this).data("duty_time");
    var overtime = $(this).data("overtime");
    var total_duty_time = $(this).data("total_duty_time");
    var total_overtime = $(this).data("total_overtime");
    var first_punchin_time = $(this).data("first_punchin_time");
    var last_punchout_time = $(this).data("last_punchout_time");

    //   alert(permission);

    $("#pertodate").val(perdateto);
    $("#perfromdate").val(perdatefrom);
    $("#perendtime").val(pertimeend);
    $("#perstarttime").val(pertimestart);
    $("#details").val(permistiming);

    $("#permission").val(permission);
    $("#odendtime").val(odtimeend);
    $("#odstarttime").val(odtimestart);

    $("#daya").val(day);
    $("#montha").val(month);
    $("#yeara").val(year);

    $("#logintime").val(login);
    $("#logouttime").val(logout);
    $("#dataaid").val(e_id);
    $("#atempid").val(editattenemid);
    $("#dataa").val(atdate);
    $("#atempname").val(emnmae);
    $("#remat").val(remark);

    $("#punchintime").val(punchin_time);
    $("#punchouttime").val(punchout_time);
    $("#dutytime").val(duty_time);
    $("#overtime").val(overtime);
    $("#total_dutytime").val(total_duty_time);
    $("#total_overtime").val(total_overtime);
    $("#first_punchintime").val(first_punchin_time);
    $("#last_punchouttime").val(last_punchout_time);

    var text = "";
    var bgColor = "";
    var grayscale = "";
    switch (attendancestatus) {
        case "P":
            text = "Present";
            bgColor = "#4aa23c";
            grayscale = "grayscale(0)"; // Example background color for Present
            $("#flexRadioDefault1").prop("checked", true);
            break;
        case "Y":
            text = "Pending";
            bgColor = "#ff7600"; // Example background color for Pending
            break;
        case "W":
            text = "Week off";
            bgColor = "#f8b32d";
            grayscale = "grayscale(0)"; // Example background color for Week off
            $("#flexRadioDefault3").prop("checked", true);
            break;
        case "H":
            text = "Holiday";
            bgColor = "#e7368d";
            grayscale = "grayscale(0)"; // Example background color for Holiday
            $("#flexRadioDefault4").prop("checked", true);
            break;
        case "A":
            text = "Absent";
            bgColor = "#ed3833";
            grayscale = "grayscale(0)"; // Example background color for Absent
            $("#flexRadioDefault2").prop("checked", true);
            break;
        default:
            text = "Unknown";
            bgColor = "gray"; // Example background color for Unknown
            break;
    }

    // Set text content and background color
    $("#attstatus").text(text).css("background-color", bgColor);

    if (permission) {
        $.ajax({
            type: "GET",
            url: "Getstate/" + permission,
            success: function (html) {
                console.log(html);
                $("#details").html('<option value="">Select Timing</option>'); // Clear previous options
                html.forEach((htmlee) => {
                    if (permistiming == htmlee["id"]) {
                        $("#details").append(
                            `<option value="${htmlee["id"]}" selected>${htmlee["name"]}</option>`
                        );
                    } else {
                        $("#details").append(
                            `<option value="${htmlee["id"]}">${htmlee["name"]}</option>`
                        );
                    }
                });

                // Show/hide elements based on selected option in #details dropdown
                var selectedOptionId = $("#details").val();
                switch (selectedOptionId) {
                    case "1": // Hours for OD
                        $("#fromdate-group, #todate-group").hide();
                        $("#starttime-od, #endtime-od").show();
                        $("#starttime-group, #endtime-group").hide();
                        break;
                    case "2": // Days for OD
                        $("#fromdate-group, #todate-group").show();
                        $("#starttime-od, #endtime-od").hide();
                        $("#starttime-group, #endtime-group").hide();
                        break;
                    case "3": // Hours for Permission
                        $("#fromdate-group, #todate-group").hide();
                        $("#starttime-od, #endtime-od").hide();
                        $("#starttime-group, #endtime-group").show();
                        break;
                    default:
                        // Handle default case or unknown values
                        break;
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching states:", error);
            },
        });
    } else {
        // If no permission data, clear #details dropdown
        $("#details").html('<option value="">Select Timing</option>');
    }
});

$(document).ready(function () {
    $("#sitid").on("change", function () {
        var designation = $(this).val();
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        if (designation) {
            $.ajax({
                type: "POST",
                url: "/getattenreports",
                data: {
                    _token: csrfToken,
                    designation: designation,
                },
                success: function (data) {
                    $("#salempid").html(data);
                },
            });
        } else {
            $("#salempid").html(
                '<option value="">-- Choose Employee --</option>'
            );
        }
    });
});

$(document).ready(function () {
    $("#paydep").on("change", function () {
        var designation = $(this).val();
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        if (designation) {
            $.ajax({
                type: "POST",
                url: "/getpayslipreports",
                data: {
                    _token: csrfToken,
                    designation: designation,
                },
                success: function (data) {
                    $("#paysempid").html(data);
                },
            });
        } else {
            $("#paysempid").html(
                '<option value="">-- Select Employee --</option>'
            );
        }
    });
});

$(document).ready(function () {
    $("#fandfdep").on("change", function () {
        var designation = $(this).val();
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        if (designation) {
            $.ajax({
                type: "POST",
                url: "/getfandfreports",
                data: {
                    _token: csrfToken,
                    designation: designation,
                },
                success: function (data) {
                    $("#fandfempid").html(data);
                },
            });
        } else {
            $("#fandfempid").html(
                '<option value="">-- Choose Employee --</option>'
            );
        }
    });
});

$(".idedva").click(function () {
    var e_gp = $(this).data("emerg");
    var e_va = $(this).data("vali");
    var empid = $(this).data("emp");
    var eid = $(this).data("emid");

    $(".valditi").val(e_va);
    $(".emerge").val(e_gp);
    $(".editval").val(eid);
});

$(".editholiday").click(function () {
    var hid = $(this).data("id");
    var hdate = $(this).data("hdate");
    var hday = $(this).data("hday");
    var hname = $(this).data("hname");

    $("#edithoid").val(hid);
    $("#dateholied").val(hdate);
    $("#dayholied").val(hday);
    $("#holinameed").val(hname);
});

$(".attendencedetail").on("click", function () {
    var empid4 = $(this).attr("data-empaa");

    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    $("#atted-empid").val(empid4);
    $.ajax({
        type: "POST",
        url: "employeereport_attend",
        data: {
            _token: csrfToken,
            empid4: empid4,
            // month: selectedMonth, // pass selected month here
            // year: selectedYear,
        },
        success: function (response) {
            // Log the response for debugging
            // console.log(response);

            // Generate HTML for table rows
            var html = "";
            html += "<tr>";

            html += "<td>" + response.month + " - " + response.year + "</td>";
            html += "<td>" + response.employee.emp_id + "</td>";
            html += "<td>" + response.employee.name + "</td>";
            html += "<td>" + response.countTotal + "</td>";
            html += "<td>" + response.countPresent + "</td>";
            html += "<td>" + response.countAbsent + "</td>";
            html += "<td>" + response.countWeekOff + "</td>";
            html += "<td>" + response.countHoliday + "</td>";
            html += "</tr>";

            // Append HTML to table body
            $("#attendancedetail1").html(html);

            // Reset dropdowns (if needed)
            $("#year-dropdown1").prop("selectedIndex", 0);
            $("#month-dropdown").prop("selectedIndex", 0);
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            // Handle errors here
        },
    });
});

$(".view-attdetail").on("click", function () {
    var empid5 = $("#atted-empid").val();
    var curr_year = $("#year-dropdown1").val();
    var curr_month = $("#month-dropdown").val();
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    if (curr_year != "" && curr_month != "") {
        $.ajax({
            type: "POST",
            url: "employeereport_attendfetch",
            data: {
                _token: csrfToken,
                empid5: empid5,
                curr_year: curr_year,
                curr_month: curr_month,
            },
            success: function (data) {
                var html = "";
                html += "<tr>";
                html += "<td>" + data.month + " - " + data.year + "</td>";
                html += "<td>" + data.employee.emp_id + "</td>";
                html += "<td>" + data.employee.name + "</td>";
                html += "<td>" + data.countTotal + "</td>";
                html += "<td>" + data.countPresent + "</td>";
                html += "<td>" + data.countAbsent + "</td>";
                html += "<td>" + data.countWeekOff + "</td>";
                html += "<td>" + data.countHoliday + "</td>";
                html += "</tr>";

                $("#attendancedetail1").html(html);
            },
        });
    } else {
        $.confirm({
            title: "Warning!",
            content: "Please Choose Year and Month",
            type: "red",
            typeAnimated: true,
            buttons: {
                tryAgain: {
                    text: "OK",
                    btnClass: "btn-red",
                    action: function () {},
                },
            },
        });
    }
});

$(document).ready(function () {
    var html =
        '<tr><td><input class="form-control dateholi1"  placeholder="" type="date" name="dateholi[]" id="dateholi1" required></td><td><input placeholder="" class="form-control" type="text" name="dayholi[]" required></td><td><input placeholder="Holiday" class="form-control" type="text" name="holiname[]" required></td><td><button class="btn btn-danger remove">X</button></td></tr>';
    $("#addholiday").click(function () {
        // alert("Hellow");
        $("#holi1").append(html);
    });

    $(document).on("click", ".remove", function () {
        $(this).parents("tr").remove();
    });
});

$(document).ready(function () {
    $(".viewempsalary").click(function () {
        var empId = $("#fandfempid").val();
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url: "get_fandf_details", // URL to your server-side endpoint
            method: "POST",
            data: {
                _token: csrfToken,
                empId: empId,
            },
            success: function (response) {
                // Update employee details section
                var employeeDetails = response.employeeDetails;
                if (employeeDetails) {
                    var employeeText =
                        employeeDetails.name +
                        " (" +
                        employeeDetails.emp_id +
                        ")";
                    $("#employee-name").text(employeeText);
                } else {
                    // Handle case where employee details are not found
                }
                // $('#employee-name').text(response.employeeDetails.name);
                // $('#employee-designation').text(response.employeeDetails.desigination);
                $(".dessi").text(response.employeeDetails.designation);
                $(".dofj").text(response.employeeDetails.doj);
                $("#penadva").show();
                $("#penasse").show();

                $(".headd").show();

                // Update other advances section
                var advancesTable = $("#edit_datable_3");
                advancesTable.find("tr:gt(0)").remove();
                response.otherAdvances.forEach(function (advance) {
                    var row = $("<tr>");
                    row.append("<td>" + advance.advance_type + "</td>");
                    row.append("<td>" + advance.amount + "</td>");
                    advancesTable.append(row);
                });
                var assettable = $("#pend_aset");
                assettable.find("tr:gt(0)").remove();
                response.assets.forEach(function (asset) {
                    var row = $("<tr>");
                    row.append("<td>" + asset.aname + "</td>");
                    row.append("<td>" + asset.a_qty + "</td>");
                    row.append("<td>" + asset.Status + "</td>");

                    assettable.append(row);
                });
                $("#pend_aset").append(
                    '<tr><td colspan="3"><input onclick="conform();" type="submit" class="btn btn-success" style="width:300px;background: #192a9f; border: none;" value="View F and F"></td></tr>'
                );
                // $('#pend_aset').append('<tr><td colspan="3"><input type="submit" class="btn btn-success" style="width:300px;background: #192a9f; border: none;" value="View F and F" disabled="true"></td></tr>');
            },
        });
    });
});

function conform() {
    $.confirm({
        title: "Confirm!",
        content: "Are you sure process on F and F?",
        buttons: {
            confirm: function () {
                $("#fandfform").submit();
            },
            cancel: function () {
                $.alert("Canceled!");
            },
        },
    });
}

// $(document).on('click', '.monyrwise_btn', function () {

//     // $('.monyrwise_btn').click(function() {
//     var year = $('#atyear').val(); // Assuming you have inputs for year and month
//     var month = $('#atmonth').val();
//     var day = $('#atday').val();
//     // alert(month);
//     $.ajax({
//         url: '/filter',
//         type: 'GET',
//         data: {
//             atyear: year,
//             atmonth: month,
//             atday: day
//         },
//         success: function (response) {
//             if (Array.isArray(response)) {
//                 // Clear existing data
//                 $('#initialDataaa').empty();

//                 if (day) {
//                     $('#lohdkj, #kojdj').show();
//                 } else {
//                     $('#lohdkj, #kojdj').hide();
//                 }

//                 // Iterate over each set of data in the response
//                 response.forEach(function (attendanceData) {
//                     // Log each set of data
//                     console.log(attendanceData);

//                     // Construct HTML for each set of data
//                     var html = '<tr>';
//                     if (day) {
//                         html += '<td>' + attendanceData.a_date + '</td>';
//                     } else {
//                         html += '<td>' + attendanceData.a_month + '-' + attendanceData.a_year + '</td>';
//                     }
//                     // html += '<td>' + attendanceData.a_month + '-' + attendanceData.a_year + '</td>';
//                     html += '<td>' + attendanceData.emp_id + '</td>';
//                     html += '<td>' + attendanceData.name + '</td>';
//                     html += '<td>' + attendanceData.total_days + '</td>';
//                     html += '<td>' + attendanceData.present_days_count + '</td>';
//                     html += '<td>' + attendanceData.A_count + '</td>';
//                     html += '<td>' + attendanceData.W_count + '</td>';
//                     html += '<td>' + attendanceData.H_count + '</td>';
//                     html += '<td>' + attendanceData.Y_count + '</td>';
//                      html += '<td>' + attendanceData.total_duty_time + '</td>';
//                      if (day) {
//                         html += '<td>' + (attendanceData.punchin_time || '-') + '</td>';  // Punch-in Time
//                         html += '<td>' + (attendanceData.punchout_time || '-') + '</td>'; // Punch-out Time
//                     }
//                     // html += '<td>' + attendanceData.total_per_time + '</td>';
//                     // html += '<td>' + attendanceData.total_od_time + '</td>';
//                     // html += '<td>' + attendanceData.total_od_days_sum_formatted + '</td>';
//                     html += '</tr>';

//                     // Append the HTML to the table
//                     $('#initialDataaa').append(html);
//                 });
//             } else {
//                 console.error('Invalid response format');
//             }
//         },
//         error: function (xhr, status, error) {
//             // Handle error
//             console.error(error);
//         }
//     });
// });

$(document).on("click", ".otmonyrwisebtn", function () {
    // $('.monyrwise_btn').click(function() {
    var year = $("#atyear").val(); // Assuming you have inputs for year and month
    var month = $("#atmonth").val();
    // alert(month);
    $.ajax({
        url: "/overtimefilter",
        type: "GET",
        data: {
            atyear: year,
            atmonth: month,
        },
        success: function (response) {
            if (Array.isArray(response)) {
                // Clear existing data
                $("#otinitialDataaa").empty();

                // Iterate over each set of data in the response
                response.forEach(function (attendanceData) {
                    // Log each set of data
                    console.log(attendanceData);

                    // Construct HTML for each set of data
                    var html = "<tr>";
                    html +=
                        "<td>" +
                        attendanceData.a_month +
                        "-" +
                        attendanceData.a_year +
                        "</td>";
                    html += "<td>" + attendanceData.emp_id + "</td>";
                    html += "<td>" + attendanceData.name + "</td>";
                    html += "<td>" + attendanceData.total_days + "</td>";
                    html += "<td>" + attendanceData.P_count + "</td>";
                    html += "<td>" + attendanceData.A_count + "</td>";
                    html += "<td>" + attendanceData.W_count + "</td>";
                    html += "<td>" + attendanceData.H_count + "</td>";
                    html += "<td>" + attendanceData.Y_count + "</td>";
                    html += "<td>" + attendanceData.total_ot_time + "</td>";
                    html += "<td>" + attendanceData.total_per_time + "</td>";
                    html += "<td>" + attendanceData.total_od_time + "</td>";
                    html +=
                        "<td>" +
                        attendanceData.total_od_days_sum_formatted +
                        "</td>";
                    html += "</tr>";

                    // Append the HTML to the table
                    $("#otinitialDataaa").append(html);
                });
            } else {
                console.error("Invalid response format");
            }
        },
        error: function (xhr, status, error) {
            // Handle error
            console.error(error);
        },
    });
});

$(document).on("click", ".otwisebtn", function () {
    // $('.monyrwise_btn').click(function() {
    var date = $("#atdate").val(); // Assuming you have inputs for year and month
    // var month = $('#atmonth').val();
    // alert(month);
    $.ajax({
        url: "/singleotfilter",
        type: "GET",
        data: {
            atdate: date,
            // atmonth: month
        },
        success: function (response) {
            if (Array.isArray(response)) {
                // Clear existing data
                $("#sotinitialDataaa").empty();

                // Iterate over each set of data in the response
                response.forEach(function (attendanceData) {
                    // Log each set of data
                    console.log(attendanceData);

                    // Construct HTML for each set of data
                    var html = "<tr>";
                    html += "<td>" + attendanceData.a_date + "</td>";
                    html += "<td>" + attendanceData.emp_id + "</td>";
                    html += "<td>" + attendanceData.name + "</td>";
                    html += "<td>" + attendanceData.attendance_status + "</td>";
                    html +=
                        "<td>" + attendanceData.over_time_formatted + "</td>";
                    html +=
                        "<td>" +
                        attendanceData.total_od_time_formatted +
                        "</td>";
                    html +=
                        "<td>" +
                        attendanceData.total_per_time_formatted +
                        "</td>";
                    html +=
                        "<td>" +
                        attendanceData.total_od_days_sum_formatted +
                        "</td>";
                    html += "</tr>";

                    // Append the HTML to the table
                    $("#sotinitialDataaa").append(html);
                });
            } else {
                console.error("Invalid response format");
            }
        },
        error: function (xhr, status, error) {
            // Handle error
            console.error(error);
        },
    });
});

$(".salarygensitewise").on("click", function () {
    var month = $(".cmonth").val();
    var year = $(".cyear").val();
    var sitid = $("#salempid").val();
    var workday = $(".totworkingthismonth").val();
    // sitid: sitid,
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    // alert(sitid);
    if (sitid != "") {
        $.ajax({
            type: "POST",
            url: "/ajaxsalarygensitewise",
            data: {
                month: month,
                year: year,
                siteid: sitid,
                workday: workday,
                _token: csrfToken,
            },
            dataType: "json",
        })
            .done(function (response) {
                if (response.status === "success") {
                    alert("Salary generated successfully!");
                } else if (response.status === "error") {
                    alert(
                        "Salary already generated for this employee and month."
                    );
                }
            })
            .fail(function () {
                alert("Failed..!");
            });

        // contentLoaded: function(data, status, xhr){
        //     self.setContentAppend('<div>Content loaded!</div>');
        // },
        // onContentReady: function(){
        //     this.setContentAppend('<div>Content ready!</div>');
        // }
    } else {
        alert("Please fill all the Fields");
    }
});

$(".viesalary").on("click", function () {
    var month = $(".cmonth").val();
    var year = $(".cyear").val();
    var empid = $("#salempid").val();
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    if (empid != "") {
        // var self = this;
        $.ajax({
            type: "POST",
            url: "/ajaxsalary",
            data: { month: month, year: year, empid: empid, _token: csrfToken },
        }).done(function (response) {
            if (response.code == 1) {
                alert("First Generate Salary");
            } else {
                $(".employeesalarygen").html(response);
                $("#updatesal").show();
            }
        });
    } else {
        $.confirm({
            title: "Warning!",
            content: "Please Choose Employee",
            type: "red",
            typeAnimated: true,
            buttons: {
                tryAgain: {
                    text: "OK",
                    btnClass: "btn-red",
                    action: function () {},
                },
            },
        });
    }
    $.ajax({
        type: "POST",
        url: "/checkExistingData",
        data: { month: month, year: year, empid: empid, _token: csrfToken },
        success: function (response) {
            console.log(response);
            $("#empdesalge").show();
            console.log(empid);
            $("#allande").show();
            var existingData = response.existingData;
            $(".table_id").val(existingData.id);
            $(".totaldays").val(existingData.Total_days);
            // $(".presentday").val(existingData.Present_days);
            $(".presentday").val(existingData.Present_days);
            $(".holiday").val(existingData.Holidays);
            $(".weekoff").val(existingData.weekoff);
            $(".halfday").val(existingData.dutyttime);
            $(".salaryday").val(existingData.Present_days);
            $(".actualbasic").val(existingData.Actual_basic);
            $(".actualAllowance").val(existingData.Misc_allow);
            $(".actualDeduction").val(existingData.OTH_MISC_DEDN);
            $(".da").val(existingData.DA);
            $(".basicsalary").val(existingData.basic_salary);
            $(".netsalary").val(existingData.Net_pay);
            $(".ddoj").text(existingData.employee_detail.doj);
            $(".withopf").text(existingData.employee_detail.all_type);

            $(".clintna").text(existingData.employee_detail.name);
            var photoUrl = existingData.employee_detail.photo;
            var designationName = response.designationName;
            $(".ddepar").text(designationName);
            // Update the src attribute of the image
            $("#employeePhoto").attr(
                "src",
                "/public/public/images/" + photoUrl
            );

            // alert(existingData.employee_detail.doj);
            if (response.salaryCalculation) {
                // Salary calculation data found
                var salaryCalculation = response.salaryCalculation;

                // Populate the fields with the data
                $(".hra").val(salaryCalculation.HRA);
                $(".wa").val(salaryCalculation.WA);
                $(".bonus").val(salaryCalculation.bonus);
                $(".misc").val(salaryCalculation.Misc_allow);
                $(".amt").val(salaryCalculation.OT_AMT);
                $(".hrs").val(salaryCalculation.OT_HRS);
                $(".grosscr").val(salaryCalculation.GROSS_CR);
                $(".epf").val(salaryCalculation.EPF);
                $(".esi").val(salaryCalculation.ESI);
                $(".late1").val(salaryCalculation.uniform);
                $(".loan").val(salaryCalculation.LOANDEDN);
                $(".advance").val(salaryCalculation.ADVANCE_DEDN);
                $(".misc1").val(salaryCalculation.OTH_MISC_DEDN);
                $(".late").val(salaryCalculation.LATE_DEDN);
                $(".grossdr").val(salaryCalculation.GROSS_DR);

                // Loop through advance types
                response.advanceTypes.forEach(function (advanceType) {
                    $("input[name='" + advanceType.id + "deduction']").val(
                        advanceType.amount
                    );
                });

                // Handle case when other data is returned
                console.log("Existing data found.");
            } else {
                // Handle case when no data is returned
                console.log("No existing data found.");
            }

            calcnetsalary();
        },
        error: function () {
            // Handle error case
            console.log("Error retrieving existing data.");
        },
    });
});

// validation

// function calcnetsalary() {
//     var presentday = $('.presentday').val();
//     var holiday = $('.holiday').val();
//     var weekoff = $('.weekoff').val();
//     var halfday = $('.halfday').val();
//     var abasic = $('.actualbasic').val();
//     var DA = $('.da').val();
//     var totaldays = $('.totaldays').val();

//     var originalSalarydays = parseFloat(presentday) + parseFloat(holiday) + parseFloat(weekoff) + parseFloat(halfday) / 2;
//     var salarydays = originalSalarydays;

//     if(totaldays < originalSalarydays){
//         alert('Should Not Exceed than Total days');
//         $(event.target).val('');
//         salarydays = originalSalarydays;
//     }else{
//     $('.salaryday').val(salarydays);

//     abasic = parseFloat(abasic) + parseFloat(DA);
//     var cal = (parseFloat(abasic) / parseFloat(totaldays)) * parseFloat(salarydays);
//     cal = cal.toFixed(2);
//     $(".basicsalary").val(cal);
//     $(".netsalary").val(cal);
//     $(".grosscr").val(cal);
// }
//     var hra = $('.hra').val();
//     var wa = $('.wa').val();
//     var bonus = $('.bonus').val();
//     var others = $('.misc').val();
//     var ot = $('.amt').val();

//     var allowances = parseFloat(hra) + parseFloat(wa) + parseFloat(bonus) + parseFloat(others) + parseFloat(ot);

//     var grosscr = parseFloat(allowances) + parseFloat(cal);
//     grosscr = grosscr.toFixed(2);
//     $(".netsalary").val(grosscr);
//     $(".grosscr").val(grosscr);

//     var epf = $('.epf').val();
//     var esi = $('.esi').val();
//     // var uniform = $('.uniform').val();
//     var loan = $('.loan').val();
//     var advance = $('.advance').val();
//     var misc1 = $('.misc1').val();
//     var late = $('.late').val();
//     // var otherdedn = $('.otherdedn').val();
//     var totalDeductions = 0;

//     $('.deduction').each(function() {
//         var deductionValue = parseFloat($(this).val());
//         if (!isNaN(deductionValue)) {
//             totalDeductions += deductionValue;
//         }
//     });
//     $(".otherdedn").val(totalDeductions);
//     // + parseFloat(uniform)

//     var deductions = parseFloat(epf) + parseFloat(esi) + parseFloat(loan) + parseFloat(advance) + parseFloat(misc1) + parseFloat(late) + parseFloat(totalDeductions) ;

//     var totgrosscr = grosscr - deductions;
//     totgrosscr = totgrosscr.toFixed(2);
//     $(".netsalary").val(totgrosscr);
//     // $(".grosscr").val(totgrosscr);
//     $(".grossdr").val(deductions);
// }

$(
    ".presentday, .holiday, .weekoff, .halfday, .hra, .wa, .bonus, .misc, .amt, .epf, .esi, .loan, .advance, .misc1, .late, .otherdedn, .deduction"
).keyup(function () {
    calcnetsalary();
});

$(document).ready(function () {
    $(".sallarydetail").on("click", function () {
        var empid4 = $(this).attr("data-empaa"); // Corrected attribute name

        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $("#atted-empid1").val(empid4);
        $.ajax({
            type: "POST",
            url: "employeereport_salary",
            data: {
                _token: csrfToken,
                empid4: empid4,
            },
            success: function (response) {
                var html = "";
                html += "<tr>";
                html +=
                    "<td>" + response.month + " - " + response.year + "</td>";
                html += "<td>" + response.employee.emp_id + "</td>";
                html += "<td>" + response.employee.name + "</td>";
                html += "<td>" + response.salary.Total_days + "</td>";
                html += "<td>" + response.salary.Present_days + "</td>";
                html += "<td>" + response.salary.weekoff + "</td>";
                html += "<td>" + response.salary.Holidays + "</td>";
                html += "<td>" + response.salary.Present_days + "</td>"; // Assuming this is for salary days
                html += "<td>" + response.salary.basic_salary + "</td>"; // Add salary details here
                html += "<td>" + response.salary.GROSS_CR + "</td>"; // Add salary details here
                html += "<td>" + response.salary.GROSS_DR + "</td>"; // Add salary details here
                html += "<td>" + response.salary.Net_pay + "</td>"; // Add salary details here
                html += "</tr>";

                $("#salarydetail1").html(html);
                $("#year-dropdown2").prop("selectedIndex", 0);
                $("#month-dropdown1").prop("selectedIndex", 0);
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            },
        });
    });
});

$(".view-saladetail").on("click", function () {
    var empid5 = $("#atted-empid1").val();
    var curr_yea = $("#year-dropdown2").val();
    var curr_mon = $("#month-dropdown1").val();
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    if (curr_yea != "" && curr_mon != "") {
        $.ajax({
            type: "POST",
            url: "employeereport_salaryfetch",
            data: {
                _token: csrfToken,
                empid5: empid5,
                curr_yea: curr_yea,
                curr_mon: curr_mon,
            },
            success: function (data) {
                var html = "";
                html += "<tr>";
                html += "<td>" + data.month + " - " + data.year + "</td>";
                html += "<td>" + data.salary.emp_no + "</td>";
                html += "<td>" + data.employee.name + "</td>";
                html += "<td>" + data.salary.Total_days + "</td>";
                html += "<td>" + data.salary.Present_days + "</td>";
                html += "<td>" + data.salary.weekoff + "</td>";
                html += "<td>" + data.salary.Holidays + "</td>";
                html += "<td>" + data.salary.Present_days + "</td>";
                html += "<td>" + data.salary.basic_salary + "</td>";
                html += "<td>" + data.salary.GROSS_CR + "</td>";
                html += "<td>" + data.salary.GROSS_DR + "</td>";
                html += "<td>" + data.salary.Net_pay + "</td>";
                html += "</tr>";

                $("#salarydetail1").html(html);
            },
        });
    } else {
        $.confirm({
            title: "Warning!",
            content: "Please Choose Year and Month",
            type: "red",
            typeAnimated: true,
            buttons: {
                tryAgain: {
                    text: "OK",
                    btnClass: "btn-red",
                    action: function () {},
                },
            },
        });
    }
});







       let currentStep = 1;
    const totalSteps = 6;

    const requiredFieldsStep1 = [
        { id: 'name', message: 'Name is required' },
        { id: 'dob', message: 'Date of Birth is required' },
        { id: 'Qualification', message: 'Qualification is required' },
        { id: 'mobile_no', message: 'Mobile number is required' },
        { id: 'email', message: 'Email is required' },
        { id: 'nationality', message: 'Nationality is required' },
        { id: 'doj', message: 'Date of Joining is required' },
        { id: 'gender', message: 'Gender is required' },
        { id: 'oridesignation', message: 'Designation is required' },
        { id: 'bloodgroup', message: 'Blood Group is required' },
    ];

    function showStep(step) {
        const steps = document.querySelectorAll('.form-step');
        steps.forEach((stepDiv, index) => {
            stepDiv.style.display = (index + 1 === step) ? 'block' : 'none';
        });

        document.getElementById('prevBtn').style.display = (step === 1) ? 'none' : 'inline-block';
        document.getElementById('nextBtn').classList.toggle('d-none', step === totalSteps);
        document.getElementById('submitBtn').classList.toggle('d-none', step !== totalSteps);
    }

    function showFieldError(input, message) {
        const span = document.createElement('span');
        span.classList.add('text-danger');
        span.innerText = message;
        input.insertAdjacentElement('afterend', span);
    }

    function validateStep1() {
        let isValid = true;

        requiredFieldsStep1.forEach(field => {
            const input = document.getElementById(field.id);
            const existingError = document.querySelector(`#${field.id} + .text-danger`);
            if (existingError) {
                existingError.remove();
            }

            if (!input.value.trim()) {
                showFieldError(input, field.message);
                isValid = false;
            }
        });

        return isValid;
    }

    function validateOptionalField(input, rules = []) {
        const existingError = input.parentNode.querySelector('.text-danger');
        if (existingError) {
            existingError.remove();
        }

        const value = input.value.trim();
        if (!value) return true; // field is empty, skip validation

        for (let rule of rules) {
            if (rule.type === 'minLength' && value.length < rule.value) {
                showFieldError(input, rule.message);
                return false;
            }
            if (rule.type === 'maxLength' && value.length > rule.value) {
                showFieldError(input, rule.message);
                return false;
            }
            if (rule.type === 'pattern' && !rule.value.test(value)) {
                showFieldError(input, rule.message);
                return false;
            }
        }

        return true;
    }

function validateMultiRowSection(rowSelector, fieldConfigs) {
    let isAnyFieldFilled = false;
    let isValid = true;

    document.querySelectorAll(rowSelector).forEach(row => {
        let rowHasInput = false;
        let rowIsValid = true;

        fieldConfigs.forEach(cfg => {
            const input = row.querySelector(cfg.selector);
            if (!input) return;

            const existingError = input.parentNode.querySelector('.text-danger');
            if (existingError) existingError.remove();

            const value = input.value.trim();
            if (value) rowHasInput = true;

            if (value) {
                for (let rule of cfg.rules) {
                    if (rule.type === 'minLength' && value.length < rule.value) {
                        showFieldError(input, rule.message);
                        rowIsValid = false;
                        break;
                    }
                    if (rule.type === 'maxLength' && value.length > rule.value) {
                        showFieldError(input, rule.message);
                        rowIsValid = false;
                        break;
                    }
                    if (rule.type === 'pattern' && !rule.value.test(value)) {
                        showFieldError(input, rule.message);
                        rowIsValid = false;
                        break;
                    }
                }
            }
        });

        if (rowHasInput) isAnyFieldFilled = true;
        if (!rowIsValid) isValid = false;
    });

    return !isAnyFieldFilled || isValid;
}


    // Initial Step
    showStep(currentStep);

    document.getElementById('prevBtn').addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });

    document.getElementById('nextBtn').addEventListener('click', function (e) {
        e.preventDefault();

        if (currentStep === 1) {
            const isStep1Valid = validateStep1();
            if (isStep1Valid) {
                currentStep++;
                showStep(currentStep);
            }
        } else if (currentStep === 2) {
            const bankFields = [
                {
                    el: document.getElementById('bankname'),
                    rules: [
                        { type: 'minLength', value: 3, message: 'Bank Name must be at least 3 characters' },
                        { type: 'maxLength', value: 30, message: 'Bank Name must be less than 30 characters' }
                    ]
                },
                {
                    el: document.getElementById('accholdername'),
                    rules: [
                        { type: 'minLength', value: 3, message: 'Holder Name must be at least 3 characters' },
                        { type: 'maxLength', value: 30, message: 'Holder Name must be less than 30 characters' }
                    ]
                },
                {
                    el: document.getElementById('acc_number'),
                    rules: [
                        { type: 'pattern', value: /^\d{6,20}$/, message: 'Account number must be 6–20 digits' }
                    ]
                },
                {
                    el: document.getElementById('accno'),
                    rules: [
                        {
                            type: 'pattern',
                            value: /^[A-Z]{3,5}0[A-Z0-9]{3,6}$/i, // more flexible IFSC pattern
                            message: 'Invalid IFSC code format'
                        }
                    ]
                }
            ];

            const anyFilled = bankFields.some(f => f.el.value.trim() !== '');

            if (!anyFilled || bankFields.every(f => validateOptionalField(f.el, f.rules))) {
                currentStep++;
                showStep(currentStep);
            }
        } else if (currentStep === 3) {
            const isValid = validateMultiRowSection('#app1 tr', [
                { selector: '.f_name', rules: [{ type: 'minLength', value: 2, message: 'Name too short' }] },
                { selector: '.f_relation', rules: [{ type: 'minLength', value: 2, message: 'Relation too short' }] },
                { selector: '.f_mobile', rules: [{ type: 'pattern', value: /^\d{10}$/, message: 'Invalid Mobile' }] },
                { selector: '.f_email', rules: [{ type: 'pattern', value: /^[^\s@]+@[^\s@]+\.[^\s@]+$/, message: 'Invalid Email' }] }
            ]);
            if (isValid) {
                currentStep++;
                showStep(currentStep);
            }
        } else if (currentStep === 4) {
            const isValid = validateMultiRowSection('#app2 tr', [
                { selector: 'select[name="level[]"]', rules: [{ type: 'minLength', value: 1, message: 'Level is required' }] },
                { selector: '.degree', rules: [{ type: 'minLength', value: 2, message: 'Degree too short' }] },
                { selector: '.univers', rules: [{ type: 'minLength', value: 2, message: 'University too short' }] },
                { selector: '.passout', rules: [{ type: 'pattern', value: /^\d{4}$/, message: 'Invalid Year' }] }
            ]);
            if (isValid) {
                currentStep++;
                showStep(currentStep);
            }
        } else if (currentStep === 5) {
            const isValid = validateMultiRowSection('#app3 tr', [
                { selector: '.exp_start_date', rules: [{ type: 'minLength', value: 1, message: 'Start Date is required' }] },
                { selector: '.exp_end_date', rules: [{ type: 'minLength', value: 1, message: 'End Date is required' }] },
                { selector: '.perioed', rules: [{ type: 'minLength', value: 1, message: 'Period is required' }] },
                { selector: '.rrole', rules: [{ type: 'minLength', value: 2, message: 'Role too short' }] },
                { selector: '.companyy', rules: [{ type: 'minLength', value: 2, message: 'Company too short' }] },
                { selector: '.dedis', rules: [{ type: 'minLength', value: 2, message: 'Designation too short' }] }
            ]);
            if (isValid) {
                currentStep++;
                showStep(currentStep);
            }
        } else if (currentStep === 6) {
        // Final submit
        document.getElementById('addemplyoe').submit();
    }
    });



    let editcurrentStep = 1;
    const edittotalSteps = 6;

    const editrequiredFieldsStep1 = [
        { id: 'name', message: 'Name is required' },
        { id: 'dob', message: 'Date of Birth is required' },
        { id: 'Qualification', message: 'Qualification is required' },
        { id: 'mobile_no', message: 'Mobile number is required' },
        { id: 'email', message: 'Email is required' },
        { id: 'nationality', message: 'Nationality is required' },
        { id: 'doj', message: 'Date of Joining is required' },
        { id: 'gender', message: 'Gender is required' },
        { id: 'oridesignation', message: 'Designation is required' },
        { id: 'bloodgroup', message: 'Blood Group is required' },
    ];

    function showeditStep(step) {
        const steps = document.querySelectorAll('.editform-step');
        steps.forEach((stepDiv, index) => {
            stepDiv.style.display = (index + 1 === step) ? 'block' : 'none';
        });

        document.getElementById('prevBtn').style.display = (step === 1) ? 'none' : 'inline-block';
        document.getElementById('nextBtn').classList.toggle('d-none', step === edittotalSteps);
        document.getElementById('submitBtn').classList.toggle('d-none', step !== edittotalSteps);
    }

    function showFieldError(input, message) {
        const span = document.createElement('span');
        span.classList.add('text-danger');
        span.innerText = message;
        input.insertAdjacentElement('afterend', span);
    }

    function validateStep1() {
        let isValid = true;

        editrequiredFieldsStep1.forEach(field => {
            const input = document.getElementById(field.id);
            const existingError = document.querySelector(`#${field.id} + .text-danger`);
            if (existingError) {
                existingError.remove();
            }

            if (!input.value.trim()) {
                showFieldError(input, field.message);
                isValid = false;
            }
        });

        return isValid;
    }

    function validateOptionalField(input, rules = []) {
        const existingError = input.parentNode.querySelector('.text-danger');
        if (existingError) {
            existingError.remove();
        }

        const value = input.value.trim();
        if (!value) return true; // field is empty, skip validation

        for (let rule of rules) {
            if (rule.type === 'minLength' && value.length < rule.value) {
                showFieldError(input, rule.message);
                return false;
            }
            if (rule.type === 'maxLength' && value.length > rule.value) {
                showFieldError(input, rule.message);
                return false;
            }
            if (rule.type === 'pattern' && !rule.value.test(value)) {
                showFieldError(input, rule.message);
                return false;
            }
        }

        return true;
    }

function validateMultiRowSection(rowSelector, fieldConfigs) {
    let isAnyFieldFilled = false;
    let isValid = true;

    document.querySelectorAll(rowSelector).forEach(row => {
        let rowHasInput = false;
        let rowIsValid = true;

        fieldConfigs.forEach(cfg => {
            const input = row.querySelector(cfg.selector);
            if (!input) return;

            const existingError = input.parentNode.querySelector('.text-danger');
            if (existingError) existingError.remove();

            const value = input.value.trim();
            if (value) rowHasInput = true;

            if (value) {
                for (let rule of cfg.rules) {
                    if (rule.type === 'minLength' && value.length < rule.value) {
                        showFieldError(input, rule.message);
                        rowIsValid = false;
                        break;
                    }
                    if (rule.type === 'maxLength' && value.length > rule.value) {
                        showFieldError(input, rule.message);
                        rowIsValid = false;
                        break;
                    }
                    if (rule.type === 'pattern' && !rule.value.test(value)) {
                        showFieldError(input, rule.message);
                        rowIsValid = false;
                        break;
                    }
                }
            }
        });

        if (rowHasInput) isAnyFieldFilled = true;
        if (!rowIsValid) isValid = false;
    });

    return !isAnyFieldFilled || isValid;
}


    // Initial Step
    showeditStep(editcurrentStep);

    document.getElementById('prevBtn').addEventListener('click', () => {
        if (editcurrentStep > 1) {
            editcurrentStep--;
            showeditStep(editcurrentStep);
        }
    });

    document.getElementById('nextBtn').addEventListener('click', function (e) {
        e.preventDefault();

        if (editcurrentStep === 1) {
            const isStep1Valid = validateStep1();
            if (isStep1Valid) {
                editcurrentStep++;
                showeditStep(editcurrentStep);
            }
        } else if (editcurrentStep === 2) {
            const bankFields = [
                {
                    el: document.getElementById('bankname'),
                    rules: [
                        { type: 'minLength', value: 3, message: 'Bank Name must be at least 3 characters' },
                        { type: 'maxLength', value: 30, message: 'Bank Name must be less than 30 characters' }
                    ]
                },
                {
                    el: document.getElementById('accholdername'),
                    rules: [
                        { type: 'minLength', value: 3, message: 'Holder Name must be at least 3 characters' },
                        { type: 'maxLength', value: 30, message: 'Holder Name must be less than 30 characters' }
                    ]
                },
                {
                    el: document.getElementById('acc_number'),
                    rules: [
                        { type: 'pattern', value: /^\d{6,20}$/, message: 'Account number must be 6–20 digits' }
                    ]
                },
                {
                    el: document.getElementById('accno'),
                    rules: [
                        {
                            type: 'pattern',
                            value: /^[A-Z]{3,5}0[A-Z0-9]{3,6}$/i, // more flexible IFSC pattern
                            message: 'Invalid IFSC code format'
                        }
                    ]
                }
            ];

            const anyFilled = bankFields.some(f => f.el.value.trim() !== '');

            if (!anyFilled || bankFields.every(f => validateOptionalField(f.el, f.rules))) {
                editcurrentStep++;
                showeditStep(editcurrentStep);
            }
        } else if (editcurrentStep === 3) {
            const isValid = validateMultiRowSection('#app1 tr', [
                { selector: '.f_name', rules: [{ type: 'minLength', value: 2, message: 'Name too short' }] },
                { selector: '.f_relation', rules: [{ type: 'minLength', value: 2, message: 'Relation too short' }] },
                { selector: '.f_mobile', rules: [{ type: 'pattern', value: /^\d{10}$/, message: 'Invalid Mobile' }] },
                { selector: '.f_email', rules: [{ type: 'pattern', value: /^[^\s@]+@[^\s@]+\.[^\s@]+$/, message: 'Invalid Email' }] }
            ]);
            if (isValid) {
                editcurrentStep++;
                showeditStep(editcurrentStep);
            }
        } else if (editcurrentStep === 4) {
            const isValid = validateMultiRowSection('#app2 tr', [
                { selector: 'select[name="level[]"]', rules: [{ type: 'minLength', value: 1, message: 'Level is required' }] },
                { selector: '.degree', rules: [{ type: 'minLength', value: 2, message: 'Degree too short' }] },
                { selector: '.univers', rules: [{ type: 'minLength', value: 2, message: 'University too short' }] },
                { selector: '.passout', rules: [{ type: 'pattern', value: /^\d{4}$/, message: 'Invalid Year' }] }
            ]);
            if (isValid) {
                editcurrentStep++;
                showeditStep(editcurrentStep);
            }
        } else if (editcurrentStep === 5) {
            const isValid = validateMultiRowSection('#app3 tr', [
                { selector: '.exp_start_date', rules: [{ type: 'minLength', value: 1, message: 'Start Date is required' }] },
                { selector: '.exp_end_date', rules: [{ type: 'minLength', value: 1, message: 'End Date is required' }] },
                { selector: '.perioed', rules: [{ type: 'minLength', value: 1, message: 'Period is required' }] },
                { selector: '.rrole', rules: [{ type: 'minLength', value: 2, message: 'Role too short' }] },
                { selector: '.companyy', rules: [{ type: 'minLength', value: 2, message: 'Company too short' }] },
                { selector: '.dedis', rules: [{ type: 'minLength', value: 2, message: 'Designation too short' }] }
            ]);
            if (isValid) {
                editcurrentStep++;
                showeditStep(editcurrentStep);
            }
        } else if (editcurrentStep === 6) {
        // Final submit
        document.getElementById('editemplyoe').submit();
    }
    });







