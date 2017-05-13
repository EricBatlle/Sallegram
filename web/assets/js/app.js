/**
 * Created by Erik on 30/03/2017.
 */

function validateForm() {
    var x = document.forms["myForm"]["fname"].value;
    if (x == "") {
        alert("Name must be filled out");
        return false;
    }
}

$('#form_submit').click(function(){
    console.log("warap");
});
