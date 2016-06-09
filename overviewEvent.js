(function( overviewEvent, $, undefined ) {

  overviewEvent.clearModal = function() {
    $('input[name=thingId]').removeAttr('value');
    $('#thing-content').val('');
	$('#thing-start').val('');
	$('#thing-location').val('');

    $('#deleteThing-button').addClass('hidden');

  };

  overviewEvent.sendDeleteRequest = function() {
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
  
  overviewEvent.createSelect = function(jsonString){
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

  overviewEvent.updateTable = function(jsonString) {
    var tableData = JSON.parse(jsonString);
	tableData.locations
	
    $('#thing-table').bootstrapTable({
      data: tableData.things
    }).on('click-row.bs.table', function (e, row, $element) {
      overviewEvent.clearModal();

      $('input[name=thingId]').attr('value', row.id);
      $('#thing-content').val(row.thing);
	  $('#thing-start').val(row.start);
	  $('#thing-location').val(row.location);

      $('#deleteThing-button').removeClass('hidden');

    });

    $('#thing-table > tbody > tr').attr('data-toggle', 'modal');
    $('#thing-table > tbody > tr').attr('href', '#thing-modal');
    $('#thing-table > tbody > tr').attr('style', 'cursor: pointer');
    // data-toggle="modal" href="#thing-modal"
  };

  overviewEvent.showModal = function() {
    $('#thing-modal').modal('show');
  };
}( window.overviewEvent = window.overviewEvent || {}, jQuery ));
