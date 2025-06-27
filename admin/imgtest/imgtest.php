<!DOCTYPE html>
<html lang="en">

<?php
require '../../config/db.php';


?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require '../../components/link_imports.php' ?>
    <title>Document</title>
</head>
<script>

    $(document).ready(() => {
        $("#turncate").click(function () {
            $("#imgs").load('imgtestinner.php', { turncate: "turncate" });
        });

        $("#refresh").click(function () {
            $("#imgs").load('imgtestinner.php');
        });

        $("#imgs").on('change', "input[name='item-checkbox']", function () {
            checkAndToggleGetDataBtn();
        });

        function checkAndToggleGetDataBtn() {
            if ($("input[name='item-checkbox']:checked").length > 0) {
                showChecked();
                $("#sendDataBtn").show();
            } else {
                showChecked();
                $("#sendDataBtn").hide();
            }
        }

        $("#sendDataBtn").hide();
    });

    function deleteThis(e) {
        $("#imgs").load('imgtestinner.php', { delete: e.value });
    }

    function showChecked() {
        let checkedItems = $("input[name='item-checkbox']:checked");
        let selectedValues = [];
        let inputElementsToAppend = [];

        checkedItems.each(function () {
            selectedValues.push($(this).val());
            console.log($(this).val());

            let newInput = $("<input>", {
                type: 'number',
                name: 'selected_checkbox[]',
                class: 'bg-slate-400 m-2 p2',
                value: $(this).val()
            });

            inputElementsToAppend.push(newInput);
        });

        $("#sendInputs").empty();

        if (selectedValues.length > 0) {
            $("#contents").text(selectedValues.join(", "));
            $.each(inputElementsToAppend, function (index, element) {
                $("#sendInputs").append(element);
            })

        } else {
            $("#sendInputs").empty();
            $("#contents").text("No selected values");
        }
        console.log(selectedValues);
    }
</script>

<body class="bg-black text-white">
    <form method="POST" action="add.php" enctype="multipart/form-data" id="addImgForm">
        <input type="file" name="image" class="p-3 border border-slate-400 rounded-md" required>
        <br>
        <button name="submit" class="p-3 bg-blue-500 rounded-md text-white">Submit</button>
        <button type="button" id="turncate" class="p-3 bg-gray-500 rounded-md text-white">Clear All</button>
    </form>
    <button type="button" id="refresh" class="p-3 bg-gray-500 rounded-md text-white">Refresh</button>


    <br>
    <hr><br>
    <div id="imgs">
        <?php include 'imgtestinner.php'; ?>
    </div>

    <form action="send_data.php" method="POST" id="sendForm" class="bg-slate-600 p-2 m-2 rounded-md">
        <div id="contents" class="bg-slate-700 p-2 m-2 block s-full rounded-md">
            Selected Values...
        </div>
        <div id="sendInputs"></div>
        <button type="submit" id="sendDataBtn" class="p-3 bg-green-500 rounded-md text-white" onclick="">
            Send Data
        </button>
    </form>
</body>
<script>
</script>

</html>