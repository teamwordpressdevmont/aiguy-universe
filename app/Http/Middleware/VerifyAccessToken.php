<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Token;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class VerifyAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the token from the request headers
        $accessToken = $request->header('Authorization');

        if (!$accessToken) {
            return response()->json(['message' => 'Access Token is required'], 401);
        }

        // Fetch the stored token from the database
        $tokenRecord = Token::first();

        if (!$tokenRecord) {
            return response()->json(['message' => 'Invalid Access Token'], 401);
        }

        // Attempt to decrypt stored tokens and retrieve expiration times
        try {
            $decryptedStoredToken = Crypt::decryptString($tokenRecord->token);
            $decryptedStoredTokenPrev = $tokenRecord->previous_token ? Crypt::decryptString($tokenRecord->previous_token) : null;

            $expiresAt = Carbon::parse($tokenRecord->expires_at);
            $previousExpiresAt = $tokenRecord->previous_expires_at ? Carbon::parse($tokenRecord->previous_expires_at) : null;
        } catch (\Exception $e) {
            return response()->json(['message' => 'Stored token decryption failed'], 401);
        }

        // Attempt to decrypt the provided token
        try {
            $decryptedHeaderToken = Crypt::decryptString($accessToken);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid Access Token format'], 401);
        }

        $currentTime = Carbon::now();

    //   if (
    //         ($decryptedHeaderToken !== $decryptedStoredToken && !( $currentTime->lt($expiresAt) ||
    //         ($decryptedHeaderToken === $decryptedStoredTokenPrev && $previousExpiresAt && $currentTime->lt($previousExpiresAt)) ) )
    //     ) 
        if ( ($decryptedHeaderToken !== $decryptedStoredToken || $currentTime->gt($expiresAt)) && 
        ($decryptedHeaderToken !== $decryptedStoredTokenPrev || !$previousExpiresAt || $currentTime->gt($previousExpiresAt)) )
        {
            return response()->json(['message' => 'Unauthorized: Token Expired'], 401);
        }
        return $next($request);
    }
}
