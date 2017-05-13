/**
 * Created by Erik on 13/05/2017.
 */
/**
 * Created by Erik on 30/03/2017.
 */

function readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#thumbnail').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

//ToDo: CSS to hide the img
$(".form_thumbnail").change(function(){
    readURL(this);
});


