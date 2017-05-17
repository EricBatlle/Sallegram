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
            //array[2] = true if logged
            for(var i = 0; i < array[1].length;i++){
                var img = $(document.createElement("img"));
                    img.attr('src','assets/uploads/'+array[1][i].img_path);
                    img.attr('width',200);
                    img.attr('height',200);

                var aname = array[1][i].title;
                var atitle = "<a id='atag' href='/photo/'"+array[1][i].id+">"+aname+"</a>"
                var title = document.createElement("p");
                    title.innerHTML = "Title "+atitle;

                var aname = array[1][i].username;
                var ausername = "<a id='atag' href='/profile/'"+array[1][i].user_id+">"+aname+"</a>"
                var username = document.createElement("p");
                    username.innerHTML = "Username "+ausername;

                var date = document.createElement("p");
                    date.innerHTML = "Date "+array[1][i].created_at;
                var likes = document.createElement("p");
                    likes.innerHTML = "Likes "+array[1][i].likes;
                var form_like = $(document.createElement("form"));
                    form_like.attr('id',array[1][i].id);
                    form_like.attr('class','like_form');
                    form_like.attr('METHOD','POST');
                    form_like.attr('enctype','multipart/form-data');
                var input = $(document.createElement(("input")));
                    input.attr('type','submit');
                    //Mirar si la imagen esta en la lista de likes
                    if(isLiked(array[1][i].id,array[2])){
                        input.attr('value','Like');
                    }else{
                        input.attr('value','Dislike');
                    }
                form_like.append(input);

                var visits = document.createElement("p");
                    visits.innerHTML = "Visits "+array[1][i].visits;

                if(array[2] == true){
                    //ToDo: Doing second form, the comments, check possible errors confusion with 2 forms
                    var form_comment = $(document.createElement("form"));
                        form_comment.attr('id',array[1][i].id);
                        form_comment.attr('class','comment_form');
                        form_comment.attr('METHOD','POST');
                        form_comment.attr('enctype','multipart/form-data');
                        form_comment.attr('action','');
                    var textarea = $(document.createElement(("textarea")));
                        textarea.attr('id',array[1][i].id);
                        textarea.attr('name','comment');
                        textarea.attr('class','comment');
                    var input = $(document.createElement(("input")));
                        input.attr('type','submit');
                        input.submit();
                    form_comment.append(textarea);
                    form_comment.append(input);
                }

                //añadir el nuevo
                $("#top5").after(form_comment);
                $("#top5").after(visits);
                $("#top5").after(form_like);
                $("#top5").after(likes);
                $("#top5").after(date);
                $("#top5").after(username);
                $("#top5").after(title);
                $("#top5").after(img);

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

function isLiked(id, liked) {
    itIs = false;

    for(var j=0;(j<liked.length) && (liked.length > 0);j++){
        if(id == liked[j-1].id){
            return itIs = true;
        }
    }
    return itIs;
}

