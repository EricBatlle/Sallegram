/**
 * Created by Erik on 14/05/2017.
 */
/**
 * Created by Erik on 11/05/2017.
 */
// this is the id of the form
$(".like_form").submit(function(e) {
    e.preventDefault();

    //Check if value is Like or Dislike
    var i = $(".input_like").attr('value');
    $(".like_input").attr('value', 'holita');

    console.log(i);
    var id_img
    var id_user

    var url = "/addComment/"+image_id+"/"+comment; // the script where you handle the form input.


    $.ajax({
        type: "POST",
        url: url,
        dataType: 'json',
        data: $(".comment_form").serialize(), // serializes the form's elements.
        success: function(data)
        {
            var array = $.map(data, function(value, index) {
                return [value];
            });
            console.log(array); // show response from the php script.
        },
        error: function(error)
        {
            console.log(error);
        }
    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});