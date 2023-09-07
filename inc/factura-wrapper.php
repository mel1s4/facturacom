<?php
require_once 'api-helper.php';
require_once 'commerce-helper.php';
require_once 'api-client.php';
require_once 'factura-config.php';

class FacturaWrapper{
  private static $error = array();
  private static $messages = array();

  /**
  * Getting wordpress shortcode
  *
  * @return String
  */
  static function form_shortcode(){
    $configEntity = FacturaWrapper::getConfigEntity();
    ob_start();
    include 'shortcode_form.php';
    $form = ob_get_clean();
    return $form;
  }

    /**
    * Saving configuration in .conf file
    *
    * @return Boolean
    */
    static function saveSettings($settings){
      // echo '<script>alert (" Ha respondido '. $settings.' respuestas afirmativas");</script>';
      return FacturaConfig::saveConf($settings);

    }

    /**
    * Getting settings entity
    *
    * @return Array
    */
    static function getConfigEntity(){
      return FacturaConfig::configEntity();
    }

    /**
    * Get Factura.com invoices via API
    *
    * @return Object
    */
    static function getInvoices(){

      $configEntity = self::getConfigEntity();
      $url     = $configEntity['apiurl'] . 'v3/cfdi40/list';
      $request = 'GET';

      return WrapperApi::callCurl($url, $request);
    }

    /**
    * Send invoice to customer via email
    *
    * @param Int $uid
    * @return Object
    */
    static function sendInvoiceEmail($uid){
      if(!isset($uid)){
        return array(
          'Error' => 'No se ha recibido el id de la factura.',
        );
      }

      $configEntity = self::getConfigEntity();

      $url     = $configEntity['apiurl'] . 'v3/cfdi40/' . $uid . '/email';
      $request = 'GET';

      return WrapperApi::callCurl($url, $request);
    }

    /**
    * Cancel invoice in Factura.com system
    *
    * @param Int $uid
    * @return Object
    */
    static function cancelInvoice($uid, $motivo, $folioSustituto){
      if(!isset($uid)){
        return array(
          'Error' => 'No se ha recibido el id de la factura.',
        );
      }

      $configEntity = self::getConfigEntity();

      $url = $configEntity['apiurl'] . 'v3/cfdi40/' . $uid . '/cancel';
      $data = ['motivo' => $motivo, 'folioSustituto' => $folioSustituto];
      $request = 'POST';

      return WrapperApi::callCurl($url, $request, $data);
    }

    /**
    * Get invoice by customer's RFC and order num
    *
    * @param String $rfc
    * @return Object
    */
    static function getInvoiceByOrder($rfc, $orderId){
      if(!isset($rfc)){
        return null;
      }

      if(!isset($orderId)){
        return null;
      }

      $configEntity = self::getConfigEntity();

      // $url = $configEntity['apiurl'] . 'invoices?rfc=' . $rfc . '&num_order=' . $orderId;
      $url     = $configEntity['apiurl'] . 'v3/cfdi40/list?rfc=' . $rfc;
      //$url = 'https://factura.com/api/v3/cfdi40/list' . '?rfc=' . $rfc;
      $request = 'GET';

      // $invoideData = WrapperApi::callCurl($url, $request)->data;
      $invoideData = WrapperApi::callCurl($url, $request);
      // $idata = (array)$invoideData->data;
              // var_dump((int)$idata[1]->NumOrder);
              // var_dump($orderId);

      foreach ($invoideData->data as $value) {
        $valor = (int)$value->NumOrder;
        // var_dump("valor".$valor);
        if($valor === $orderId){
          return $value->UID;
          break;
        }

      }


    }

    /**
    * Get customer data from Factura.com system
    *
    * @param String $rfc
    * @return Object
    */
    static function getCustomer($rfc){
      if(!isset($rfc)){
        return array(
          'Error' => 'No se ha recibido el rfc del cliente.',
        );
      }

      $configEntity = self::getConfigEntity();

      $url = $configEntity['apiurl'] . 'v1/clients/' . $rfc;
      // var_dump($url);
      $request = 'GET';

      return WrapperApi::callCurl($url, $request);
    }

