<?php

namespace App\Http\Controllers;

use App\Http\Requests\EncodeUrlRequest;
use App\Http\Requests\DecodeUrlRequest;
use App\Http\Resources\UrlEncodeResource;
use App\Http\Resources\UrlDecodeResource;
use App\Models\Url;
use App\Services\EventLoggerService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class UrlController extends Controller
{
    public function encodeUrl(EncodeUrlRequest $request)
    {
        try {

            $validatedData = $request->validated();
            $url = Url::where('original_url', $validatedData['original_url'])->first();

            if (is_null($url)) {
                $domain = Str::lower(Str::random(5) . "." . Str::random(3) . "/" . Str::random(6));
                $shortenedUrl = "http://{$domain}/";
                $shortenedUrlExist = true;

                while ($shortenedUrlExist) {
                    $shortenedUrlAlreadyExistInDb = Url::where('shortened_url', $shortenedUrl)->exists();
                    if ($shortenedUrlAlreadyExistInDb) {
                        $domain = Str::lower(Str::random(5) . "." . Str::random(3) . "/" . Str::random(6));
                        $shortenedUrl = "http://{$domain}/";
                    } else {
                        $shortenedUrlExist = false;
                    }
                }

                $url = Url::create([
                    'original_url' => $validatedData['original_url'],
                    'shortened_url' => $shortenedUrl
                ]);

            }

            return response()->json(new UrlEncodeResource($url));

        } catch (Exception $e) {
            EventLoggerService::errorLogger($e);

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }


    public function decodeUrl(DecodeUrlRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $url = Url::where('shortened_url', $validatedData['shortened_url'])->firstOrFail();

            EventLoggerService::infoLogger("Url ({$url->shortened_url}) was successfully decoded");

            return response()->json(new UrlDecodeResource($url));
        } catch (ModelNotFoundException $e) {
            EventLoggerService::errorLogger($e);

            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
