<?php
$filename = readline("Enter CSV filename: [ ENTER to use default ]");

// Default file
if(!$filename){
    $file = fopen('public/allsome_interview_test_orders.csv', 'r');
}else{
    $file = fopen(trim($filename), 'r');
}

//Error Handling
if (!$file) {
    echo json_encode(["error" => "Could not open file: {$filename}"]);
    exit(1);
}

//Remove Header
fgetcsv($file);

$totalRevenue = 0;
$skuArray = [];

// Loop to calculate Total Revenue
// Loop to summarise the date in the csv
while (($order = fgetcsv($file)) !== false) {
    $skuName = $order[1];
    $quantity = (int) $order[2];
    $price = (float) $order[3];

    $totalRevenue += ($quantity * $price);
    $skuArray[$skuName] = ($skuArray[$skuName] ?? 0) + $quantity;
}

fclose($file);

$bestSku = null;
$currentHighest = null;

// Loop the summarised data to get the most selling SKU
foreach ($skuArray as $sku => $value) {
    if ($currentHighest === null || $value > $currentHighest) {
        $currentHighest = $value;
        $bestSku = $sku;
    }
}

//Reponse in JSON Format
$response = [
    "total_revenue"   => $totalRevenue,
    "best_selling_sku" => [
        "sku" => $bestSku,
        "total_quantity" => $skuArray[$bestSku],
    ],
];

echo json_encode($response);
