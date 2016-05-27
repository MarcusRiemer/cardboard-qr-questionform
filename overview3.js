(function( overview3, $, undefined ) {

  overview3.clearModal = function() {
    $('input[name=thingId]').removeAttr('value');
    $('#thing-content').val('');
	$('#thing-start').val('');
	$('#thing-location').val('');

    $('#delete3-button').addClass('hidden');

  };

  overview3.sendDeleteRequest = function() {
    var id = $('input[name=thingId]').val();

    $.ajax({
      url: 'api.php/things/' + id,
      type: 'DELETE',
      success: function(result) {
          console.log(result);
          location.reload(true);
      }
    });
  };
  
  overview3.createSelect = function(jsonString){
    var tableData = JSON.parse(jsonString);	
    var container = document.getElementById("thingContainer");
	while (container.hasChildNodes()) {
		container.removeChild(container.lastChild);
    }
    var selectList = document.createElement("select");	
	selectList.id = "thing-location";
    selectList.name = "location";
    container.appendChild(selectList);
	var selectArray = [];
	var count = tableData.locations.length;
	tableData.locations.sort(
		function(a, b){
			return a.id - b.id;
		}
	)
	for (i=0; i<(count); i++){
		var loca = tableData.locations[i].location;
		var option = document.createElement("option");
		option.value = loca;
		option.text = loca;
		selectList.appendChild(option);
	}
    // Append a line break 
    container.appendChild(document.createElement("br"));
  }

  overview3.updateTable = function(jsonString) {
    var tableData = JSON.parse(jsonString);
	tableData.locations
	
    $('#thing-table').bootstrapTable({
      data: tableData.things
    }).on('click-row.bs.table', function (e, row, $element) {
      overview3.clearModal();

      $('input[name=thingId]').attr('value', row.id);
      $('#thing-content').val(row.thing);
	  $('#thing-start').val(row.start);
	  $('#thing-location').val(row.location);

      $('#delete3-button').removeClass('hidden');

    });

    $('#thing-table > tbody > tr').attr('data-toggle', 'modal');
    $('#thing-table > tbody > tr').attr('href', '#thing-modal');
    $('#thing-table > tbody > tr').attr('style', 'cursor: pointer');
    // data-toggle="modal" href="#thing-modal"
  };

  overview3.showModal = function() {
    $('#thing-modal').modal('show');
  };
}( window.overview3 = window.overview3 || {}, jQuery ));
