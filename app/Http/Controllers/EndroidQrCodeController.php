<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EndroidQrCodeController extends Controller
{
    public function generateQrCode(Request $request, $restaurante)
    {
        // Validate or sanitize the restaurante param as you see fit. For now we expect a numeric id or slug:
        if (empty($restaurante)) {
            return response()->json(['error' => 'restaurante no proporcionado'], 400);
        }

        // Full menu URL that the QR will point to (absolute)
        $menuUrl = url("/carta/{$restaurante}");

        // Build QR image URL (using external QR API for simplicity)
        $content = urlencode($menuUrl);
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?data={$content}&size=300x300";

        // For AJAX requests return JSON; if someone visits directly, optionally show a simple view
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'qr_url' => $qrUrl,
                'menu_url' => $menuUrl
            ]);
        }

        // If not ajax, render a simple view with the QR (optional)
        return view('Items_Menu.qr_code', [
            'qr_url' => $qrUrl,
            'menu_url' => $menuUrl
        ]);
    }
}