    /**
    * Get order data from Woocommerce system
    *
    * @param Int $orderId
    * @return Array
    */
    static function getOrder($orderId){
      if(!isset($orderId)){
        return array(
          'Error' => 'No se ha recibido el id del pedido.',
        );
      }

      $order = CommerceHelper::getOrderById($orderId);

      if(gettype($order) != 'object'){
        return array('Error' => 'El pedido no existe');
      }
      return $order;
    }

    /**
    * Validate order
    *
    * @param Object $order
    * @param String $email
    * @param String $rfc
    * @return Array
    */
    static function validateOrder($order, $email, $rfc){
      // var_dump($order);
      //   self::changeOrderStatus('wc-invoiced', $order->id);
      // $order::update_status('wc-invoiced');
      //  var_dump($order);
      //validate order is set
      if(!isset($order)){
        return array(
          'Error' => true,
          'Message' => 'No se ha recibido el pedido.',
        );
      }

      //validate email given is set
      if(!isset($email)){
        return array(
          'Error' => true,
          'Message' => 'No se ha recibido el email del cliente.',
        );
      }

      //validate rfc given is set
      if(!isset($rfc)){
        return array(
          'Error' => true,
          'Message' => 'No se ha recibido el rfc del cliente.',
        );
      }

      //validate email given is of the order given
      // var_dump($order->billing_email);
      if($order->billing_email != $email){
        return array(
          'Error' => true,
          'Message' => 'El email ingresado no corresponde al email del pedido',
        );
      }

      //validate invoiced status
      if($order->status == "invoiced"){
        return array(
          'Error' => true,
          'Message' => 'Este pedido se encuentra facturado.',
          'Meta' => array(
            'code' => 101,
            'uid' => self::getInvoiceByOrder($rfc, $order->id),
          )
        );
      }

      //validate order completed status
      if($order->status != 'completed'){
        return array(
          'Error' => true,
          'Message' => 'Este pedido no se encuentra completado.',
        );
      }

      //validate order and dayoff to invoice
      if(self::validateDayOff($order->completed_at) == false){
        return array(
          'Error' => true,
          'Message' => 'Este pedido se encuentra fuera del periodo para facturación y ya no se puede facturar.',
        );
      }

      return true;

    }

    /**
    * Validate invoicing day off
    *
    * @param String $completed_date
    * @return Boolean
    */
    static function validateDayOff($completed_date){
      $order_month = date("m",strtotime($completed_date));
      $current_month = date("m");
      $current_day = date("d");

      $configEntity = self::getConfigEntity();

      if($order_month != $current_month){
        $order_day = date("d",strtotime($completed_date));

        if($order_month < $current_month){
          if($current_day > $configEntity['dayoff']){
            return false;
          }
        }
      }

      return true;
    }

    /**
    * Create cookies by name
    *
    * @param Object $customer
    * @param Object $order
    * @return void
    */
    static function saveCookies($name, $value){

      if(isset($value)){
        ApiHelper::saveCookie($name, $value);
      }

    }

    /**
    * Get cookies variables by name
    *
    * @param String $name
    */
    static function getCookies($name){
      $cookie = ApiHelper::getCookie($name);
      return $cookie;
    }

    /**
    * Delete cookies variables by name
    *
    * @param String $name
    */
    static function deleteCookies($name){
      ApiHelper::deleteCookies($name);
    }

