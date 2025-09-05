document.addEventListener("DOMContentLoaded", function () {
    // Initialize any date pickers
    $(".datepicker").datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true,
    });

    // Initialize select2 for dropdowns
    $(".select2").select2({
        theme: "bootstrap4",
    });

    // Handle form submission with draft/submit buttons
    $("form").on("click", 'button[type="submit"]', function () {
        const form = $(this).closest("form");
        const action = $(this).attr("name");
        const value = $(this).attr("value");

        if (action && value) {
            $("<input>")
                .attr({
                    type: "hidden",
                    name: action,
                    value: value,
                })
                .appendTo(form);
        }
    });
});
