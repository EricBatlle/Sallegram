/**
 * Created by Erik on 17/05/2017.
 */
/**
 * Created by Erik on 14/05/2017.
 */
/**
 * Created by Erik on 11/05/2017.
 */
$(".more_last5_form").submit(function(e) {
    e.preventDefault();
    var image_id = $('.more_last5_input').attr('id');
    var id = parseInt($('.more_last5_form').attr('id')) + 1
    $('.more_last5_form').attr('id',id);
    var clicks = $('.more_last5_form').attr('id');
    var url = "/add/last5/"+clicks; // the script where you handle the form input.

    $.ajax({
        type: "POST",
        url: url,
        dataType: 'json',
        data: $(".more_last5_form").serialize(), // serializes the form's elements.
        success: function(data)
        {
            var array = $.map(data, function(value, index) {
                return [value];
            });
            if(data[0] != false) {
                for (var i = 0; i < array[1].length; i++) {
                    var img = $(document.createElement("img"));
                    img.attr('src', 'assets/uploads/' + array[1][i].img_path);
                    img.attr('width', 200);
                    img.attr('height', 200);

                    var aname = array[1][i].title;
                    var atitle = "<a id='atag' href=/photo/"+ array[1][i].id + ">" + aname + "</a>"
                    var title = document.createElement("p");
                    title.innerHTML = "Title " + atitle;

                    var aname = array[1][i].username;
                    var ausername = "<a id='atag' href='/profile/" + array[1][i].user_id + "'>" + aname + "</a>"
                    var username = document.createElement("p");
                    username.innerHTML = "Username " + ausername;

                    var date = document.createElement("p");
                    date.innerHTML = "Date " + array[1][i].created_at;
                    var likes = document.createElement("p");
                    likes.innerHTML = "Likes " + array[1][i].likes;
                    var input = $(document.createElement(("input")));
                    input.attr('type', 'submit');
                    input.attr('id', array[1][i].id);
                    //Mirar si la imagen esta en la lista de likes

                    var form_like = $(document.createElement("form"));
                    form_like.attr('id', array[1][i].id);
                    form_like.attr('class', 'like_form');
                    form_like.attr('METHOD', 'POST');
                    form_like.attr('enctype', 'multipart/form-data');
                    if (isLiked(array[1][i].id, array[3])) {
                        input.attr('value', 'Dislike');
                        form_like.attr('name', 'Dislike');
                    } else {
                        input.attr('value', 'Like');
                        form_like.attr('name', 'Like');
                    }
                    form_like.submit(function (e) {
                        e.preventDefault();

                        //Check if value is Like or Dislike
                        var form_name = $(this).attr('name');
                        var form_id = $(this).attr('id');

                        //Cojer el mismo id del form para encontrar el input
                        var input_value = $('input[id=' + form_id + ']').attr('Value');
                        var url = "/like/" + input_value + "/" + form_id; // the script where you handle the form input.

                        $.ajax({
                            type: "POST",
                            url: url,
                            dataType: 'json',
                            data: $(".comment_form").serialize(), // serializes the form's elements.
                            success: function (data) {
                                var array = $.map(data, function (value, index) {
                                    return [value];
                                });


                                //Si estaba en like me devuelve un dislike
                                //Dislike = 1
                                if (array[0] == 1) {
                                    $('input[id=' + form_id + ']').attr('Value', 'Dislike');
                                } else {
                                    $('input[id=' + form_id + ']').attr('Value', 'Like');
                                }
                            },
                            error: function (error) {
                                console.log(error);
                            }
                        });

                        e.preventDefault(); // avoid to execute the actual submit of the form.
                    });

                    form_like.append(input);

                    var visits = document.createElement("p");
                    visits.innerHTML = "Visits " + array[1][i].visits;

                    if (array[2] == true) {
                        //ToDo: Doing second form, the comments, check possible errors confusion with 2 forms
                        var form_comment = $(document.createElement("form"));
                        form_comment.attr('id', array[1][i].id);
                        form_comment.attr('class', 'comment_form');
                        form_comment.attr('METHOD', 'POST');
                        form_comment.attr('enctype', 'multipart/form-data');
                        form_comment.attr('action', '');
                        form_comment.submit(function (e) {
                            e.preventDefault();
                            var comment = $(this).children('.comment').val();
                            var image_id = $(this).children('.comment').attr('id');
                            var url = "/addComment/" + image_id + "/" + comment; // the script where you handle the form input.

                            $.ajax({
                                type: "POST",
                                url: url,
                                dataType: 'json',
                                data: $(".comment_form").serialize(), // serializes the form's elements.
                                success: function (data) {
                                    var array = $.map(data, function (value, index) {
                                        return [value];
                                    });
                                },
                                error: function (error) {
                                    console.log(error);
                                }
                            });
                        });
                        var textarea = $(document.createElement(("textarea")));
                        textarea.attr('id', array[1][i].id);
                        textarea.attr('name', 'comment');
                        textarea.attr('class', 'comment');
                        var input = $(document.createElement(("input")));
                        input.attr('type', 'submit');
                        input.submit(function (e) {
                            e.preventDefault();
                            var comment = $(this).children('.comment').val();
                            var image_id = $(this).children('.comment').attr('id');
                            var url = "/addComment/" + image_id + "/" + comment; // the script where you handle the form input.

                            $.ajax({
                                type: "POST",
                                url: url,
                                dataType: 'json',
                                data: $(".comment_form").serialize(), // serializes the form's elements.
                                success: function (data) {
                                    var array = $.map(data, function (value, index) {
                                        return [value];
                                    });
                                },
                                error: function (error) {
                                    console.log(error);
                                }
                            });

                            e.preventDefault(); // avoid to execute the actual submit of the form.
                        });
                        form_comment.append(textarea);
                        form_comment.append(input);
                    }

                    //añadir el nuevo
                    $("#last5").after(form_comment);
                    $("#last5").after(visits);
                    $("#last5").after(form_like);
                    $("#last5").after(likes);
                    $("#last5").after(date);
                    $("#last5").after(username);
                    $("#last5").after(title);
                    $("#last5").after(img);
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

function isLiked(id, array_likeds) {
    for(var j=0;(j<array_likeds.length) && (array_likeds.length > 0);j++){
        if(id == array_likeds[j].id){
            return true;
        }
    }
    return false;
}

