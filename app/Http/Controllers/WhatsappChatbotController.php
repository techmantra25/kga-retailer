<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockBarcode;
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

        // step 1, 2
        $sender       = $request->sender;
        $message      = strtolower(trim($request->message ?? ''));
        $buttonValue  = strtolower(trim($request->button_value ?? ''));

        // step 3
        $product_serial_number  = strtolower(trim($request->product_serial_number ?? ''));

        // step 4
        $product_confirmation  = strtolower(trim($request->product_confirmation ?? ''));
        $product_id  = strtolower(trim($request->product_id ?? ''));

        // step 5
        $invoice_date  = strtolower(trim($request->invoice_date ?? ''));
        $pincode  = strtolower(trim($request->pincode ?? ''));
        $address  = strtolower(trim($request->address ?? ''));

        // step 6
        $call_booking_number = strtolower(trim($request->call_booking_number ?? ''));

        // Step 1.2.2
        $call_booked_id = strtolower(trim($request->call_booked_id ?? ''));

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

        // 4 handle product confirmation
        if($product_confirmation && $product_id) {
            Log::info("Product Confirmation Received", [
                "product_confirmation" => $product_confirmation,
                "product_id" => $product_id,
                "sender" => $sender
            ]);

            if($product_confirmation === 'yes') {
                $response = [
                    "status"  => "success",
                    "message" => "Thank you! 😊 We’ve received your confirmation. Please continue to the next step.",
                ];
            } else {
                $response = [
                    "status"  => "closed",
                    "message" => "Thank you for contacting us. You will receive a callback during our business hours, Monday to Friday, 10:00 AM – 6:00 PM.",
                ];
            }
             return response()->json($response);
        }

        // 5 handle Customer Address Details
        if($invoice_date && $pincode && $address) {
            Log::info("Customer Address Details Received", [
                "invoice_date" => $invoice_date,
                "pincode" => $pincode,
                "address" => $address,
                "sender" => $sender
            ]);
            $callId = 'CB' . strtoupper(uniqid());
            $response = [
                "status"  => "success",
                "message" => "Thank you! 😊 We’ve received your details, and our team will reach out soon.",
                "call_booking_number" => $callId ?? null,
            ];
            return response()->json($response);
        }

        // 6 handle Call Booking Number
        if($call_booking_number) {
            Log::info("Call Booking Number Received", [
                "call_booking_number" => $call_booking_number,
                "sender" => $sender
            ]); 

            if(str_starts_with($call_booking_number, 'cb')) {
                $callId = strtoupper($call_booking_number);
                $response = [
                    "status"  => "success",
                    "message" => "Your Call Booking Number: {$callId} is being processed. Our team will reach out to you soon.",
                    "call_id" => $callId,
                    "service_partner" => "KGA Test Service Team",
                    "service_partner_contact_number" => "9876543210"
                ];

            } else {
                $response = [
                    "status"  => "failed",
                    "message" => "The provided Call Booking ID is incorrect. Kindly enter the correct Call Booking ID.",
                ];
            }
            return response()->json($response);
        }
        // Step 1.2.2 handle Call Booked ID
        if($call_booked_id){
            Log::info("Call Booked ID Received", [
                "call_booked_id" => $call_booked_id,
                "sender" => $sender
            ]); 

            if(str_starts_with($call_booked_id, 'cb')) {
                $callId = strtoupper($call_booked_id);
                $response = [
                    "status"  => "success",
                    "message" => "Your Call Booking ID: {$callId} is being processed. Our team will reach out to you soon.",
                    "call_id" => $callId,
                    "service_partner" => "KGA Test Service Team",
                    "service_partner_contact_number" => "9876543210"
                ];

            } else {
                $response = [
                    "status"  => "failed",
                    "message" => "The provided Call Booking ID is incorrect. Kindly enter the correct Call Booking ID.",
                ];
            }
            return response()->json($response);
        }



        $response = [
            "status"  => "closed",
            "message" => "Thank you for contacting us. You will receive a callback during our business hours, Monday to Friday, 10:00 AM – 6:00 PM.",
        ];
        return response()->json($response);
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
        }else {
            Log::info("Call Status Request Detected");
            $response = [
                "status"  => "success",
                "message" => "Kindly enter your Call Booking ID"
            ];

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
        $productStock = StockBarcode::where('barcode_no', $sl)->where('is_stock_out', 1)->first();

        if (!$productStock) {
            $response = [
                "status"  => "failed",
                "message" => "Thank you for connecting with us. The provided SL No is incorrect. Kindly enter the correct SL No.",
            ];
        } else {
            $response = [
                "status"  => "success",
                "message" => "product found",
                "product" => optional($productStock->product)->title,
                "product_id" => optional($productStock->product)->id,

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