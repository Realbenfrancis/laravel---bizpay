/**
 * Created by teamwork on 3/01/2017.
 */



$(document).ready(function() {
    $("input[name$='check']").click(function() {
        var test = $(this).val();

        $("div.desc").hide();
        $("#rd-sec" + test).show();
    });
});


$(".show-more").click(function(event) {
    var txt = $(".hide-part").is(':visible') ? 'show remaining +' : 'hide remaining -';
    $(".hide-part").toggleClass("show-part");
    $(this).html(txt);
    event.preventDefault();
});

$(".visible-area").click(function(event) {
    var txt = $(".visible-div").is(':visible') ? 'show remaining +' : 'hide remaining -';
    $(".visible-div").toggleClass("hide-div");
    $(this).html(txt);
    event.preventDefault();
});



document.getElementById('test').addEventListener('change', function () {
    var style = this.value == 7 ? 'block' : 'none';
    document.getElementById('hidden_div').style.display = style;
});