    /*
    * Create invoice in factura.com system
    *
    * @param Array $data customer's data to save in factura.com system
    * @return Array
    *
    */
    static function generateInvoice($payment_data){
      $configEntity = self::getConfigEntity();
      // var_dump($configEntity);
      $url = $configEntity['apiurl'] . "v3/cfdi40/create";
      //$url = "https://factura.com/api/v3/cfdi40/create";

      $request = 'POST';

      $order = FacturaWrapper::getCookies('order');
      // var_dump($order);
      $customer = $_SESSION['customer'];

      $items = array();
      $discount = $order->cart_discount;
      $calculate_tax = 1.16;

      foreach($order->line_items as $item){
        $unidad = ($item["product_id"] == 31) ? "Servicio" : "Producto"; //No aplica
        // if(CommerceHelper::includeTax()){
        //     $product_price = ($item["subtotal"]/$item["quantity"]) / 1.16;
        // }else{
        //     $product_price = ($item["subtotal"]/$item["quantity"]);
        // }
        // $unit_price = $item["subtotal"]/$item["quantity"];
        // $product_price = $unit_price - ($unit_price * 0.16);
        // $product_price = ($item["subtotal"]/$item["quantity"]) / 1.16;

        // if($order->total_discount <= 0){
        //     $product_price = ($item["subtotal"]/$item["quantity"]);
        // }else{
        //     $product_price = ($item["total"]/$item["quantity"]);
        // }

        // $product_price = ($item["total"]/$item["quantity"]);
        // $product_price = $item["price"]/ 1.16;
        // $subtotal_item = ($item["price"] * $item["quantity"]) / 1.16;
        $product_price = ($item["subtotal"] / $item["quantity"]); // / $calculate_tax;

        /**
        * @TODO Configuración de IVA
        */

        $product_data = array(
          "cantidad"  => $item["quantity"],
          "unidad"    => $unidad,
          "concept"   => $item["name"],
          "precio"    => $product_price,
          "subtotal"  => $product_price * $item["quantity"], //$subtotal_item, //$item["subtotal"],
        );

        array_push($items, $product_data);
      }

      //payment method
      if($payment_data["account"] == ''){
        $num_cta = '';
      }else{
        $num_cta = $payment_data["account"];
      }

      //Consigo la serie
      $series = FacturaWrapper::check_serie();
      $serie = '';
      foreach( $series->data as $ser) {
        if($ser->SerieName == $configEntity['serie'] && $ser->SerieType=='F'){
          $serie = $ser;
        }
      }

      //Creo la estructura del objeto que voy a enviar:
      $cfdi = array(
        "TipoCfdi" => "factura",
        "Receptor" => [
          "UID" => $customer->UID,
        ],
        "UsoCFDI" => $payment_data["cfdi_use"],
        "Serie" => $serie->SerieID,
        "MetodoPago" => "PUE",
        "FormaPago" => $payment_data["method"],
        "Moneda" => $order->currency,
        "Conceptos" => array(),
        'EnviarCorreo' => true,
        "Redondeo" => 2,
        "NumOrder" =>$order->id,
        // "Cuenta" =>(int)$num_cta,
      );
        foreach( $order->line_items as $item  ) {
          // var_dump(floatval(wc_format_decimal($item['meta']['item_total'], 2 )));
          if($item['F_ClaveProdServ'] != "78102203"){
            $precio = floatval(wc_format_decimal($item['meta']['item_total'], 2 ));
          }
          else{
            $precio = floatval(wc_format_decimal($item['total'], 2 ));
          }
        
          if(isset($item['F_IVA']) && $item['F_IVA'] != "") {
            $tasa = $item['F_IVA']/100;  
          }else {
            $tasa = 0.16;
          }
          
          //Reviso la configuración para saber si los precios incluyen iva 
          if($configEntity['sitax'] == "true"){
            $importe = $precio / (1 + $tasa);
          }

          if($configEntity['sitax'] == "false"){
            $importe = $precio;
          }

          if($item['type_tax'] == "none" || $item['type_tax'] == "shipping" && $item['F_ClaveProdServ'] != "78102203"){
            $cfdi['Conceptos'][] = array(
              "ClaveProdServ" => $item['F_ClaveProdServ'],
              "Cantidad" => $item['quantity'],
              "ClaveUnidad" => $item['F_ClaveUnidad'],
              "Unidad" => $item['F_Unidad'],
              "ValorUnitario" => floatval(wc_format_decimal($importe, 2 )),
              "Descripcion" => $item['name'],
            );
          }
          else{

            $valorUnitario = floatval(wc_format_decimal($importe, 6));
            $Base = floatval(wc_format_decimal($valorUnitario * $item['quantity'], 6));
            $Importe = floatval(wc_format_decimal($Base * $tasa, 6));

            $cfdi['Conceptos'][] = array(
              "ClaveProdServ" => $item['F_ClaveProdServ'],
              "Cantidad" => $item['quantity'],
              "ClaveUnidad" => $item['F_ClaveUnidad'],
              "Unidad" => $item['F_Unidad'],
              "ValorUnitario" => $valorUnitario,
              "Descripcion" => $item['name'],
              "Impuestos" => array(
                "Traslados" => array([
                  "Base" => $Base,
                  "Impuesto" => "002",
                  "TipoFactor" => "Tasa",
                  "TasaOCuota" => $tasa,
                  "Importe" => $Importe
                  ]
                ),
              ),
            );
          }

        }

        //echo "<pre>";
        //  var_dump($order);
        //die;
        //  var_dump(woocommerce_price($product->get_price_including_tax()));
        //  die;
        // var_dump($order->line_items);
        // var_dump(json_encode($cfdi));
        // die;
        // $invoiced = WrapperApi::callCurl($url, $request, $params);
        $invoiced = WrapperApi::callCurl($url, $request, $cfdi);
        // var_dump($invoiced->invoice->status);
        // var_dump($invoiced->response);
        //change status
        if($invoiced->response == 'success'){
          self::changeOrderStatus('wc-invoiced', $order->id);
          //  $order->update_status( 'invoiced' );
        }
        // if($invoiced->status == 'success'){
        //   self::changeOrderStatus('wc-invoiced', $order->id);
        //   //  $order->update_status( 'invoiced' );
        // }

        return $invoiced;
      }

