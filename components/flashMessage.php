<div id="flash-mesg"
    class="absolute right-0 w-96 max-h-24 my-2 p-3 border-l-5 rounded-l-md overflow-y-hidden bg-green-200 border-green-500 text-green-800 hidden">
</div>

<script>
    function setFlashMessage(state, mesg) {
        if (state == "success") {
            $("#flash-mesg").addClass('bg-green-200 border-green-500 text-green-800');
            $("#flash-mesg").removeClass('bg-red-100 border-red-400 text-red-800 bg-blue-100 border-blue-400 text-blue-800');
        } else if (state == "fail") {
            $("#flash-mesg").addClass('bg-red-100 border-red-400 text-red-800');
            $("#flash-mesg").removeClass('bg-green-200 border-green-500 text-green-800 bg-blue-100 border-blue-400 text-blue-800');
        } else if (state = "info") {
            $("#flash-mesg").addClass('bg-blue-100 border-blue-400 text-blue-800');
            $("#flash-mesg").removeClass('bg-red-100 border-red-400 text-red-800 bg-green-200 border-green-500 text-green-800');
        }
        $("#flash-mesg").text(mesg);
        $("#flash-mesg").animate({
            width: 'toggle'
        });
        setTimeout(() => {
            $("#flash-mesg").animate({
                width: 'toggle'
            });
        }, 3500);
    }

    console.log("Flahs Message script loaded");
    
</script>

