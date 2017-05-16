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
                //ToDo: like_form
                var visits = document.createElement("p");
                    visits.innerHTML = "Visits "+array[1][i].visits;

                if(array[2] == true){
                    //ToDo: Doing second form, the comments, check possible errors confusion with 2 forms
                    var form = $(document.createElement("form"));
                        form.attr('id',array[1][i].id);
                        form.attr('class','comment_form');
                        form.attr('METHOD','POST');
                        form.attr('enctype','multipart/form-data');
                    var input = $(document.createElement(("input")));
                        input.attr('type','submit');
                    form.append(input);
                }

                //añadir el nuevo
                $("#top5").after(form);
                $("#top5").after(visits);
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