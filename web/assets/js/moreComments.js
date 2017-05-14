/**
 * Created by Erik on 14/05/2017.
 */
/**
 * Created by Erik on 11/05/2017.
 */
// this is the id of the form
$(".more_comment_form").submit(function(e) {
    e.preventDefault();
    var image_id = $('.more_comment_input').attr('id');
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
            console.log(array); // show response from the php script.
            if(array[0] == false){
                console.log('do nothing')
            }else{
                console.log('do this')
                //eliminar el contenedor de comentarios actual
                $("#comments").empty();
                //añadir el nuevo
            }
        },
        error: function(error)
        {
            console.log(error);
        }
    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});