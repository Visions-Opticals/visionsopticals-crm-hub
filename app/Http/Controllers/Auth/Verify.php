<?php

namespace App\Http\Controllers\Auth;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Models\User;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Verify extends Controller
{
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function verifyEmail(Request $request, Sdk $sdk, string $id)
    {

        if (is_numeric($id)) {
            # bug fix for old links
            $user = User::find($id);
            $id = !empty($user) ? $user->uuid : $id;
        }
        $query = $sdk->createUserResource($id)->send('PUT', ['verify-account']);
        if ($query->isSuccessful()) {
            $response = (tabler_ui_html_response(['Successfully verified account.']))->setType(UiResponse::TYPE_SUCCESS);
        } else {
            $response = (tabler_ui_html_response([$query->errors[0]['title'] ?? 'Account verification failed.']))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect()->route('login')->with('UiResponse', $response);
    }
}