      static function check_serie(){
        $configEntity = self::getConfigEntity();

        $url = $configEntity['apiurl'] . "v1/series";
        //$url = 'https://factura.com/api/v1/series';

        $request = 'GET';
        // $params = array(
        //   "nombre"          => $data["g_nombre"],
        //   "apellidos"       => $data["g_apellidos"],
        //   "email"           => $data["g_email"],
        //   "telefono"        => $data["f_telefono"],
        //   "razons"          => $data["f_nombre"],
        //   "rfc"             => $data["f_rfc"],
        //   "calle"           => $data["f_calle"],
        //   "numero_exterior" => $data["f_exterior"],
        //   "numero_interior" => $data["f_interior"],
        //   "codpos"          => $data["f_cp"],
        //   "colonia"         => $data["f_colonia"],
        //   "estado"          => $data["f_estado"],
        //   "ciudad"          => $data["f_municipio"],
        //   "delegacion"      => $data["f_municipio"],
        //   "save"            => true,
        // );

        return WrapperApi::callCurl($url, $request);
      }

      /*
      * Create client in factura.com system
      *
      * @param Array $data customer's data to save in factura.com system
      * @return Array
      *
      */
      static function create_client($data){
        $configEntity = self::getConfigEntity();

        if($data["api_method"] == "create"){
          $url = $configEntity['apiurl'] . "v1/clients/create";
          //$url = 'https://factura.com/api/v1/' . 'clients/create';
        }else{
          $url = $configEntity['apiurl'] . "v1/clients/".$data["uid"]."/update";
          //$url = 'https://factura.com/api/v1/' . "clients/".$data["uid"]."/update";
        }

        $request = 'POST';
        $params = array(
          "nombre"          => $data["g_nombre"],
          "apellidos"       => $data["g_apellidos"],
          "email"           => $data["g_email"],
          "telefono"        => $data["f_telefono"],
          "razons"          => $data["f_nombre"],
          "rfc"             => $data["f_rfc"],
          'regimen'         => $data["f_regimen"],
          "calle"           => $data["f_calle"],
          "numero_exterior" => $data["f_exterior"],
          "numero_interior" => $data["f_interior"],
          "codpos"          => $data["f_cp"],
          "colonia"         => $data["f_colonia"],
          "estado"          => $data["f_estado"],
          "ciudad"          => $data["f_municipio"],
          "delegacion"      => $data["f_municipio"],
          "save"            => true,
        );

        return WrapperApi::callCurl($url, $request, $params);
      }

      /*
      * Change status order in woocommerce system
      *
      * @param String $new_status new status to woocommerce's order
      * @param integer $order_id order id
      * @param integer $invoice_id invoice id
      * @return Array
      */
      static function changeOrderStatus($new_status, $order_id){
        global $wpdb;

        //change to invoiced
        $order = new WC_Order($order_id);
        $order->update_status($new_status, '');
      }

      /**
      *
      */
      static function downloadFile($data){

        $configEntity = self::getConfigEntity();

        $url = $configEntity['apiurl'] . 'v3/cfdi40/'.$data['uid'].'/'.$data['type'];

        $request = 'GET';

        return WrapperApi::callCurl($url, $request, null, true);
      }


    }
