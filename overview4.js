(function( overview4, $, undefined ) {

  overview4.clearModal = function() {
    $('input[name=locationId]').removeAttr('value');
    $('#location-content').val('');
	$('#location-describtion').val('');
	$('#location-bssids').val('');
	
	$('input[type=radio][value=nein]').parent().removeClass('active');
    $('input[type=radio][value=ja]').parent().removeClass('active');

    $('#delete4-button').addClass('hidden');

  };

  overview4.sendDeleteRequest = function() {
    var id = $('input[name=locationId]').val();

    $.ajax({
      url: 'api.php/locations/' + id,
      type: 'DELETE',
      success: function(result) {
          console.log(result);
          location.reload(true);
      }
    });
  };

  overview4.updateTable = function(jsonString) {
    var tableData = JSON.parse(jsonString);

    $('#location-table').bootstrapTable({
      data: tableData.locations
    }).on('click-row.bs.table', function (e, row, $element) {
      overview4.clearModal();


      $('input[name=locationId]').attr('value', row.id);
      $('#location-content').val(row.location);
	  $('#location-describtion').val(row.describtion);
	  $('#location-bssids').val(row.bssids);
	  
	  var navigate = row.navigate;

      $('input[type=radio][value=' + navigate +"]").parent().addClass('active');
      $('input[type=radio][value=' + navigate +"]").attr('checked', '');
	  
      $('#delete4-button').removeClass('hidden');
    });

    $('#location-table > tbody > tr').attr('data-toggle', 'modal');
    $('#location-table > tbody > tr').attr('href', '#location-modal');
    $('#location-table > tbody > tr').attr('style', 'cursor: pointer');
    // data-toggle="modal" href="#location-modal"
  };

  overview4.showModal = function() {
    $('#location-modal').modal('show');
  };
}( window.overview4 = window.overview4 || {}, jQuery ));
