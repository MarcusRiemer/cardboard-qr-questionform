(function( overviewPosition, $, undefined ) {

  overviewPosition.clearModal = function() {
    $('input[name=positionId]').removeAttr('value');
    $('#position-content').val('');

	$.get(
		"api.php/locationcount",
		 {},
		 function(number) {
			var count = JSON.parse(number);
			for (i=0;i<(count);i++){
				$('#destination-'+i).val('');
			}
	    }	
	);
	
	$('#qr-preview-position').addClass('hidden');
    $('#deletePosition-button').addClass('hidden');
    $('#printPosition-button').addClass('hidden');
  };

  overviewPosition.print = function() {
    var prtContent = document.getElementById("printPosition-page");
    var WinPrint = window.open('', '', 'left=0,top=0,toolbar=0,scrollbars=0,status=0');
    WinPrint.document.write(prtContent.innerHTML);
    WinPrint.document.close();
    WinPrint.focus();
    WinPrint.print();
    WinPrint.close();
  };

  overviewPosition.sendDeleteRequest = function() {
    var id = $('input[name=positionId]').val();

    $.ajax({
      url: 'api.php/positions/' + id,
      type: 'DELETE',
      success: function(result) {
          console.log(result);
          location.reload(true);
      }
    });
  };
  
  overviewPosition.createHtml = function(jsonString) {
	var tableData = JSON.parse(jsonString);
    var container = document.getElementById("locontainer");
	while (container.hasChildNodes()) {
		container.removeChild(container.lastChild);
    }
	var selectArray = ["Straight", "StraightLeft", "StraightRight", "Left", "LeftLeft", "LeftRight", "Right", "RightLeft", "RightRight", "Back", "BackLeft", "BackRight"];
	var count = tableData.locations.length;
	tableData.locations.sort(
		function(a, b){
			return a.id - b.id;
		}
	)
	for (i = 0; i < count; i++){
        container.appendChild(document.createTextNode(tableData.locations[i].location+"  "));
        var selectList = document.createElement("select");	
		selectList.id = "destination-"+i;
        selectList.name = "destination"+i;
        container.appendChild(selectList);
		for (x = 0; x < selectArray.length; x++) {
			var option = document.createElement("option");
			option.value = selectArray[x];
			option.text = selectArray[x];
			selectList.appendChild(option);
		}
        // Append a line break 
        container.appendChild(document.createElement("br"));
    }
  }

  overviewPosition.updateTable = function(jsonString) {
    var tableData = JSON.parse(jsonString);

    $('#position-table').bootstrapTable({
      data: tableData.positions
    }).on('click-row.bs.table', function (e, row, $element) {
      overviewPosition.clearModal();

      $('#qr-preview-position').removeClass('hidden');
	  
      $('input[name=positionId]').attr('value', row.id);
      $('#position-content').val(row.position);

	  $.get(
		"api.php/locationcount",
		{},
		function(data) {
			var count = JSON.parse(data);
			for (i=0;i<count;i++){		
				$('#destination-'+i).val(row.arrows[i]);
			}
	    }	
	  );
	  
      $.get(
          "api.php/qrcodesPosition/" + row.id,
          {},
          function(data) {
            var qrCodes = JSON.parse(data);
            console.log(data);
            $('#position-qr').attr('src', qrCodes.position);
          }
      );
	  
	  $.get(
		"api.php/qrcodesprintPosition/" + row.id,
        {},
		function(data) {
			var qrCodes = JSON.parse(data);
            console.log(data);
            $('#print-position-qr').attr('src', qrCodes.position);
        }
      );
	  
	  $('#printPosition-title').html("Position: " + row.position);
	  
      $('#deletePosition-button').removeClass('hidden');
      $('#printPosition-button').removeClass('hidden');
	  
     });

    $('#position-table > tbody > tr').attr('data-toggle', 'modal');
    $('#position-table > tbody > tr').attr('href', '#position-modal');
    $('#position-table > tbody > tr').attr('style', 'cursor: pointer');
    // data-toggle="modal" href="#position-modal"
  };

  overviewPosition.showModal = function() {
    $('#position-modal').modal('show');
  };
}( window.overviewPosition = window.overviewPosition || {}, jQuery ));
