<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use DB;

class AiController extends Controller
{

public function postAiImage(Request $request)
{
    
	$max_results = $request->max_results ??'';
          

            $prompt = $request->title ?? '';
            
            if ($request->style != 'none') {
                $prompt .= ', ' . $request->style; 
            } 
            
            if ($request->lightning != 'none') {
                $prompt .= ', ' . $request->lightning; 
            } 
            
            if ($request->artist != 'none') {
                $prompt .= ', ' . $request->artist; 
            }
            
            if ($request->medium != 'none') {
                $prompt .= ', ' . $request->medium; 
            }
            
            if ($request->mood != 'none') {
                $prompt .= ', ' .$request->mood; 
            }


            $complete = [
                    'prompt' => $prompt,
                    'size' => $request->resolution,
                    'n' => (int)$max_results,
                ];
       $key = "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";         
      $ch = curl_init();
  $headers  = [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer '.$key
    ];

     curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/images/generations');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($complete)); 

  $output = curl_exec($ch);
 $err = curl_error($ch);
  curl_close($ch);
   # Print any errors, to help with debugging.
if ($err) {
   echo "cURL Error #:" . $err;
  }
// var_dump($output);
// die();
  file_put_contents("imageAiImage".auth()->user()->id.".data",$output);
$foutput=file_get_contents("imageAiImage".auth()->user()->id.".data");
$joutput=json_decode($foutput,true);

if(isset($joutput)){
     $path = 'public/images/user_'.auth()->user()->id.'/ai/';
        if (!file_exists($path)) {
    mkdir($path, 0777, true);
}

    for($i = $max_results-1; $i >= 0; $i--){
   $temp = time();
  file_put_contents($path.$temp."image".$i.".png",file_get_contents($joutput["data"][$i]["url"]));
   
   DB::table('ai_content')->insert([
           'user_id'=> auth()->user()->id,
           //'name' =>  request("name"),
           'image_url' => $path.$temp."image".$i.".png",
           'resolution' => $request->resolution,
           'type' => 'ai_image',
       ]);
      
 if (file_exists("imageAiImage".auth()->user()->id.".data")) {
    unlink("imageAiImage".auth()->user()->id.".data");
    }

   
}




  
}






}
