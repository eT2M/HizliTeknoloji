

<h1 align="center">Hızlı Bilişim E Fatura E Arşiv</h1>

[![GitHub issues](https://img.shields.io/github/issues/eT2M/HizliTeknoloji)](https://github.com/eT2M/HizliTeknoloji/issues)
[![GitHub forks](https://img.shields.io/github/forks/eT2M/HizliTeknoloji)](https://github.com/eT2M/HizliTeknoloji/network)
[![GitHub stars](https://img.shields.io/github/stars/eT2M/HizliTeknoloji)](https://github.com/eT2M/HizliTeknoloji/stargazers)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/eT2m/HizliTeknoloji.svg?style=flat-square)](https://packagist.org/packages/eT2M/HizliTeknoloji)
[![Total Downloads](https://img.shields.io/packagist/dt/eT2M/HizliTeknoloji.svg?style=flat-square)](https://packagist.org/packages/eT2M/HizliTeknoloji)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Hızlı Bilişim E Fatura E Arşiv E Dönüşüm için Yazılmış Modüler Php Composer Paketidir.

 - Laravel 7,8,9 Tam Uyumlu (Providers Gerek yok) 
 - Lumen API ve OOP için Tam Uyumlu 
 - Symfony İçin Tam Uyumlu Pakettir.

# Kurulumu :

    composer require et2m/hizliteknoloji

# Kullanımı :

    <?php

    use eT2M\HizliTeknoloji\HizliTeknoloji;

    /// Bu Bilgiler Veritabanından veya İhtiyacınıza Göre Çekersiniz...
    $ayar = array(
        'kadi' => 'kadi',  // Hızlı Bilişim WS Kullanıcı Adınız 
        'sifre' => 'sifre', // Hızlı Bilişim WS Kullanıcı Şifreniz 
        'SourceUrn' => 'defaultgb@alanadiniz.com.tr', // Hızlı Bilişim Gönderici Etiketi
        'supplier' => array(
            'supplierParty' => array(
                'CityName' => 'ANTALYA', 
                'CitySubdivisionName' => 'MANAVGAT',
                'CountryName' => 'TÜRKİYE',
                'ElectronicMail' => 'bilgi@alanadi.com.tr',
                'IdentificationID' => 'vergino',   // Şireket Vergi No
                'PartyName' => 'Şirket Ünvanı',
                'Person_FamilyName' => 'Yetkili Soyad',
                'Person_FirstName' => 'Yetkili Ad',
                'PostalZone' => NULL,
                'StreetName' => 'Şirket Adresi',
                'TaxSchemeName' => 'Vergi Dairesi',
                'Telefax' => 'Kurumsal Fax',
                'Telephone' => 'Kurumsal Telefon',
                'WebsiteURI' => 'wwww.alanadi.com.tr',
                'customerIdentificationsOther' => NULL
            )
        ),
        "paymentMeans" => null,
        'EfatSeri' => "XXF", // Fatura Seri Noları
        'EArsSeri' => "XXA",
        'abn_tipi' => null,
    );
    $fatura = new HizliTeknoloji($ayar);



    /// Fatura Oluşturma
    //Fatura Kesilecek Kişinin veya Kurumun Tanımı
    $Musteri = [
        'abn_tipi' => null,
        'unvan' => null,
        'isim' => 'Müşteri İsmi',
        'soyisim' => 'Müşteri Soyadı',
        'vergino_tcno' => 'Müşteri TC',
        'vergidaire' => 'Manavgat',
        'il' => 'Antalya',
        'ilce' => "Manavgat",
        'email' => "mehmet@alanadi.com.tr",
        'pk' => "07600",
        'adres' => "Müşteri Adresi",
        'telefon' => "Müşteri Telefon",
        'aboneno' => "Abone No veya ID", // Bu Ek Bilgi Her faturada Olmayabilir.


    ];

    /// Mukellefi Kontrol Etme. Eğer IsSucceeded true ise E Fatura Mükellefi,  false ise E Arşiv Faturası Kesilir.
    $mukkelefsor = $fatura->mukellefKontrol($Musteri['vergino_tcno']);

    // E Arşiv E Fatura Ayrımı Ayrıca E Arşive Dahil ama  E Fatura Kullanmıyan Ticari Şirket yada Şahıs Şirketi Ayrımı

    if ($mukkelefsor->IsSucceeded) {
        $fBil = $mukkelefsor->gibUserLists[0];
        $faturaTipi = 1;
        $DestinationUrn = $fBil->Alias;
        $DestinationIdentifier = $fBil->Identifier;
        $Unvan = $fBil->Title;
        $isim = null;
        $soyisim = null;
        $OnseriNo = $ayar['EfatSeri'];
        $seriNo = $OnseriNo . date('Y');
        $faturaNosu = str_replace("$OnseriNo", "", $fatura->sonFatura($seriNo, $faturaTipi)->InvoiceId) + 1;
        $faturaNo = $OnseriNo . $faturaNosu;
        $fatSenTip = "TEMELFATURA";
    } else {
        $faturaTipi = 3;
        $DestinationUrn = null;
        $DestinationIdentifier = $Musteri['vergino_tcno'];
        $Unvan = $Musteri['abn_tipi'] == 'kurumsal' ? $Musteri['unvan'] : $Musteri['isim'] . ' ' . $Musteri['soyisim'];
        $isim = $Musteri['abn_tipi'] == 'kurumsal' ? $Musteri['unvan'] : $Musteri['isim'];
        $soyisim = $Musteri['abn_tipi'] == 'kurumsal' ? '.' : $Musteri['soyisim'];
        $OnseriNo = $ayar['EArsSeri'];;
        $seriNo = $OnseriNo . date('Y');
        $faturaNosu = str_replace("$OnseriNo", "", $fatura->sonFatura($seriNo, $faturaTipi)->InvoiceId) + 1;
        $faturaNo = $OnseriNo . $faturaNosu;
        $fatSenTip = "EARSIVFATURA";
    }

    /// Yukarda Bilgileri Toparladıktan Sonra Burda Faturayı Oluşturuyoruz.
    /// Bu Sistemin Diğerlerinden Farkı Faturayı Tamamen Costumize Edebiliyor Olmanızdır.


    $faturaverileri = array(
        "AppType" => $faturaTipi, // 1 : e-Fatura,3 :e-Arşiv
        "IsDraft" => false, // Taslak Mı
        "IsDraftSend" => false, // Fatura verileri doldurulmalı
        "IsPreview" => false, // Gibe göndermeden sadece html görüntüsü almak için     
        "UpdateDocument" => false, // Hatalı dokumanları yeniden gönderebilmek için "true" yapılarak kullanılabilir.
        "DestinationUrn" => $DestinationUrn, // Alıcı PK Adresi
        "DestinationIdentifier" => "$DestinationIdentifier", // Alıcı Vergi Kimlik No
        "faturaNo" => $faturaNo,
        "Unvan" => $Unvan,
        "fatTAR" => date("Y-m-d"), // Burası fatura Oluşturma Tarihi Bekleyen Faturalar İçin VT dende Alınabilir.
        "customer" => array(
            'CityName' => strtoupper($Musteri['il']),
            'CitySubdivisionName' => strtoupper($Musteri['ilce']),
            'CountryName' => 'TÜRKİYE',
            'ElectronicMail' => $Musteri['email'],
            'IdentificationID' => "$DestinationIdentifier",
            'PartyName' => $Unvan,
            'Person_FamilyName' => $soyisim,
            'Person_FirstName' => $isim,
            'PostalZone' => $Musteri['pk'],
            'StreetName' => strtoupper($Musteri['adres']),
            'TaxSchemeName' => $Musteri['vergidaire'] ? $Musteri['vergidaire'] : $Musteri['ilce'],
            'Telefax' => $Musteri['telefon'],
            'Telephone' => $Musteri['telefon'],
            'WebsiteURI' => '',
            'customerIdentificationsOther' => array(
                array(
                    'SchemeID' => 'ABONENO',
                    'Value' => $Musteri['aboneno'],
                )

            ),
        ),
        "faturaGenel" => array(
            'AllowanceTotalAmount' => 0,
            'CalculationRate' => 0, // Dolar Kuru Burdan Ayarlanmakta veya $fatura->kurcek('USD');  kullanılabilir.
            'DocumentCurrencyCode' => 'TRY', // Döviz Tipi Burdan Belirlenmekte /TRY - USD
            'InvoiceTypeCode' => 'SATIS',
            'Invoice_ID' => $faturaNo,
            'IsInternetSale' => false,
            'IsInternet_ActualDespatchDate' => NULL,
            'IsInternet_Delivery_FamilyName' => NULL,
            'IsInternet_Delivery_FirstName' => NULL,
            'IsInternet_Delivery_PartyName' => NULL,
            'IsInternet_Delivery_TcknVkn' => NULL,
            'IsInternet_InstructionNote' => NULL,
            'IsInternet_PaymentDueDate' => NULL,
            'IsInternet_PaymentMeansCode' => NULL,
            'IssueDate' => date("Y-m-d"), // Burası fatura Oluşturma Tarihi Bekleyen Faturalar İçin VT dende Alınabilir.
            'IssueTime' => date("H:i:s"), // Burası fatura Oluşturma Saati Bekleyen Faturalar İçin VT dende Alınabilir.
            'LineExtensionAmount' => 7.2, /// Toplam Ham Fiyatı
            'Note' => 'Bu Fatura Nakit Ödeme Şekli İle Ödenmiştir.',
            'Notes' => null, /* array(
                array('Note' => 'DENEME NOT 1'),
                array('Note' => 'DENEME NOT 2'),
            ), */
            'OrderReferenceDate' => date("Y-m-d"),
            'OrderReferenceId' => 'Y' . $faturaNosu . 'N',
            'PayableAmount' => 10,
            'ProfileID' => $fatSenTip,
            'Sgk_AccountingCost' => NULL,
            'Sgk_DosyaNo' => NULL,
            'Sgk_Mukellef_Adi' => NULL,
            'Sgk_Mukellef_Kodu' => NULL,
            'Sgk_Period_EndDate' => NULL,
            'Sgk_Period_StartDate' => NULL,
            'TaxInclusiveAmount' => 10,
            'UUID' => $fatura->getUUID(),
            'XSLT_Adi' => 'general',
            'XSLT_Doc' => NULL,
        ),
        "faturaSatir" => array([
            'Allowance_Amount' => 0,
            'Allowance_Percent' => 0,
            'Allowance_Reason' => NULL,
            'ID' => 1,                     // For veya Foreach da indis den yaralanılabilir. 1 den başlayıp artan sayıdır.
            'Item_Brand' => NULL,
            'Item_Classification' => NULL,
            'Item_Description' => NULL,
            'Item_ID_Buyer' => NULL,
            'Item_ID_Seller' => NULL,
            'Item_Model' => NULL,
            'Item_Name' => 'İnternet Hizmet Bedeli Test Fatura',
            'LineCurrencyCode' => NULL,
            'LineNote' => NULL,
            'Manufacturers_ItemIdentification' => NULL,
            'Price_Amount' => number_format(7.2, 2), // Toplamın Ham Tutarı
            'Price_Total' => number_format(10 / 1, 2), // Toplamın Adet Kadar Parçası
            'Quantity_Amount' => 1, // Adet
            'Quantity_Unit_User' => 'C62',
            'exportLine' => NULL,
            'lineTaxes' => array(   /// Vergileri Gönderdiğimiz Yerdir.
                array(
                    'Tax_Amnt' => number_format(1.8, 2), // KDV Miktarı
                    'Tax_Base' => number_format(7.2, 2), // KDV Hariç Ham Tutar
                    'Tax_Code' => '0015',
                    'Tax_Exem' => '',
                    'Tax_Exem_Code' => '',
                    'Tax_Name' => 'KDV',
                    'Tax_Perc' => 18,
                )/* ,
                array(     /// Diğer Vergi Türleri Array Şeklinde Alt Alta Tanımlayarak Geçebilirsiniz...
                    'Tax_Amnt' => number_format(0.8, 2),
                    'Tax_Base' => $kl['oiv']?number_format(7.2, 2):null,
                    'Tax_Code' => '4081',
                    'Tax_Exem' => '',
                    'Tax_Exem_Code' => '',
                    'Tax_Name' => 'ÖİV',
                    'Tax_Perc' => 10,
                ), */
            ),
        ]),
        "despatchs" => array(
            array(
                'DespatchDocumentID' => 'Y' . $faturaNosu . 'D',
                'DespatchDocumentIssueDate' => date("Y-m-d"),
            ),
        ),
    );

    $fatYolla = $fatura->efatura_olustur($faturaverileri); // Faturayı Gönderir...
    $fatYol = json_decode($fatYolla); // Php Kullanmak İçin array Şekline Getirir.

    if ($fatYol[0]->IsSucceeded == 1) {
        /// Fatura Kesimi Olumlu Olunca Yapılacak İşlemler
        return $fatYol;
    } else {
        //Olumsuz Durumda Yapılacak İşlemler
        return $fatYol;
    }

    /*  Gelen Örnek Başarılı Sonuc
     {
    "HtmlContent": null,
    "IsSucceeded": true,
    "Message": "(DocumentUUID: fb9ac150-d556-e3fa-d453-c79dc7fd819e) Başarılı"
  }
    */

    /* ------------------------------------------------ */
    //Son Faturayı Getirme
    $FaturaKontrol = $fatura->sonFatura('XXA2022', 3); //1 : e-Fatura,3 :e-Arşiv ,5:Giden e-İrsaliye,6:Giden e-Serbest Meslek Makbuzu,7:Giden e-Müstahsil Makbuzu,8:Giden İrsaliye Yanıtı

    /* /// Sorgudan Gelen Cevap
    {
        "InvoiceId": "XXA2022000000004",
        "InvoiceDate": "2022-08-30T00:00:00",
        "IsSucceeded": true,
        "Message": "Başarılı"
    }
    */

    return $FaturaKontrol;

    /* ------------------------------------------------ */

    /* ------------------------------------------------ */
    //Fatura Kontrol Etme
    $FaturaKontrol = $fatura->efatura_pdf('fb9ac150-d556-e3fa-d453-c79dc7fd819e'); // efatura için sonuna 2 ekleyiniz

    /* 
    {
        // DocumentFile yi pdf mime sine göre gösteriniz
    }
    */

    return $FaturaKontrol;

    /* ------------------------------------------------ */

    /* ------------------------------------------------ */
    //Faturaları Kontrol Etme
    $FaturaKontrol = $fatura->faturakontrol();

    /* 
    {
        // Buraya Buraya 15 Günlük fatular Gelir...
    }
    */

    return $FaturaKontrol;

    /* ------------------------------------------------ */
    //Mükellef Listeleme
    $TamMukellefListesi = $fatura->mukellefListe();

    /* 
    {
        // Buraya GİB deki Mükellef Listesi Dökülür...
    }
    */

    return $TamMukellefListesi;

    /* -------------------------------------------------------- */

    // Kontör Sorgulama 
    $faturaKontorGoster = $fatura->kontor();

    /* Fatura Kontör Sorgulama Gelen Başarılı Sonuç
    {
        "totalCredit": 10000.00,
        "remainCredit": 9770.00,
        "IsSucceeded": true,
        "Message": null
    }
    */

    return $faturaKontorGoster;

<p align="center">
  <img width="460" height="166" src="https://img.et2m.com/logo_kirmizi.png">
</p>
<p align="center">
    Bu proje, MIT lisansı altında lisanslanmış açık kaynaklı bir yazılımdır .
</p>


