<?php

namespace eT2M\HizliTeknoloji;

class HizliTeknoloji
{
    private $kadi       = '';
    private $sifre      = '';
    private $url        = 'https://econnect.hizliteknoloji.com.tr/HizliApi/RestApi/';
    private $supplier    = [];
    private $paymentMeans    = [];
    private $IdentificationID='';
    private $SourceUrn = '';

    public function __construct($firmaBilgileri)
    {
        $this->kadi     = $firmaBilgileri['kadi'];
        $this->sifre    = $firmaBilgileri['sifre'];
        $this->url      = @$firmaBilgileri['url']?$firmaBilgileri['url']:$this->url;
        $this->supplier = $firmaBilgileri['supplier'];
        $this->SourceUrn = $firmaBilgileri['SourceUrn'];
        $this->paymentMeans = $firmaBilgileri['paymentMeans'];
        $this->IdentificationID = $firmaBilgileri['supplier']['supplierParty']['IdentificationID'];

    }

    public function efatura_olustur($v)
    {
        $LocalId = rand(1, 9999); // Sistem fatura id'si
        $AppType = $v['AppType']; // 1 : e-Fatura,3 :e-Arşiv
        $IsDraft = $v['IsDraft']; // Taslak Mı
        $IsDraftSend = $v['IsDraftSend']; // Fatura verileri doldurulmalı
        $IsPreview = $v['IsPreview']; // Gibe göndermeden sadece html görüntüsü almak için
        $SourceUrn = $this->SourceUrn; //$v['SourceUrn']; // Satıcı GB Adresi        
        $UpdateDocument = $v['UpdateDocument']; // Hatalı dokumanları yeniden gönderebilmek için "true" yapılarak kullanılabilir.
        $DestinationUrn = $v['DestinationUrn']; // Alıcı PK Adresi
        $DestinationIdentifier = $v['DestinationIdentifier']; // Alıcı Vergi Kimlik No
        $UUID = $this->getUUID();

        // seri(3 harften oluşan değer) + yıl + fatura numarası
        // Toplamda 16 karakter olacak
        $Invoice_ID = $v['faturaNo'];

        /**
         * InvoiceModel
         * 
         * Fatura Detaylari
         * */

        // Urunlerin Listesi
        $invoiceLines = $v['faturaSatir']; 
         /* array(
            array(
                'Allowance_Amount' => 0,
                'Allowance_Percent' => 0,
                'Allowance_Reason' => NULL,
                'ID' => 1,
                'Item_Brand' => NULL,
                'Item_Classification' => NULL,
                'Item_Description' => NULL,
                'Item_ID_Buyer' => NULL,
                'Item_ID_Seller' => NULL,
                'Item_Model' => NULL,
                'Item_Name' => 'Iphone 12',
                'LineCurrencyCode' => NULL,
                'LineNote' => NULL,
                'Manufacturers_ItemIdentification' => NULL,
                'Price_Amount' => 1,
                'Price_Total' => 1,
                'Quantity_Amount' => 1,
                'Quantity_Unit_User' => 'C62',
                'exportLine' => NULL,
                'lineTaxes' =>
                array(
                    array(
                        'Tax_Amnt' => 1.8,
                        'Tax_Base' => 10,
                        'Tax_Code' => '0015',
                        'Tax_Exem' => '',
                        'Tax_Exem_Code' => '',
                        'Tax_Name' => 'KDV',
                        'Tax_Perc' => 18,
                    ),
                    array(
                        'Tax_Amnt' => 1.8,
                        'Tax_Base' => 10,
                        'Tax_Code' => '4081',
                        'Tax_Exem' => '',
                        'Tax_Exem_Code' => '',
                        'Tax_Name' => 'ÖİV',
                        'Tax_Perc' => 10,
                    ),
                ),
            ),
            array(
                'Allowance_Amount' => 0,
                'Allowance_Percent' => 0,
                'Allowance_Reason' => NULL,
                'ID' => 2,
                'Item_Brand' => NULL,
                'Item_Classification' => NULL,
                'Item_Description' => NULL,
                'Item_ID_Buyer' => NULL,
                'Item_ID_Seller' => NULL,
                'Item_Model' => NULL,
                'Item_Name' => 'Iphone 12',
                'LineCurrencyCode' => NULL,
                'LineNote' => NULL,
                'Manufacturers_ItemIdentification' => NULL,
                'Price_Amount' => 1,
                'Price_Total' => 1,
                'Quantity_Amount' => 1,
                'Quantity_Unit_User' => 'C62',
                'exportLine' => NULL,
                'lineTaxes' =>
                array(
                    array(
                        'Tax_Amnt' => 1.8,
                        'Tax_Base' => 10,
                        'Tax_Code' => '0015',
                        'Tax_Exem' => '',
                        'Tax_Exem_Code' => '',
                        'Tax_Name' => 'KDV',
                        'Tax_Perc' => 18,
                    ),
                ),
            ),
        ); */

        // Fatura Detayları
        $invoiceheader = $v['faturaGenel'];
        /* array(
            'AllowanceTotalAmount' => 10,
            'CalculationRate' => 1,
            'DocumentCurrencyCode' => 'TRY',
            'InvoiceTypeCode' => 'SATIS',
            'Invoice_ID' => $Invoice_ID,
            'IsInternetSale' => false,
            'IsInternet_ActualDespatchDate' => NULL,
            'IsInternet_Delivery_FamilyName' => NULL,
            'IsInternet_Delivery_FirstName' => NULL,
            'IsInternet_Delivery_PartyName' => NULL,
            'IsInternet_Delivery_TcknVkn' => NULL,
            'IsInternet_InstructionNote' => NULL,
            'IsInternet_PaymentDueDate' => NULL,
            'IsInternet_PaymentMeansCode' => NULL,
            'IssueDate' => '2021-11-20',
            'IssueTime' => '00:00:00',
            'LineExtensionAmount' => 10,
            'Note' => 'DENEM NOT',
            'Notes' => array(
                array(
                    'Note' => 'DENEME NOT 1',
                ),
                array(
                    'Note' => 'DENEME NOT 2',
                ),
            ),
            'OrderReferenceDate' => '2021-11-20',
            'OrderReferenceId' => 'SIPARİŞNO',
            'PayableAmount' => 90.2,
            'ProfileID' => $v['AppType'],
            'Sgk_AccountingCost' => NULL,
            'Sgk_DosyaNo' => NULL,
            'Sgk_Mukellef_Adi' => NULL,
            'Sgk_Mukellef_Kodu' => NULL,
            'Sgk_Period_EndDate' => NULL,
            'Sgk_Period_StartDate' => NULL,
            'TaxInclusiveAmount' => 90,
            'UUID' => $UUID,
            'XSLT_Adi' => 'general',
            'XSLT_Doc' => NULL,
        ); */


        // Alıcı Bilgileri
        $customer = $v['customer'];
        //$v['customer']; // $v['customer'];
        // Ödeme hakkında bilgi sağlayan bir grup ticari şart.
        $paymentMeans = $this->paymentMeans;
        // Satıcı Bilgileri
        // Satıcı Şube Bilgileri
        
        // İrsaliyeler
        $despatchs = $v['despatchs'];

        /**
         * Faturada yapılan talepleri doğrulayan ek destekleyici belgeler hakkında bilgi sağlayan bir grup iş şartları.
         * Ek destekleyici belgeler, hem alıcı tarafından bilinmesi beklenen bir belge numarasına,
         * harici bir belgeye (bir URL ile başvurulur) hem de Base64 kodlu gömülü bir
         * belge olarak (bir zaman raporu gibi) referans olarak kullanılabilir.
         * */
        $additionalDocumentReferences =null; /* array(
            array(
                'DocumentType' => 'FATURAKODLIST',
                'DocumentTypeCode' => '',
                'ID' => '101.00001.0000010019',
                'IssueDate' => '2014-02-18',
            ),
        ); */


        // Verilerin toparlandigi degisken
        $post_fields = array(
            array(
                'AppType' => $AppType,
                'LocalId' => $LocalId,
                'IsDraft' => $IsDraft,
                'IsPreview' => $IsPreview,
                'SourceUrn' => $SourceUrn,
                'IsDraftSend' => $IsDraftSend,
                'UpdateDocument' => $UpdateDocument,
                'DestinationUrn' => $DestinationUrn,
                'DestinationIdentifier' => $DestinationIdentifier,
                'InvoiceModel' => array(
                    'additionalDocumentReferences' => $additionalDocumentReferences,
                    'customer' => $customer,
                    'customerAgent' => @$customerAgent,
                    'despatchs' => $despatchs,
                    'invoiceLines' => $invoiceLines,
                    'invoiceheader' => $invoiceheader,
                    'paymentMeans' => $paymentMeans
                ),
            ),
        );
        //return $post_fields; die();

        /**
         * Start Curl Operation
         * 
         * */
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . "SendInvoiceModel/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($post_fields),
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "password: $this->sifre",
                "username: $this->kadi"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    public function efatura_pdf($UUID = "", $fatTip=3)
    {
        // 1:Gelen e-Fatura,2:Giden e-Fatura,3:Giden e-Arşiv Fatura,
        // 4:Gelen e-İrsaliye,5:Giden e-İrsaliye,6:Giden e-Serbest Meslek Makbuzu,
        // 7:Giden e-Müstahsil Makbuzu,8:Giden İrsaliye Yanıtı,9:Gelen İrsaliye Yanıtı
        $AppType = $fatTip;

        // PDF,XML,HTML
        $Tur = "PDF";

        // AppType=2 giden taslak , AppType=3 giden earşiv taslak
        $IsDraft = false;

        $getParams = "?AppType=$AppType&Uuid=$UUID&Tur=$Tur&IsDraft=$IsDraft";
        $methodType = "GetDocumentFile";

        /**
         * Start Curl Operation
         * 
         * */
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . "$methodType/$getParams",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "password: $this->sifre",
                "username: $this->kadi"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response);
        }
    }

    public function getUUID()
    {
        mt_srand((double) microtime() * 10000);$charid = md5(uniqid(rand(), true));$hyphen = chr(45);
        $bsno = substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12);
        return $bsno;
    }

    public function getInvoiceId($LocalId = 0)
    {
        $LocalString = $LocalId;
        $expectedLength = 9;

        $length = strlen((string)$LocalId);

        if ($length < $expectedLength) {
            for ($i = $expectedLength - $length; $i > 0; $i--) {
                $LocalString = "0" . $LocalString;
            }
        } else if ($length > $expectedLength) {
            $LocalString = substr($LocalString, $length - $expectedLength, $expectedLength);
        }

        return "BDD" . date('Y') . $LocalString;
    }

    public function mukellefKontrol($vNoveyaTCKN){
       
        $AppType = 1;
        $Tip = "PK";

        $getParams = "?AppType=$AppType&Type=$Tip&Identifier=$vNoveyaTCKN";
        $methodType = "GetGibUserList";

        /**
         * Start Curl Operation
         * 
         * */
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . "$methodType/$getParams",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "password: $this->sifre",
                "username: $this->kadi"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response);
        }
    }
    public function sonFatura($seri,$faturatipi){
       
        $AppType = $faturatipi; //1 : e-Fatura,3 :e-Arşiv ,5:Giden e-İrsaliye,6:Giden e-Serbest Meslek Makbuzu,7:Giden e-Müstahsil Makbuzu,8:Giden İrsaliye Yanıtı
        $Seri = $seri;

        $getParams = "?AppType=$AppType&Seri=$Seri";
        $methodType = "GetLastInvoiceIdAndDate";

        /**
         * Start Curl Operation
         * 
         * */
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . "$methodType/$getParams",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "password: $this->sifre",
                "username: $this->kadi"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response);
        }
    }
    public function faturakontrol(){
       
        $AppType = 3;
        $DateType = "IssueDate";
        $EndDate = date('Y-m-d'); //"2021-10-01";
        $StartDate = date('Y-m-d', strtotime($EndDate . ' -15 days')); //"2021-11-01";
        $IsNew = 0;
        $IsExport = false;
        $TakenFromEntegrator = "ALL";
        $IsDraft = false;

        $getParams = "?AppType=$AppType&DateType=$DateType&StartDate=$StartDate&EndDate=$EndDate&IsNew=false&IsExport=false&TakenFromEntegrator=$TakenFromEntegrator&IsDraft=false";
        $methodType = "GetDocumentList";

        /**
         * Start Curl Operation
         * 
         * */
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . "$methodType/$getParams",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "password: $this->sifre",
                "username: $this->kadi"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response);
        }
    }

    public function mukellefListe(){
       
        $AppType = 0;
        $Tip = "PK_Yeni_Format";

        $getParams = "?AppType=$AppType&Type=$Tip";
        $methodType = "GetGibUserFile";

        /**
         * Start Curl Operation
         * 
         * */
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . "$methodType/$getParams",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 180,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "password: $this->sifre",
                "username: $this->kadi"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        /* if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response);
        } */
        return $response;
    }

    public function kontor(){
        $AppType = 1;
        $Tip = "PK";

        $vNoveyaTCKN= $this->IdentificationID;
        $getParams = "?vkn_tckn=$vNoveyaTCKN";
        $methodType = "GetCustomerCreditCount";

        /**
         * Start Curl Operation
         * 
         * */
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . "$methodType/$getParams",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "password: $this->sifre",
                "username: $this->kadi"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }


    // UUID icin efatura pdf almak icin
    //$efatura = efatura_pdf('7ca3786e-4071-4fba-b14c-b5574eaa9381');
    /* $data = base64_decode($efatura->DocumentFile); */
    /* file_put_contents('file.pdf', $data); */


    // yenibir e-fatura yada e-arsiv olursturmak icin
    // echo efatura_olustur();

    public function kurcek($birim)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.tcmb.gov.tr/kurlar/today.xml",
            CURLOPT_HEADER=> 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION=> true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET"
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            $ilkdeger = json_encode(simplexml_load_string($response));
            $duzeltme = json_decode($ilkdeger,true);
            $birimlerdahil = $duzeltme['Currency'];
            foreach($birimlerdahil as $k) {
                if($k['@attributes']['Kod']==$birim){
                    return $k['BanknoteSelling']; 
                    exit();
                }
                
            }


        }

    }

    public function test()
    {
        return $this;
    }

}