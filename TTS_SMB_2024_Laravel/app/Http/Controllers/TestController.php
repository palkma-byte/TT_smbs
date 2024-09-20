<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\CapitalCate;
use Arcanedev\LogViewer\Entities\Log as EntitiesLog;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    public function index()
    {
        return "Hello from TestController!";
    }
    public function show()
    {
        Log::info("Logging...");
        Log::alert("GGG");
        $results = DB::select('
        SELECT
            * 
        FROM
            `ttsmb2024`.`project_cate`
         
    ');
    

    // Assuming the 'name' column exists in the 'project' table or one of the joined tables
    foreach ($results as $result) {
        echo $result->CateID . "<br>"; // Adjust the column name as needed
    }
    }
}
