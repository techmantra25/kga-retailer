<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\KgaSalesData;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class WhatsappChatbotController extends Controller
{
    // sender: 919876543210
    // profile_name: Rahul
    // waba_number: 12025550198
    // message: ""
    // button_value: Repair / Service
    public function handle(Request $request)
    {
        Log::info("Webhook Triggered", $request->all());

        $sender       = $request->sender;
        $message      = strtolower(trim($request->message ?? ''));
        $buttonValue  = strtolower(trim($request->button_value ?? ''));
        $product_serial_number  = strtolower(trim($request->product_serial_number ?? ''));

        /**
         * 1. Handle Service Selection
         */
        if (in_array($buttonValue, ['installation', 'repair / service', 'call status'])) {
            Log::info("Service Button Detected", [
                "button_value" => $buttonValue,
                "sender" => $sender
            ]);

            return $this->serviceResponse($buttonValue);
        }
        if (in_array($buttonValue, ['catalogue'])) {
            Log::info("Service Button Detected", [
                "button_value" => $buttonValue,
                "sender" => $sender
            ]);
            $response = [
                "status"  => "success",
                "message" => "Thank you! 😊 We’ve received your details, and our team will reach out soon.This is for lead capture only. No catalogue will be sent.",
            ];
            Log::info('WhatsApp Button Response', $response);

            return response()->json($response);
        }

        /**
         * 2. Handle Category Selection
         */
        if (str_starts_with($buttonValue, 'category:')) {

            $categoryId = str_replace('category:', '', $buttonValue);

            Log::info("Category Selected", [
                "category_id" => $categoryId,
                "sender" => $sender
            ]);

            // 👉 NEXT STEP AFTER CATEGORY
            // Option A: Ask serial number
            return $this->askSerialNumber($categoryId);
        }

        // 3 handle Product Serial Number
        if($product_serial_number) {
            Log::info("Product Serial Number Received", [
                "serial_number" => $product_serial_number,
                "sender" => $sender
            ]);

            return $this->validateProduct($product_serial_number);
        }

        Log::warning("No recognized input received", [
            "sender" => $sender,
            "button_value" => $buttonValue,
            "message" => $message
        ]);
    }



    private function serviceResponse($value)
    {

        // valid service types
        $serviceTypes = ["installation", "repair / service"];

        if (in_array($value, $serviceTypes)) {
            // Handle Installation & Repair category listing
            $query = Product::where('type', 'fg');

            if ($value === "installation") {
                $query->where('is_installable', 1);
            }

            if ($value === "repair / service") {
                $query->where('repair_charge', '>', 0);
            }

            $catIds = $query
                ->pluck('cat_id')
                ->unique()
                ->toArray();


            $categories = Category::whereIn('id', $catIds)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(function ($cat) {
                    $name = strtolower($cat->name);
                    $cat->name = ucwords(str_replace('_', ' ', $name));
                    return $cat;
                });


           $buttons = $categories
            ->take(10)
            ->map(function ($cat) {
                return [
                    "title" => strtoupper($cat->name),
                    "value" => "category:" . $cat->id
                ];
            })
            ->values()
            ->toArray();


           
            $response = [
                "status"  => "success",
                "message" => "Kindly Select Your Product",
                "buttons" => $buttons
            ];
            Log::info('WhatsApp Button Response', $response);

            return response()->json($response);
        } 
        else {

            Log::info("Call Status Request Detected");

            $response = [
                "status"  => "success",
                "message" => "Kindly enter your Call Booking ID"
            ];

            Log::info("Final Response (Call Status)", $response);

            return response()->json($response);
        }
    }

    private function askSerialNumber()
    {
        $response = [
            "status"  => "success",
            "message" => "Kindly enter your Product Serial Number.",
        ];
        return response()->json($response);
    }

    private function validateProduct($sl)
    {
        $product = KgaSalesData::where('serial', $sl)->first();

        if (!$product) {
            $response = [
                "status"  => "failed",
                "code"    => 404,
                "message" => "Kindly enter your Product Serial Number.",
            ];
        } else {
            $response = [
                "status"  => "success",
                "code"    => 200,
                "message" => "Product Found: " . $product->name,
            ];
        }

        return response()->json($response);
    }



    // <?php

    // namespace App\Http\Controllers;

    // use Illuminate\Http\Request;
    // use App\Models\Booking;
    // use App\Models\Product;

    // class ChatbotController extends Controller
    // {
    //     public function handle(Request $request)
    //     {
    //         $sender     = $request->sender;
    //         $message    = strtolower(trim($request->message));
    //         $value      = strtolower(trim($request->button_value));

    //         /* -----------------------------
    //         1. Handle Service Selection
    //         ------------------------------*/
    //         if (in_array($value, ['installation', 'repair_service', 'call_status'])) {
    //             return $this->serviceResponse($value);
    //         }

    //         /* -----------------------------
    //         2. Handle Product Category
    //         ------------------------------*/
    //         if (in_array($value, ['ac', 'tv', 'chimney'])) {
    //             return $this->askSerialNumber();
    //         }

    //         /* -----------------------------
    //         3. Validate Serial Number
    //         ------------------------------*/
    //         if ($this->isSerialNumber($message)) {
    //             return $this->validateProduct($message);
    //         }

    //         /* -----------------------------
    //         4. Validate Booking ID
    //         ------------------------------*/
    //         if ($this->isBookingId($message)) {
    //             return $this->bookingStatus($message);
    //         }

    //         return $this->defaultMessage();
    //     }

    //     private function serviceResponse($type)
    //     {
    //         if ($type == 'installation') {
    //             return $this->reply("Please select your product: AC / TV / Chimney");
    //         }

    //         if ($type == 'repair_service') {
    //             return $this->reply("Please enter your Product Serial Number.");
    //         }

    //         if ($type == 'call_status') {
    //             return $this->reply("Please enter your Call Booking ID.");
    //         }
    //     }

    //     private function askSerialNumber()
    //     {
    //         return $this->reply("Kindly enter your Product Serial Number.");
    //     }

    

    //     private function bookingStatus($bookingId)
    //     {
    //         $booking = Booking::where('id', $bookingId)->first();

    //         if (!$booking) {
    //             return $this->reply("❌ No Booking Found with this ID.");
    //         }

    //         return $this->reply("Your Current Booking Status: " . $booking->status);
    //     }

    //     private function defaultMessage()
    //     {
    //         return $this->reply("Sorry, I did not understand that. Please select an option.");
    //     }

    //     private function isSerialNumber($str)
    //     {
    //         return strlen($str) > 5;
    //     }

    //     private function isBookingId($str)
    //     {
    //         return is_numeric($str);
    //     }

    //     private function reply($text)
    //     {
    //         return response()->json([
    //             "messages" => [
    //                 ["text" => $text]
    //             ]
    //         ]);
    //     }
    // }

}