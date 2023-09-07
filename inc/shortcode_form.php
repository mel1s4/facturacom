<?php 
  $rfc = (@$_GET['rfc']) ? $_GET['rfc'] : '';
  $email = (@$_GET['email']) ? $_GET['email'] : '';
  $order = (@$_GET['order']) ? $_GET['order'] : '';

  if($rfc !== '' && $email !== '' && $order !== '') {
    ?>
      <script>
        setTimeout(() => {
          console.log('yes');
          document.querySelector('[data-remodal-action=confirm]').click();
        }, 100);
      </script>
    <?php
  }
?>
  <div id="">
  <div class="full-width">
  <div id="primary" class="content-area">
  <div id="content" class="site-content" role="main">
  <div class="home_product">
  <div class="steps-container">
  <div class="f-welcome-container">
  <?php if(!empty($configEntity['title'])): ?>
    <h1 class="f-page-title"><?php $configEntity['title'] ?></h1>
  <?php 
    endif;
  ?>
  </div>
  
  <div id="step-one" class="step-block">
  <div class="step-header" style="background:<?php echo $configEntity['colorheader'] ?>">
  <h1 style="color:<?php echo $configEntity['colorfont'] ?>">
  <span>Paso 1/4</span>
  Identificar pedido
  </h1>
  </div>
  <div class="step-content">
  <p class="step-instruction">Ingresa tu RFC, n&uacute;mero de pedido y correo electr&oacute;nico para buscar tu pedido.</p>
  <form name="f-step-one-form" id="f-step-one-form" action="<?php echo get_permalink(); ?>" method="post">
  <input type="hidden" name="csrf" value="" />
  <label for="f-rfc" >RFC<span class="requerido">*</span></label>
  <input type="text" class="input-upper f-input" id="f-rfc" name="rfc" value="<?php echo $rfc ?>" placeholder="12 o 13 dígitos" />
  <label for="f-num-order" >N&uacute;m de pedido<span class="requerido">*</span></label>
  <input type="text" class="f-input" id="f-num-order" name="order" value="<?php echo $order ?>" placeholder="Sin signo  #"  />
  <label for="f-email">Correo electr&oacute;nico<span class="requerido">*</span></label>
  <input type="email" class="f-input" id="f-email" name="email" value="<?php echo $email ?>" placeholder="El correo registrado en el pedido"  />
  <div class="buttons-right">
  <input type="submit" class="f-submit" id="step-one-button-next" style="background:<?php echo $configEntity['colorheader'] ?> color:<?php echo $configEntity['colorfont'] ?>" name="f-submit" value="Siguiente" />
  </div>
  <div class="error_msj"></div>
  <div class="clearfix"></div>
  </form>
  </div>
  <div class="loader_content">
  <div class="loader">Cargando...</div>
  </div>
  <div class="remodal" data-remodal-id="respuesta-paso-uno">
  <button data-remodal-action="close" class="remodal-close"></button>
  <h1 id="message-response-one"> </h1>
  <br>
  <button data-remodal-action="confirm" class="remodal-confirm">Aceptar</button>
  </div>
  </div>
  <!-- step one ends -->
  <!-- step two starts -->
  <div id="step-two" class="step-block">
  <div class="step-header" style="background:<?php echo $configEntity['colorheader'] ?>">
  <h1 style="color:<?php echo $configEntity['colorfont'] ?>">
  <span>Paso 2/4</span>
  Datos de facturaci&oacute;n
  </h1>
  </div>
  <div class="step-content">
  <p class="step-instruction"></p>
  <form name="f-step-two-form" id="f-step-two-form" action="<?php echo get_permalink(); ?>" method="post">
  <input type="hidden" name="csrf" value="" />
  <input type="hidden" id="apimethod" name="apimethod" value="create" />
  <input type="hidden" id="uid" name="uid" value="" />
  <h3>Datos de contacto</h3>
  <div class="input-group">
  <label for="general-nombre">Nombre</label>
  <input type="text" class="input-cap f-input f-top" id="general-nombre" name="general-nombre" value="" placeholder="Nombre" readonly />
  </div>
  <div class="input-group">
  <label for="general-apellidos">Apellidos</label>
  <input type="text" class="input-cap f-input f-top" id="general-apellidos" name="general-apellidos" value="" placeholder="Apellidos" readonly />
  </div>
  <div class="input-group">
  <label for="general-email">Correo electr&oacute;nico</label>
  <input type="email" class="f-input f-top" id="general-email" name="general-email" value="" placeholder="Email para envío de CFDI" readonly />
  </div>
  <div class="input-group">
  <label for="fiscal-telefono">Tel&eacute;fono</label>
  <input type="text" class="input-cap f-input f-no-top f-right f-bottom" id="fiscal-telefono" name="fiscal-telefono" value="" placeholder="10 digitos" readonly />
  </div>
  <br>
  <h3>Datos fiscales</h3>
  <div class="input-group">
  <label for="fiscal-nombre">Razón Social</label>
  <input type="text" class="input-cap f-input f-top" id="fiscal-nombre" name="fiscal-nombre" value="" placeholder="Razón social" readonly />
  </div>
  <div class="input-group">
  <label for="fiscal-rfc">RFC</label>
  <input type="text" class="input-upper f-input f-top" id="fiscal-rfc" name="fiscal-rfc" value="" placeholder="12 o 13 dígitos" readonly />
  </div>
  <div class="input-group">
  <label for="fiscal-regimen">Régimen fiscal</label>
  <select id="fiscal-regimen" name="fiscal-regimen" class="input-cap f-select-two f-top" disabled>
    <option value="">Selecciona un régimen fiscal</option>
    <option value="601">General de Ley Personas Morales</option>
    <option value="603">Personas Morales con Fines no Lucrativos</option>
    <option value="605">Sueldos y Salarios e Ingresos Asimilados a Salarios</option>
    <option value="606">Arrendamiento</option>
    <option value="607">Régimen de Enajenación o Adquisición de Bienes</option>
    <option value="608">Demás ingresos</option>
    <option value="610">Residentes en el Extranjero sin Establecimiento Permanente en México</option>
    <option value="611">Ingresos por Dividendos (socios y accionistas)</option>
    <option value="612">Personas Físicas con Actividades Empresariales y Profesionales</option>
    <option value="614">Ingresos por intereses</option>
    <option value="615">Régimen de los ingresos por obtención de premios</option>
    <option value="616">Sin obligaciones fiscales</option>
    <option value="620">Sociedades Cooperativas de Producción que optan por diferir sus ingresos</option>
    <option value="621">Incorporación Fiscal</option>
    <option value="622">Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras</option>
    <option value="623">Opcional para Grupos de Sociedades</option>
    <option value="624">Coordinados</option>
    <option value="625">Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas</option>
    <option value="626">Régimen Simplificado de Confianza</option>
  </select>
  </div>
  <div class="input-group">
  <label for="fiscal-calle">Calle</label>
  <input type="text" class="input-cap f-input f-no-top" id="fiscal-calle" name="fiscal-calle" value="" placeholder="Calle" readonly />
  </div>
  <div class="input-group">
  <label for="fiscal-exterior">N&uacute;mero exterior</label>
  <input type="text" class="input-cap f-input f-no-top" id="fiscal-exterior" name="fiscal-exterior" value="" placeholder="No. Exterior" readonly />
  </div>
  <div class="input-group" >
  <label for="fiscal-interior">N&uacute;mero interior</label>
  <input type="text" class="input-cap f-input" id="fiscal-interior" name="fiscal-interior" value="" placeholder="No. Interior" readonly />
  </div>
  <div class="input-group">
  <label for="fiscal-colonia">Colonia</label>
  <input type="text" class="input-cap f-input f-right" id="fiscal-colonia" name="fiscal-colonia" value="" placeholder="Colonia" readonly />
  </div>
  <div class="input-group">
  <label for="fiscal-municipio">Delegaci&oacute;n o Municipio</label>
  <input type="text" class="input-cap f-input f-no-top f-right" id="fiscal-municipio" name="fiscal-municipio" value="" placeholder="Municipio" readonly />
  </div>
  <div class="input-group">
  <label for="fiscal-estado">Estado</label>
  <input type="text" class="input-cap f-input" id="fiscal-estado" name="fiscal-estado" value="" placeholder="Estado" readonly />
  </div>
  <div class="input-group">
  <label for="fiscal-pais">Pa&iacute;s</label>
  <input type="text" class="input-cap f-input f-right" id="fiscal-pais" name="fiscal-pais" value="México" placeholder="País" readonly />
  </div>
  <div class="input-group">
  <label for="fiscal-cp">C&oacute;digo Postal</label>
  <input type="text" class="input-cap f-input f-no-top f-bottom" id="fiscal-cp" name="fiscal-cp" value="" placeholder="Código postal" readonly />
  </div>
  <div class="input-group">
    <div class="error_msj"></div>
  </div>
  <div class="clearfix"></div>
  <div class="buttons-right">
  <input type="button" class="f-submit f-back" id="step-two-button-back" style="background:<?php echo $configEntity['colorheader'] ?> color:<?php echo $configEntity['colorfont'] ?>" name="f-back" value="Volver" data-f="2" />
  <input type="button" class="f-submit f-edit" id="step-two-button-edit" style="background:<?php echo $configEntity['colorheader'] ?> color:<?php echo $configEntity['colorfont'] ?>" name="f-edit" value="Editar" data-b="1" />
  <input type="submit" class="f-submit" id="step-two-button-next" style="background:<?php echo $configEntity['colorheader'] ?> color:<?php echo $configEntity['colorfont'] ?>" name="f-submit" value="Siguiente" />
  </div>
  <div class="f-loading">Cargando...</div>
  <div class="clearfix"></div>
  </form>
  </div>
  <div class="loader_content">
  <div class="loader">Cargando...</div>
  </div>
  <div class="remodal" data-remodal-id="respuesta-paso-dos">
  <button data-remodal-action="close" class="remodal-close"></button>
  <h1 id="message-response-dos"> </h1>
  <br>
  <button data-remodal-action="cancel" class="remodal-confirm">Aceptar</button>
  </div>
  </div>
  <!-- step two ends -->
  <!-- step three starts -->
  <div id="step-three" class="step-block step-invoice">
  <div class="step-header" style="background:<?php echo $configEntity['colorheader'] ?>">
  <h1 style="color:<?php echo $configEntity['colorfont'] ?>">
  <span>Paso 3/4</span>
  Verificar datos de pedido
  </h1>
  </div>
  <div class="step-content">
  <h3 class="invoice-title"> <span id="invoice-id">3526321</span></h3>
  <h3 class="invoice-title"> <span id="invoice-date">30/06/2015</span></h3>
  <div class="invoice-sections">

  <div class="invoice-emisor">
  <h3 class="invoice-header">Emisor</h3>
  <span id="emisor-nombre" class="ref-data"></span>
  <span id="emisor-rfc" class="ref-data"></span>
  <span id="emisor-direccion" class="ref-data"></span>
  <span id="emisor-direccion-zone" class="ref-data"></span>
  <span id="emisor-direccion-zone-city" class="ref-data"></span>
  <span id="emisor-telefono" class="ref-data"></span>
  <span id="emisor-email" class="ref-data"></span>
  </div>

  <div class="invoice-receptor">
  <h3 class="invoice-header">Receptor</h3>
  <span id="receptor-nombre" class="ref-data"></span>
  <span id="receptor-rfc" class="ref-data"></span>
  <span id="receptor-regimen" class="ref-data"></span>
  <span id="receptor-direccion" class="ref-data"></span>
  <span id="receptor-direccion-zone" class="ref-data"></span>
  <span id="receptor-direccion-zone-city" class="ref-data"></span>
  <span id="receptor-email" class="ref-data"></span>
  </div>

  <div class="invoice-details">
  <h3 class="invoice-header">Detalle del pedido</h3>
  <table id="table-details">
  <thead>
  <tr>
  <td>Producto</td>
  <td>Cantidad</td>
  <td>Precio unitario</td>
  <td>Total</td>
  </tr>
  </thead>
  <tbody id="datails-body">

  </tbody>
  </table>
  </div>

  <div class="invoice-payment">
  <h3 class="invoice-header">Informaci&oacute;n de pago</h3>
  Ingrese la información que se pide a continuaci&oacute;n:
    <form id="payment-method-form">
    <!-- Método de pago -->
    <div class="input-group">
    <label for="select-payment">* Forma de pago</label>
    <select id="select-payment" class="input-cap f-input f-select">
    <option value="01">01 - Efectivo</option>
    <option value="02">02 - Cheque nominativo</option>
    <option value="03">03 - Transferencia electrónica de fondos</option>
    <option value="04">04 - Tarjeta de crédito</option>
    <option value="05">05 - Monedero Electrónico</option>
    <option value="06">06 - Dinero electrónico</option>
    <option value="08">08 - Vales de despensa</option>
    <option value="12">12 - Dación en pago</option>
    <option value="13">13 - Pago por subrogación</option>
    <option value="14">14 - Pago por consignación</option>
    <option value="15">15 - Condonación</option>
    <option value="17">17 - Compensación</option>
    <option value="23">23 - Novación</option>
    <option value="24">24 - Confusión</option>
    <option value="25">25 - Remisión de deuda</option>
    <option value="26">26 - Prescripción o caducidad</option>
    <option value="27">27 - A satisfacción del acreedor</option>
    <option value="28">28 - Tarjeta de débito</option>
    <option value="29">29 - Tarjeta de servicios</option>
    <option value="31">31 - Intermediario de pagos</option>
    <option value="99">99 - Por definir</option>
    </select>
    </div>
    <div class="clearfix"></div>
    <div id="num-cta-box" class="input-group">
    <label for="f-num-cta" style="width: 285px;">&Uacute;ltimos 4 dígitos de tu cuenta o tarjeta</label>
    <input type="text" class="input-cap f-input f-no-top f-bottom f-digits" id="f-num-cta" name="f-num-cta" value="" placeholder="####" min="4" max="4"/>
    </div>
    <div class="clearfix"></div>
    </form>
    </p>
    </div>

    <div>
    <h3 class="invoice-header">Uso CFDI</h3>
    Selecciona el uso cfdi de factura
    <form id="cfdi-use-form">
    <!-- Método de pago -->
    <div class="input-group">
    <label for="cfdi-use">* UsoCFDI</label>
    <select id="cfdi-use" class="input-cap f-input f-select">
    <option value="G01">Adquisición de mercancias</option>
    <option value="G02">Devoluciones, descuentos o bonificaciones</option>
    <option value="G03">Gastos en general</option>
    <option value="I01">Construcciones</option>
    <option value="I02">Mobilario y equipo de oficina por inversiones</option>
    <option value="I03">Equipo de transporte</option>
    <option value="I04">Equipo de computo y accesorios</option>
    <option value="I05">Dados, troqueles, moldes, matrices y herramental</option>
    <option value="I06">Comunicaciones telefónicas</option>
    <option value="I07">Comunicaciones satelitales</option>
    <option value="I08">Otra maquinaria y equipo</option>
    <option value="D01">Honorarios médicos, dentales y gastos hospitalarios</option>
    <option value="D02">Gastos médicos por incapacidad o discapacidad</option>
    <option value="D03">Gastos funerales</option>
    <option value="D04">Donativos</option>
    <option value="D05">Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)</option>
    <option value="D06">Aportaciones voluntarias al SAR</option>
    <option value="D07">Primas por seguros de gastos médicos</option>
    <option value="D08">Gastos de transportación escolar obligatoria</option>
    <option value="D09">Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones</option>
    <option value="D10">Pagos por servicios educativos (colegiaturas)</option>
    <option value="S01">Sin efectos fiscales</option>

    </select>
    </div>
    <div class="clearfix"></div>
    </form>
    </div>

    <div class="invoice-totals">
    <table>
    <tr>
    <td>Subtotal</td>
    <td><span id="invoice-subtotal"></span></td>
    </tr>
    <tr id="td-discount">
    <td>Descuento</td>
    <td><span id="invoice-discount"></span></td>
    </tr>
    <tr id="tr-iva">
    <td>IVA</td>
    <td><span id="invoice-iva"></span></td>
    </tr>
    <tr>
    <td>Total</td>
    <td><span id="invoice-total"></span></td>
    </tr>
    </table>
    </div>
    <div class="clearfix"></div>
    <p class="f-page-subtitle">
    Antes de generar la factura, por favor confirme que los datos estén correctamente. <em>Ya que una vez emitida o generada la factura no podrá realizar cambios a la misma</em>. Agradecemos su preferencia.
    </p>
    <div class="clearfix"></div>
    <div class="buttons-right">
    <input type="button" class="f-submit f-back" id="step-three-button-back" name="f-back" value="Volver" data-f="3" />
    <input type="button" class="f-submit f-back" style="background:<?php echo $configEntity['colorheader'] ?> color:<?php echo $configEntity['colorfont'] ?>" id="step-three-button-next" name="f-submit" value="Generar factura" />
    </div>
    <div class="clearfix"></div>
    </div>
    </div>
    <div class="loader_content">
    <div class="loader">Cargando...</div>
    </div>
    </div>
    <!-- step three ends -->
    <!-- step four starts -->
    <div id="step-four" class="step-block step-invoice">
    <div class="step-header" style="background:<?php echo $configEntity['colorheader'] ?>">
    <h1 style="color:<?php echo $configEntity['colorfont'] ?>">
    <h1>
    <span>Paso 4/4</span>
    Resultado de facturaci&oacute;n
    </h1>
    </div>
    <div class="step-content">
    <div class="buttons_container">
    <h1 id="result-msg-title">La factura ha sido creada y enviada con &eacute;xito.</h1>
    <div class="clearfix"></div>
    <h4 id="result-email-msg"></h4>
    <h4 id="result-msg"></h4>
    <div class="invoice-success-screen">
      <a href="#" id="btn-success-email" class="btn-success invoice-button invoice-pdf" target="_blank">Enviar por correo electr&oacute;nico</a>
      <button id="btn-success-pdf" class="btn-success invoice-button invoice-pdf" style="background:<?php echo $configEntity['colorheader'] ?>!important; color:<?php echo $configEntity['colorfont'] ?>!important;">Descargar PDF</button>
      <button id="btn-success-xml" class="btn-success invoice-button invoice-xml" style="background:<?php echo $configEntity['colorheader'] ?>!important; color:<?php echo $configEntity['colorfont'] ?>!important;">Descargar XML</button>
    </div>
    </div>
    <div id="out-message">
    <h3>Ya puedes cerrar &eacute;sta p&aacute;gina o <a href="<?php get_site_url() ?>">seguir navegando</a>.</h3>
    </div>
    </div>
    </div>
    <div id="out-message">

    </div>
    </div>
    <!-- step four ends -->

    </div>
    </div>
    </div>
    </div>
    </div>