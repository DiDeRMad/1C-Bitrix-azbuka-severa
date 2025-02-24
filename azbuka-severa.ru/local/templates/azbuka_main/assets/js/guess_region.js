$(document).ready(function() {
    $('.show_city').on('click', function () {
        $('#select_region').show();
    })
    $("#guess_region .buttons a").on("click", function(e) {
        e.preventDefault();
        if ($(this).hasClass("yes")) {
            var region = $(this).attr("data-region");
            $.ajax({
                type: "GET",
                url: "/local/ajax/set_region.php",
                data: "region="+region,
                contentType: "html",
                success: function(data) {
                    var obj = JSON.parse(data);
                    if (obj.success == "Y") {
                        var vhost = location.hostname;
                        if (vhost != obj.vhost) {
                            location.href = location.protocol+"//"+obj.vhost+location.pathname;
                        } else {
                            $("#guess_region").remove();
                            //location.reload();
                        }
                    }
                }
            });
        } else {
            $("#guess_region").hide();
            $("#select_region").show();
            $("#select_region .select_city").toggle("fast");
        }
    });
});