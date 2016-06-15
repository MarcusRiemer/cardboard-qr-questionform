(function( overviewLocation, $, undefined ) {

  overviewLocation.clearModal = function() {
    $('input[name=locationId]').removeAttr('value');
    $('#location-content').val('');
	$('#location-describtion').val('');
	$('#location-bssids').val('');
	
	$('input[type=radio][value=nein]').parent().removeClass('active');
    $('input[type=radio][value=ja]').parent().removeClass('active');

    $('#deleteLocation-button').addClass('hidden');

  };

  overviewLocation.sendDeleteRequest = function() {
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

  overviewLocation.updateTable = function(jsonString) {
    var tableData = JSON.parse(jsonString);

    $('#location-table').bootstrapTable({
      data: tableData.locations
    }).on('click-row.bs.table', function (e, row, $element) {
      overviewLocation.clearModal();


      $('input[name=locationId]').attr('value', row.id);
      $('#location-content').val(row.location);
	  $('#location-describtion').val(row.describtion);
	  $('#location-bssids').val(row.bssids);
	  
	  var navigate = row.navigate;

      $('input[type=radio][value=' + navigate +"]").parent().addClass('active');
      $('input[type=radio][value=' + navigate +"]").attr('checked', '');
	  
      $('#deleteLocation-button').removeClass('hidden');
    });

    $('#location-table > tbody > tr').attr('data-toggle', 'modal');
    $('#location-table > tbody > tr').attr('href', '#location-modal');
    $('#location-table > tbody > tr').attr('style', 'cursor: pointer');
    // data-toggle="modal" href="#location-modal"
  };

  overviewLocation.showModal = function() {
    $('#location-modal').modal('show');
  };
}( window.overviewLocation = window.overviewLocation || {}, jQuery ));
