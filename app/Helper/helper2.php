<?
public function get_dap_spare_warranty($dap_id, $sp_id)
{
    $crp_data = \App\Models\DapService::find($dap_id);
    $product_id = $crp_data->product_id;
    $warranty_status = 0;
    $crp_data->dealer_type = "khosla";

    $data = [
        'purchase_date' => $crp_data->bill_date,
        'booking_date' => $crp_data->entry_date,
        'warranty_end_date' => '',
        'warranty_status' => 0,
    ];

    $get_comprehensive_warranty = \App\Models\ProductWarranty::where('goods_id', $product_id)
        ->where('dealer_type', $crp_data->dealer_type)
        ->where('warranty_type', 'comprehensive')
        ->first();

    if ($get_comprehensive_warranty) {
        // Retrieve additional warranty if available
        $get_additional_warranty = \App\Models\ProductWarranty::where('goods_id', $product_id)
            ->where('dealer_type', $crp_data->dealer_type)
            ->where('warranty_type', 'additional')
            ->where('additional_warranty_type', 1)
            ->first();

        // Calculate total warranty period including additional
        $additional_warranty_period = $get_additional_warranty ? $get_additional_warranty->warranty_period : 0;
        $warranty_period = $get_comprehensive_warranty->warranty_period + $additional_warranty_period;

        // Calculate warranty end date
        $warranty_end_date = date('Y-m-d', strtotime($crp_data->bill_date . ' + ' . $warranty_period . ' months'));
        $warranty_end_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));

        if ($crp_data->entry_date < $warranty_end_date) {
            $warranty_status = 1; // Yes
            $data['purchase_date'] = $crp_data->bill_date;
            $data['booking_date'] = $crp_data->entry_date;
            $data['warranty_end_date'] = date('d-m-Y', strtotime($warranty_end_date));
            $data['warranty_status'] = $warranty_status;
            $data['warranty_period'] = $warranty_period;
        } else {
            // Check for parts warranty if outside of comprehensive/additional warranty
            $get_parts_warranty = \App\Models\ProductWarranty::where('goods_id', $product_id)
                ->where('dealer_type', $crp_data->dealer_type)
                ->where('warranty_type', 'parts')
                ->where('spear_id', $sp_id)
                ->first();

            if ($get_parts_warranty) {
                $part_warranty_end_date = date('Y-m-d', strtotime($crp_data->bill_date . ' + ' . $get_parts_warranty->warranty_period . ' months'));
                $part_warranty_end_date = date('Y-m-d', strtotime($part_warranty_end_date . ' -1 days'));

                if ($crp_data->entry_date < $part_warranty_end_date) {
                    $warranty_status = 1; // Yes
                    $data['purchase_date'] = $crp_data->bill_date;
                    $data['booking_date'] = $crp_data->entry_date;
                    $data['warranty_end_date'] = date('d-m-Y', strtotime($part_warranty_end_date));
                    $data['warranty_status'] = $warranty_status;
                    $data['warranty_period'] = $get_parts_warranty->warranty_period;
                }
            }
        }
    } else {
        // Handle cases with only a parts warranty
        $get_parts_warranty = \App\Models\ProductWarranty::where('goods_id', $product_id)
            ->where('dealer_type', $crp_data->dealer_type)
            ->where('warranty_type', 'parts')
            ->where('spear_id', $sp_id)
            ->first();

        if ($get_parts_warranty) {
            $part_warranty_end_date = date('Y-m-d', strtotime($crp_data->bill_date . ' + ' . $get_parts_warranty->warranty_period . ' months'));
            $part_warranty_end_date = date('Y-m-d', strtotime($part_warranty_end_date . ' -1 days'));

            if ($crp_data->entry_date < $part_warranty_end_date) {
                $warranty_status = 1; // Yes
                $data['purchase_date'] = $crp_data->bill_date;
                $data['booking_date'] = $crp_data->entry_date;
                $data['warranty_end_date'] = date('d-m-Y', strtotime($part_warranty_end_date));
                $data['warranty_status'] = $warranty_status;
                $data['warranty_period'] = $get_parts_warranty->warranty_period;
            }
        }
    }

    return $data;
}
