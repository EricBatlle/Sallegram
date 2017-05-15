/**
 * Created by Erik on 14/05/2017.
 */
/**
 * Created by Erik on 11/05/2017.
 */
$(".more_comment_form").submit(function(e) {
    e.preventDefault();
    var image_id = $('.more_comment_input').attr('id');
    var id = parseInt($('.more_comment_form').attr('id')) + 1
    $('.more_comment_form').attr('id',id);
    var clicks = $('.more_comment_form').attr('id');

    var url = "/comment/add/"+image_id+"/"+clicks; // the script where you handle the form input.

    $.ajax({
        type: "POST",
        url: url,
        dataType: 'json',
        data: $(".more_comment_form").serialize(), // serializes the form's elements.
        success: function(data)
        {
            var array = $.map(data, function(value, index) {
                return [value];
            });
            //console.log(array); // show response from the php script.
            //array[2] = true if logged
            if(array[0] == true){
                //eliminar el contenedor de comentarios actual
                $("#coments").empty();
                //llenar el contenedor con los nuevos comentarios
                for(var i = 0; i < array[1].length;i++){
                    var txt = document.createElement("p");
                    txt.innerHTML = array[1][i].comment;
                    //añadir el nuevo
                    $("#coments").append(txt);
                }

            }
        },
        error: function(error)
        {
            console.log(error);
        }
    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});