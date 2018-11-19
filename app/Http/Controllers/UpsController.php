<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UpsController extends Controller
{

    public $accessKey = "9D4D236CE6545998";
    public $userId = "festfriends";
    public $password = "Coachella18";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function address_validation()
    {

        //Reference Site
       // https://packagist.org/packages/gabrielbull/ups-api#user-content-addressvalidation-class


        $address = new \Ups\Entity\Address();
        $address->setAttentionName('Test Test');
        $address->setBuildingName('Test');
        $address->setAddressLine1('Address Line 1');
        $address->setAddressLine2('Address Line 2');
        $address->setAddressLine3('Address Line 3');
        $address->setStateProvinceCode('NY');
        $address->setCity('New York');
        $address->setCountryCode('US');
        $address->setPostalCode('10000');

        $xav = new \Ups\AddressValidation($this->accessKey, $this->userId, $this->password);
        $xav->activateReturnObjectOnValidate(); //This is optional
        try {
            $response = $xav->validate($address, $requestOption = \Ups\AddressValidation::REQUEST_OPTION_ADDRESS_VALIDATION, $maxSuggestion = 15);

            var_dump($response);

            if ($response->noCandidates()) {
                //Do something clever and helpful to let the use know the address is invalid
                echo "<br>Invalid Address";
            }
            if ($response->isAmbiguous()) {
                $candidateAddresses = $response->getCandidateAddressList();
                foreach($candidateAddresses as $address) {
                    //Present user with list of candidate addresses so they can pick the correct one     
                     var_dump($address);   
                }
            }
            if ($response->isValid()) {
                $validAddress = $response->getValidatedAddress();
                
                //Show user validated address or update their address with the 'official' address
                //Or do something else helpful...
                var_dump($validAddress);
            }

        } catch (Exception $e) {
            var_dump($e);
        }
    }

    public function simpleAddressValidation()
    {

        $address = new \Ups\Entity\Address();
        $address->setStateProvinceCode('NY');
        $address->setCity('New York');
        $address->setCountryCode('US');
        $address->setPostalCode('10000');

        $av = new \Ups\SimpleAddressValidation($this->accessKey, $this->userId, $this->password);
        try {
         $response = $av->validate($address);
         var_dump($response);
        } catch (Exception $e) {
         var_dump($e);
        }
    }

    public function shipmentStatusByTrackNumber()
    {

      //Demo Tracking Number : 
      $tracking_number = "1Z433ER59034028059";  
       //1Z204E380338943508,1Z51062E6893884735,1ZXF38300382722839


      $tracking = new \Ups\Tracking($this->accessKey, $this->userId, $this->password);

        try {
            $shipment = $tracking->track($tracking_number);

            foreach($shipment->Package->Activity as $activity) {
                var_dump($activity);
            }

        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function upsRate()
    {
        $rate = new \Ups\Rate(
            $this->accessKey,
            $this->userId,
            $this->password
        );

        try {
            $shipment = new \Ups\Entity\Shipment();

            $shipperAddress = $shipment->getShipper()->getAddress();
            $shipperAddress->setPostalCode('99205');

            $address = new \Ups\Entity\Address();
            $address->setPostalCode('99205');
            $shipFrom = new \Ups\Entity\ShipFrom();
            $shipFrom->setAddress($address);

            $shipment->setShipFrom($shipFrom);

            $shipTo = $shipment->getShipTo();
            $shipTo->setCompanyName('Test Ship To');
            $shipToAddress = $shipTo->getAddress();
            $shipToAddress->setPostalCode('99205');

            $package = new \Ups\Entity\Package();
            $package->getPackagingType()->setCode(\Ups\Entity\PackagingType::PT_PACKAGE);
            $package->getPackageWeight()->setWeight(10);
            
            // if you need this (depends of the shipper country)
            $weightUnit = new \Ups\Entity\UnitOfMeasurement;
            $weightUnit->setCode(\Ups\Entity\UnitOfMeasurement::UOM_KGS);
            $package->getPackageWeight()->setUnitOfMeasurement($weightUnit);

            $dimensions = new \Ups\Entity\Dimensions();
            $dimensions->setHeight(10);
            $dimensions->setWidth(10);
            $dimensions->setLength(10);

            $unit = new \Ups\Entity\UnitOfMeasurement;
            $unit->setCode(\Ups\Entity\UnitOfMeasurement::UOM_IN);

            $dimensions->setUnitOfMeasurement($unit);
            $package->setDimensions($dimensions);

            $shipment->addPackage($package);

            var_dump($rate->getRate($shipment));
        } catch (Exception $e) {
            var_dump($e);
        }
    }

   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createShipmentLabel()
    {

        //Reference : https://github.com/gabrielbull/php-ups-api/issues/113
       

       $return = true;

       $shipment = new \Ups\Entity\Shipment;

        // Set shipper
        $shipper = $shipment->getShipper();
        $shipper->setShipperNumber("433ER5");
        $shipper->setName("Cardknox");
        $shipper->setAttentionName("Cardknox");
        $shipperAddress = $shipper->getAddress();
        $shipperAddress->setAddressLine1('895 Towbin Ave');
        $shipperAddress->setAddressLine2('Suite A');
        $shipperAddress->setPostalCode('08701');
        $shipperAddress->setCity('Lakewood');
        $shipperAddress->setCountryCode('US');
        $shipperAddress->setStateProvinceCode('NJ');
        $shipper->setAddress($shipperAddress);
        $shipper->setEmailAddress('ah@g.com'); 
        $shipper->setPhoneNumber('2124736363');
        $shipment->setShipper($shipper);

        // To address
        $address = new \Ups\Entity\Address();
        $address->setAddressLine1('AlterKnit New York');
        $address->setAddressLine2('245 W');
        $address->setAddressLine3('29th St');
        $address->setPostalCode('10001');
        $address->setCity('New York');
        $address->setCountryCode('US');
        $address->setStateProvinceCode('NY');
        $shipTo = new \Ups\Entity\ShipTo();
        $shipTo->setAddress($address);
        $shipTo->setCompanyName('KNIT NY LLC');
        $shipTo->setAttentionName('AlterKnit New York');
        $shipTo->setEmailAddress('request@alterknitnewyork.com'); 
        $shipTo->setPhoneNumber('2124736363');
        $shipment->setShipTo($shipTo);

        // From address

        $address = new \Ups\Entity\Address();
        $address->setAddressLine1('CHRIS NISWANDEE, SMALLSYS INC');
        $address->setAddressLine2('795 E DRAGRAM');
        $address->setPostalCode('85705');
        $address->setCity('TUCSON');
        $address->setCountryCode('US');
        $address->setStateProvinceCode('AZ');
        $shipFrom = new \Ups\Entity\ShipFrom();
        $shipFrom->setAddress($address);
        $shipFrom->setName('Joseph Daniel');
        $shipFrom->setAttentionName($shipFrom->getName());
        $shipFrom->setCompanyName($shipFrom->getName());
        $shipFrom->setEmailAddress('ad@g.com');
        $shipFrom->setPhoneNumber('2124736363');
        $shipment->setShipFrom($shipFrom);

        // // Sold to
        $address = new \Ups\Entity\Address();
        $address->setAddressLine1('350 5th Avenue');
        $address->setPostalCode('10118');
        $address->setCity('New York');
        $address->setCountryCode('US');
        $address->setStateProvinceCode('NY');
        $soldTo = new \Ups\Entity\SoldTo;
        $soldTo->setAddress($address);
        $soldTo->setAttentionName('Joseph Daniel');
        $soldTo->setCompanyName($soldTo->getAttentionName());
        $soldTo->setEmailAddress('dfd@g.com');
        $soldTo->setPhoneNumber('2124736363');
        $shipment->setSoldTo($soldTo);

        // Set service
        $service = new \Ups\Entity\Service;
        $service->setCode("02");
        $service->setDescription($service->getName());
        $shipment->setService($service);

        // Mark as a return (if return)
        if ($return) {
            $returnService = new \Ups\Entity\ReturnService;
            $returnService->setCode(\Ups\Entity\ReturnService::PRINT_RETURN_LABEL_PRL);
            $shipment->setReturnService($returnService);
        }
      
        // Set description
        $shipment->setDescription('jkl');

        // Add Package
        $package = new \Ups\Entity\Package();
        $package->getPackagingType()->setCode(\Ups\Entity\PackagingType::PT_PACKAGE);
        $package->getPackageWeight()->setWeight(10);
        
        $unit = new \Ups\Entity\UnitOfMeasurement;
        $unit->setCode(\Ups\Entity\UnitOfMeasurement::UOM_LBS);
        $package->getPackageWeight()->setUnitOfMeasurement($unit);

        // Set dimensions
        $dimensions = new \Ups\Entity\Dimensions();
        $dimensions->setHeight('10');
        $dimensions->setWidth('10');
        $dimensions->setLength('10');
        $unit = new \Ups\Entity\UnitOfMeasurement;
        $unit->setCode(\Ups\Entity\UnitOfMeasurement::UOM_IN);
        $dimensions->setUnitOfMeasurement($unit);
        $package->setDimensions($dimensions); 

        // Add descriptions because it is a package
        $package->setDescription('test');
        $referenceNumber = new \Ups\Entity\ReferenceNumber;
        //$referenceNumber->setCode(\Ups\Entity\ReferenceNumber::CODE_INVOICE_NUMBER);
        $referenceNumber->setValue("54987");
        $package->setReferenceNumber($referenceNumber);

        // Add this package
        $shipment->addPackage($package);
        
            // Set Reference Number
            //$referenceNumber = new \Ups\Entity\ReferenceNumber;
            /* if ($return) {
                $referenceNumber->setCode(\Ups\Entity\ReferenceNumber::CODE_RETURN_AUTHORIZATION_NUMBER);
                $referenceNumber->setValue('123456');
            } else {
                $referenceNumber->setCode(\Ups\Entity\ReferenceNumber::CODE_INVOICE_NUMBER);
                $referenceNumber->setValue($order_id);
            } */
            //$shipment->setReferenceNumber($referenceNumber);
        // $shipment->getPackages()[0]->setReferenceNumber($referenceNumber);
         
         $shipment->getPackages()[0]->setReferenceNumber($referenceNumber);
         // Set payment information
        $shipment->setPaymentInformation(new \Ups\Entity\PaymentInformation('prepaid', (object)array('AccountNumber' => '433ER5')));

        // Ask for negotiated rates (optional)
        // $rateInformation = new \Ups\Entity\RateInformation;
        // $rateInformation->setNegotiatedRatesIndicator(1);
        // $shipment->setRateInformation($rateInformation);

        // Get shipment info
        
        try {
            $api = new \Ups\Shipping($this->accessKey, $this->userId, $this->password); 
            $confirm = $api->confirm('nonvalidate', $shipment);
            //echo "ress".'<br>'.$confirm;
            echo "<pre>";var_dump($confirm, true);echo "</pre>"; // Confirm holds the digest you need to accept the result
           
            if ($confirm) {
                
                $accept = $api->accept($confirm->ShipmentDigest);
                //echo "<pre>";var_dump($accept, true);echo "</pre>";die;
                $tracking_number = $accept->PackageResults->TrackingNumber;
                $imageformat = $accept->PackageResults->LabelImage->LabelImageFormat->Code;
                    
                $base64image = $accept->PackageResults->LabelImage->GraphicImage;
                $image = base64_decode($base64image);
                if($image){
                    $root_path=public_path();
                    $destination=$root_path."/doc/shipping-labels/Agent-Shipping.pdf";
                    $html ='<!DOCTYPE html>
                    <html>
                    <head>
                    <meta charset="utf-8">
                    <style>body{font-size: 16px;color: black;}</style>
                    <title>Agent</title>
                    </head>
                    <body>
                    <h2>Dear Tesr</h2>
                    <img src="data:image/gif;base64,'. $base64image . '" />
                    </body>
                    </html>';

                    echo $html;
                    // reference the Dompdf namespace
                    // $dompdf = new Dompdf();
                    // $dompdf->loadHtml($html);
                    // // (Optional) Setup the paper size and orientation
                    // $dompdf->setPaper('A4', 'portrait');
                    // // Render the HTML as PDF
                    // $dompdf->render();
                    // $pdf_gen = $dompdf->output();
                    
                    // //Save Pdf on server
                    // if(file_put_contents($destination, $pdf_gen)){
                    // echo 1;
                    // }else{
                    // echo 0;
                    // }



                }else{
                    echo 0;
                }
                exit;       
                echo "<pre>";var_dump($accept, true);echo "</pre>"; // Accept holds the label and additional information
            }
        } catch (\Exception $e) {
            echo "error".$e;die;
            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
