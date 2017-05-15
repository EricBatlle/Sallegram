/**
 * Created by Erik on 14/05/2017.
 */
/**
 * Created by Erik on 11/05/2017.
 */
$(".more_top5_form").submit(function(e) {
    e.preventDefault();
    var image_id = $('.more_top5_input').attr('id');
    var id = parseInt($('.more_top5_form').attr('id')) + 1
    $('.more_top5_form').attr('id',id);
    var clicks = $('.more_top5_form').attr('id');
    console.log("ie")
    var url = "/add/top5/"+clicks; // the script where you handle the form input.

    $.ajax({
        type: "POST",
        url: url,
        dataType: 'json',
        data: $(".more_top5_form").serialize(), // serializes the form's elements.
        success: function(data)
        {
            var array = $.map(data, function(value, index) {
                return [value];
            });
            console.log(array); // show response from the php script.

            for(var i = 0; i < array[1].length;i++){
                var element = document.createElement("h1");
                element.innerHTML = array[1][i].title;
                //añadir el nuevo
                $("#top5").after(element);
            }
            //Append
        },
        error: function(error)
        {
            console.log(error);
        }
    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});