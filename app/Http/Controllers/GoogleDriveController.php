<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;

class GoogleDriveController extends Controller
{    
    private $drive;
    public function __construct(Google_Client $client)
    {
        $this->middleware(function($request ,$next) use ($client){
            //dd($client);
            //dd(Auth::user()->refresh_token);
            $client->refreshToken(Auth::user()->refresh_token);
            $this->drive = new Google_Service_Drive($client);
            return $next($request);
        });   
    }

    public function getFolders()
    {
        $this->ListFolders('1NQ_tSpyadfq6aND6rCklcarXSivCP-Oo');
    }

    public function ListFolders($id){
        $query = "mimeType='application/vnd.google-apps.folder' and '".$id."' in parents and trashed=false";

        $optParams = [
            'fields' => 'files(id,name)',
            'q' => $query
        ];

        $results = $this->drive->files->ListFiles($optParams);
        $list = $results->getFiles();

        print view('drive.index',compact('list'));
    }

    public function uploadFiles(Request $request)
    {
        if($request->isMethod('GET')){
            return view('drive.upload');
        }else{
            $this->createFile($request->file('file'));
        }
    }
}
