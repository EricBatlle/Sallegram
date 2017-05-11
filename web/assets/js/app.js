/**
 * Created by Erik on 30/03/2017.
 */

function readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#blah').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
//ToDo: CSS to hide the img
$("#form_image_profile").change(function(){
    console.log("Debug");
    readURL(this);
});
