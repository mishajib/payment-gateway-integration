<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageCompressController extends Controller
{
    public function index()
    {
        return view('file');
    }

    public function store(Request $request)
    {
        if ($request->hasFile('image')) {
//            dd('ok');
            //get filename with extension
            $filenamewithextension = $request->file('image')->getClientOriginalName();

            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);

            //get file extension
            $extension = $request->file('image')->getClientOriginalExtension();

            //filename to store
            $filenametostore = $filename . '_' . time() . '.' . $extension;

            //Upload File
            $request->file('image')->storeAs('public/profile_images', $filenametostore);

            //Compress Image Code Here
            $filepath = public_path('storage/profile_images/' . $filenametostore);
            $mime     = mime_content_type($filepath);
            $output   = new \CURLFile($filepath, $mime, $filenametostore);
            $data     = ["files" => $output];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://api.resmush.it/?qlty=40');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                $result = curl_error($ch);
            }
            curl_close($ch);

            $arr_result = json_decode($result);

            // store the optimized version of the image
            $ch = curl_init($arr_result->dest);
            $fp = fopen($filepath, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);


            return redirect()->route('image.compress')->with('success', "Image uploaded successfully.");
        }
    }

}
