<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Illuminate\Support\Str;

class DeployController extends Controller
{
    public function deploy(Request $request)
    {
        $payload = $request->getContent();
        $agent = $request->header('User-Agent');
        $contains = Str::contains($agent, 'Bitbucket-Webhooks');
        if ($contains) {
            $root_path = base_path();
            $process = new Process('cd ' . $root_path . ' && source ./deploy.sh');
            $process->run(function ($type, $buffer) {
                Log::info("#Deploy - {$type}, {$buffer}");
                echo $buffer;
            });
        }


        // foreach ($payload as $change) {
        //     // $branch = $commit->branch;
        //     Log::info("#Email invoice notification sent to {$change}");
        // }

        // Log::info("#Email invoice notification sent to {$payload}");
        // $githubHash = $request->header('X-Hub-Signature');
        // $localToken = config('app.deploy_secret');
        // $localHash = 'sha1=' . hash_hmac('sha1', $githubPayload, $localToken, false);
        // if (hash_equals($githubHash, $localHash)) {
        //     $root_path = base_path();
        //     $process = new Process('cd ' . base_path() . '; source deploy.sh');
        //     $process->run(function ($type, $buffer) {
        //         echo $buffer;
        //     });
        // }
    }
}
