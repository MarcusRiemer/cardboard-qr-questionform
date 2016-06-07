(function( overviewParticle, $, undefined ) {

  overviewParticle.clearModal = function() {
    $('input[name=particleId]').removeAttr('value');

    $('#qr-preview-particle').addClass('hidden');
    $('#delete5-button').addClass('hidden');
    $('#print5-button').addClass('hidden');
	
	$('#start-color-picker').colorpicker('setValue', '#000000');
    $('#end-color-picker').colorpicker('setValue', '#000000');
  };

  overviewParticle.print = function() {
    var prtContent = document.getElementById("print-page5");
    var WinPrint = window.open('', '', 'left=0,top=0,toolbar=0,scrollbars=0,status=0');
    WinPrint.document.write(prtContent.innerHTML);
    WinPrint.document.close();
    WinPrint.focus();
    WinPrint.print();
    WinPrint.close();
  };
  

  overviewParticle.sendDeleteRequest = function() {
    var id = $('input[name=particleId]').val();
	console.log('api.php/particles/' + id);

    $.ajax({
      url: 'api.php/particles/' + id,
      type: 'DELETE',
      success: function(result) {
          console.log(result);
          location.reload(true);
      }
    });
  };

  overviewParticle.updateTable = function(jsonString) {
    var tableData = JSON.parse(jsonString);

    $('#particle-table').bootstrapTable({
      data: tableData.particles
    }).on('click-row.bs.table', function (e, row, $element) {
      overviewParticle.clearModal();

      $('#qr-preview-particle').removeClass('hidden');

      $('input[name=particleId]').attr('value', row.id);

      $('#start-color-picker').colorpicker('setValue', row.startColor);
	  $('#end-color-picker').colorpicker('setValue', row.endColor);

      $.get(
          "api.php/qrcodesParticle/" + row.id,
          {},
          function(data) {
              var qrCodes = JSON.parse(data);
              console.log(data);
              $('#particle-qr').attr('src', qrCodes.particle);
          }
      );

      $.get(
          "api.php/qrcodesprintParticle/" + row.id,
          {},
          function(data) {
              var qrCodes = JSON.parse(data);
              console.log(data);
              $('#print-particle-qr').attr('src', qrCodes.particle);
          }
      );

      $('#print-title5').html("Partikelsystem: " + row.startColor + " - " + row.endColor);

      $('#delete5-button').removeClass('hidden');
      $('#print5-button').removeClass('hidden');

    });

    $('#particle-table > tbody > tr').attr('data-toggle', 'modal');
    $('#particle-table > tbody > tr').attr('href', '#particle-modal');
    $('#particle-table > tbody > tr').attr('style', 'cursor: pointer');
    // data-toggle="modal" href="#particle-modal"
  };

  overviewParticle.showModal = function() {
    $('#particle-modal').modal('show');
  };
}( window.overviewParticle = window.overviewParticle || {}, jQuery ));
