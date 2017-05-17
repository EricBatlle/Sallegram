/**
 * Created by Erik on 30/03/2017.
 */
/*

function validateForm() {
    var x = document.forms["myForm"]["fname"].value;
    if (x == "") {
        alert("Name must be filled out");
        return false;
    }
}
*/
$(document).ready(function () {
    $('#form_submit').submit(function(e){
        e.preventDefault();
        console.log("warap");
    });
});

