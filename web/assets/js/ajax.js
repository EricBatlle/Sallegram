/**
 * Created by Erik on 11/05/2017.
 */
// this is the id of the form
$(".comment_form").submit(function(e) {
    e.preventDefault();
    console.log($(this).children('.comment').val());
    var url = "/addComment/80"; // the script where you handle the form input.

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