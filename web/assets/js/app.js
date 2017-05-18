/**
 * Created by Erik on 30/03/2017.
 */
$(document).ready(function () {
    $('.navbar-form').submit(function(e){
        if(valid() == false){
            e.preventDefault();
        }
    });
});

function errorMessage(message) {
//    $('#errorMessage').append(message);
    $('#errorMessage').replaceWith("<p id='errorMessage'>"+message+"</p>");

}

function valid() {
    //nom d'usuari
    var user = $('#form_name').val();
    var len = user.length;
    var Exp = /^[a-z0-9]+$/i;
    if(!user.match(Exp)) {
        errorMessage("ERROR: El nombre de usuario solo puede contener caracteres alfanumericos");
        return false;
    }
    if(len > 20){
        errorMessage("ERROR: El nombre de usuario debe contener máximo 20 carácteres");
        return false;
    }

    //email
    var email = $('#form_email').val();
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (!re.test(email)){
        errorMessage("ERROR: El formato del email no es valido");
        return false;
    }
    //contraseña
    var pw = $('#form_password_first').val();
    var Max_Length = 12;
    var Min_Length = 6;
    if (pw.length < Max_Length) {
        if (pw.length >= Min_Length) {
            if (pw.match(`[a-z]`)) {
                if (pw.match(`[A-Z]`)) {
                    if (pw.match(`[0-9]`)) {
                        //alert("SUCCESS");
                    }
                    else {
                        errorMessage("ERROR: La clave debe tener al menos un caracter numérico");
                        return false;
                    }
                }
                else {
                    errorMessage("ERROR: La clave debe tener al menos una letra mayúscula");
                    return false;
                }
            }
            else {
                errorMessage("ERROR: La clave debe tener al menos una letra minúscula");
                return false;
            }
        }
        else {
            errorMessage("ERROR: La clave debe tener al menos 6 caracteres");
            return false;
        }
    }
    else {
        errorMessage("ERROR: La clave no puede tener más de 12 caracteres");
        return false;
    }
    //passwordconf
    var pwc = $('#form_password_second').val();
    if(!pwc == pw){
        errorMessage("ERROR: la verificación del password no coincide");
        return false;
    }
    return true;
}
