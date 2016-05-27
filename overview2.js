(function( overview2, $, undefined ) {

  overview2.clearModal = function() {
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
	
	$('#qr-preview2').addClass('hidden');
    $('#delete2-button').addClass('hidden');
    $('#print2-button').addClass('hidden');
  };

  overview2.print = function() {
    var prtContent = document.getElementById("print-page2");
    var WinPrint = window.open('', '', 'left=0,top=0,toolbar=0,scrollbars=0,status=0');
    WinPrint.document.write(prtContent.innerHTML);
    WinPrint.document.close();
    WinPrint.focus();
    WinPrint.print();
    WinPrint.close();
  };

  overview2.sendDeleteRequest = function() {
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
  
  overview2.createHtml = function(jsonString) {
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

  overview2.updateTable = function(jsonString) {
    var tableData = JSON.parse(jsonString);

    $('#position-table').bootstrapTable({
      data: tableData.positions
    }).on('click-row.bs.table', function (e, row, $element) {
      overview2.clearModal();

      $('#qr-preview2').removeClass('hidden');
	  
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
          "api.php/qrcodes2/" + row.id,
          {},
          function(data) {
            var qrCodes = JSON.parse(data);
            console.log(data);
            $('#position-qr').attr('src', qrCodes.position);
          }
      );
	  
	  $.get(
		"api.php/qrcodesprint2/" + row.id,
        {},
		function(data) {
			var qrCodes = JSON.parse(data);
            console.log(data);
            $('#print-position-qr').attr('src', qrCodes.position);
        }
      );
	  
	  $('#print-title2').html("Position: " + row.position);
	  
      $('#delete2-button').removeClass('hidden');
      $('#print2-button').removeClass('hidden');
	  
     });

    $('#position-table > tbody > tr').attr('data-toggle', 'modal');
    $('#position-table > tbody > tr').attr('href', '#position-modal');
    $('#position-table > tbody > tr').attr('style', 'cursor: pointer');
    // data-toggle="modal" href="#position-modal"
  };

  overview2.showModal = function() {
    $('#position-modal').modal('show');
  };
}( window.overview2 = window.overview2 || {}, jQuery ));
