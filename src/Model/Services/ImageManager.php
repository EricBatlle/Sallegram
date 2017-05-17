<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 17/05/2017
 * Time: 17:44
 */

namespace SilexApp\Model\Services;


class ImageManager
{

   public function resizeAndCopy($filename,$dir,$nuevo_ancho,$nuevo_alto){
        $imgFilename = $filename->getClientOriginalName();
        $nombreFichero = $dir.'/'.$imgFilename;

        $thumb = imagecreatetruecolor($nuevo_ancho,$nuevo_alto);
        $origen = imagecreatefromjpeg($nombreFichero); //ToDo: better from string?

        $width = imagesx($origen);
        $height = imagesy($origen);

        imagecopyresized($thumb,$origen,0,0,0,0,$nuevo_ancho,$nuevo_alto,$width,$height);
        $newNameFile = $nombreFichero.$nuevo_ancho.'x'.$nuevo_alto.'.jpeg';
        imagejpeg($thumb,$newNameFile);
        return $newNameFile;
    }
}