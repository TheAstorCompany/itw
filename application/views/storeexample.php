<?php include("application/views/admin/common/header.php");?>
<?php include("application/views/admin/common/top_menu.php");?>

    <script>
	$(function() {
		$('#tabs').tabs();
		$('#wastelist').dataTable( {
					"bPaginate": false,
					"bFilter": false
				} );
				$('#recyclelist').dataTable( {
					"bPaginate": false,
					"bFilter": false
				} );
				$('#costlist').dataTable( {
					"bPaginate": false,
					"bFilter": false
				} );
				$('#invoiceslist').dataTable( {
					"bPaginate": false,
					"bFilter": false
				} );
				$('#serviceslist').dataTable( {
					"bPaginate": false,
					"bFilter": false
				} );
				$('#callslist').dataTable( {
					"bPaginate": false,
					"bFilter": false
				} );
	});
	</script>
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});
    </script>
    <script type="text/javascript">
      function drawVisualization() {
        // Some raw data (not necessarily accurate)
        var countries =
          ['Aluminum', 'Plastic',
           'LDPE Film', 'Cardboard', 'Waste'];
        var months = ['09/2011','10/2011', '11/2011', '12/2011', '01/2012', 'Current'];
        var productionByCountry = [[200, 365, 135, 357, 239, 236],
                                   [800, 938, 1120, 1167, 1110, 691],
                                   [400, 522, 599, 587, 615, 629],
                                   [1100, 998, 1268, 807, 968, 1026],
                                   [400, 450, 288, 397, 215, 366]];
      
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Month');
        for (var i = 0; i < countries.length; ++i) {
          data.addColumn('number', countries[i]);
        }
        data.addRows(months.length);
        for (var i = 0; i < months.length; ++i) {
          data.setCell(i, 0, months[i]);
        }
        for (var i = 0; i < countries.length; ++i) {
          var country = productionByCountry[i];
          for (var month = 0; month < months.length; ++month) {
            data.setCell(month, i + 1, country[month]);
          }
        }
        // Create and draw the visualization.
        var ac = new google.visualization.AreaChart(document.getElementById('visualization'));
        ac.draw(data, {
          title : '',
          isStacked: true,
          width: 600,
          height: 300,
          vAxis: {title: "Tons"},
          hAxis: {title: "Month"}
        });
      }
      

      google.setOnLoadCallback(drawVisualization);

       function drawVisualization2() {
        // Some raw data (not necessarily accurate)
        var countries =
          ['Waste', 'Hazardous',
           'Other'];
        var months = ['09/2011','10/2011', '11/2011', '12/2011', '01/2012', 'Current'];
        var productionByCountry = [[200, 365, 135, 357, 239, 236],
                                   [800, 938, 1120, 1167, 1110, 691],
                                   [400, 522, 599, 587, 615, 629]];
      
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Month');
        for (var i = 0; i < countries.length; ++i) {
          data.addColumn('number', countries[i]);
        }
        data.addRows(months.length);
        for (var i = 0; i < months.length; ++i) {
          data.setCell(i, 0, months[i]);
        }
        for (var i = 0; i < countries.length; ++i) {
          var country = productionByCountry[i];
          for (var month = 0; month < months.length; ++month) {
            data.setCell(month, i + 1, country[month]);
          }
        }
        // Create and draw the visualization.
        var ac = new google.visualization.AreaChart(document.getElementById('visualization2'));
        ac.draw(data, {
          title : '',
          isStacked: true,
          width: 600,
          height: 300,
          vAxis: {title: "Tons"},
          hAxis: {title: "Month"}
        });
      }
      

      google.setOnLoadCallback(drawVisualization2);
	  
	        function drawVisualization3() {
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task');
        data.addColumn('number', 'Hours per Day');
        data.addRows(5);
        data.setValue(0, 0, 'Waste');
        data.setValue(0, 1, 11);
        data.setValue(1, 0, 'Hazardous');
        data.setValue(1, 1, 2);
        data.setValue(2, 0, 'Other');
        data.setValue(2, 1, 2);
      
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('wastecostschart')).
            draw(data, {title:"Waste Costs"});
      }
      

      google.setOnLoadCallback(drawVisualization3);
	  
	  
      function drawVisualization4() {
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task');
        data.addColumn('number', 'Hours per Day');
        data.addRows(5);
        data.setValue(0, 0, 'Waste');
        data.setValue(0, 1, 11);
        data.setValue(1, 0, 'Hazardous');
        data.setValue(1, 1, 2);
        data.setValue(2, 0, 'Other');
        data.setValue(2, 1, 2);
      
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('wastetypeschart')).
            draw(data, {title:"Waste Types"});
      }
       google.setOnLoadCallback(drawVisualization4);
	   
	   function drawVisualization5() {
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task');
        data.addColumn('number', 'Hours per Day');
        data.addRows(5);
        data.setValue(0, 0, 'Aluminum');
        data.setValue(0, 1, 11);
        data.setValue(1, 0, 'Cardboard');
        data.setValue(1, 1, 2);
        data.setValue(2, 0, 'LDPE Film');
        data.setValue(2, 1, 2);
        data.setValue(3, 0, 'Plastic');
        data.setValue(3, 1, 2);
        data.setValue(4, 0, 'Other');
        data.setValue(4, 1, 7);
      
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('allrecyclechart')).
            draw(data, {title:"Recycling"});
      }
      

      google.setOnLoadCallback(drawVisualization5);
	  
	        function drawVisualization6() {
        // Some raw data (not necessarily accurate)
        var countries =
          ['Cost', 'Savings',
           'Net'];
        var months = ['09/2011','10/2011', '11/2011', '12/2011', '01/2012', 'Current'];
        var productionByCountry = [[200, 365, 135, 357, 239, 236],
                                   [800, 938, 1120, 1167, 1110, 691],
                                   [400, 522, 599, 587, 615, 629]];
      
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Month');
        for (var i = 0; i < countries.length; ++i) {
          data.addColumn('number', countries[i]);
        }
        data.addRows(months.length);
        for (var i = 0; i < months.length; ++i) {
          data.setCell(i, 0, months[i]);
        }
        for (var i = 0; i < countries.length; ++i) {
          var country = productionByCountry[i];
          for (var month = 0; month < months.length; ++month) {
            data.setCell(month, i + 1, country[month]);
          }
        }
        // Create and draw the visualization.
        var ac = new google.visualization.AreaChart(document.getElementById('netvisualization'));
        ac.draw(data, {
          title : '',
          isStacked: true,
          width: 600,
          height: 300,
          vAxis: {title: "Tons"},
          hAxis: {title: "Month"}
        });
      }
      

      google.setOnLoadCallback(drawVisualization6);

      function drawVisualization7() {
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task');
        data.addColumn('number', 'Hours per Day');
        data.addRows(5);
        data.setValue(0, 0, 'Recycling Rebate');
        data.setValue(0, 1, 11);
        data.setValue(1, 0, 'Service Negotiation');
        data.setValue(1, 1, 2);
        data.setValue(2, 0, 'Service Change');
        data.setValue(2, 1, 2);
        data.setValue(3, 0, 'Equipment');
        data.setValue(3, 1, 2);
        data.setValue(4, 0, 'Other');
        data.setValue(4, 1, 7);
      
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('savingschart')).
            draw(data, {title:""});
      }
      

      google.setOnLoadCallback(drawVisualization7);
	  
	  
      function drawVisualization8() {
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task');
        data.addColumn('number', 'Hours per Day');
        data.addRows(5);
        data.setValue(0, 0, 'Pickup');
        data.setValue(0, 1, 11);
        data.setValue(1, 0, 'Haul');
        data.setValue(1, 1, 2);
        data.setValue(2, 0, 'Disposal');
        data.setValue(2, 1, 2);
        data.setValue(3, 0, 'Equipment');
        data.setValue(3, 1, 2);
        data.setValue(4, 0, 'Other');
        data.setValue(4, 1, 7);
      
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('invoicechart')).
            draw(data, {title:""});
      }
      

      google.setOnLoadCallback(drawVisualization8);
    </script>
   
      <div class="content">

    <div class="row">
          <div class="two columns"><a href="javascript:javascript:history.go(-1)" class="button"><- Go back</a></div>
        </div>
    <div class="row">
          <div class="four columns">
        <h1>Location #1234</h1>
      </div>
          <div class="two columns">Square Footage
        <h5> 2000 sqft</h5>
      </div>
          <div class="two columns">Diversion Rate
        <h5> 75%</h5>
      </div>
          <div class="eight columns omega">
        <table width="100%" border="0" cellspacing="10" cellpadding="20">
              <tr>
            <td>Contact</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
              <tr style="border-bottom:1px solid #ddd">
            <td><h5>James Rogers</h5></td>
            <td><h5>Manager</h5></td>
            <td><h5>123-456-7890</h5></td>
            <td><h5>jexample@walgreens.com</h5></td>
          </tr>
              <tr>
            <td><h5>Tommy Rich</h5></td>
            <td><h5>Ast Manager</h5></td>
            <td><h5>123-456-7890</h5></td>
            <td><h5>texample@walgreens.com</h5></td>
          </tr>
            </table>
      </div>
        </div>
    <div class="row">
          <div class="sixteen columns">
        <div id="tabs" style="border:0px;">
              <ul>
            <li><a href="#tabs-1">Waste</a></li>
            <li><a href="#tabs-2">Recycling</a></li>
            <li><a href="#tabs-3">Cost/Savings</a></li>
            <li><a href="#tabs-4">Invoices</a></li>
            <li><a href="#tabs-5">Support Requests</a></li>
            <li><a href="#tabs-6">Site Info</a></li>
          </ul>
              <div id="tabs-1" style="border:2px solid #7ABF53;"><span style="float:left;padding-right:10px">
                <label for="from">Start Date</label>
                <input name="from" type="text" id="from" value="09/01/2011" style="width:100px"/>
                </span><span style="float:left;padding-right:10px">
                <label for="to">End Date</label>
                <input name="to" type="text" id="to" value="03/01/2012" style="width:100px"/>
                </span><span style="float:right"><a href="<?=base_url()?>Company/reportsAllDCWaste" class="button" target="_blank">Printer Friendly</a> <a href="<?=base_url()?>Company/reportsAllDCWasteCSV" class="button">Export CSV</a></span><br style="clear:both;">
            <div id="wastetypeschart" style="width: 350px; height: 250px; float:left"></div>
            <div id="visualization2" style="width: 400px; height: 300px; float:left"></div>
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="wastelist" width="100%">
              <thead>
                <tr>
                  <th>Region</th>
                  <th>District</th>
                  <th>Location</th>
                  <th>24H</th>
                  <th>SqFt</th>
                  <th>Waste (Tons)</th>
                  <th>Hazardous (Tons)</th>
                  <th>Other (Tons)</th>
                  <th>Cost</th>
                </tr>
              </thead>
              <tbody>
                <tr class="odd gradeA">
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>100</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">100</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>100</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">100</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>100</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">100</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>100</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">100</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>100</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">100</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>100</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">100</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr  class="odd gradeA">
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>100</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">100</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr  class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>100</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">100</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr  class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>100</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">100</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr  class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>100</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">100</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr  class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>100</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">100</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>100</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">100</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr class="odd gradeA">
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>100</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">100</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr  class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr  class="odd gradeA">
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeA">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr  class="even gradeA">
                  <td class="odd gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr  class="odd gradeA">
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr  class="even gradeA">
                  <td class="odd gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr  class="odd gradeA">
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeA">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr  class="even gradeA">
                  <td class="odd gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                </tr>
                <tr  class="odd gradeA">
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>66</td>
                  <td>6</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <th>1 Regions</th>
                  <th>1000 Districts</th>
                  <th>1904 Locations</th>
                  <th>&nbsp;</th>
                  <th>100,000 SqFt</th>
                  <th>1000 Waste (Tons)</th>
                  <th>2000 Hazardous (Tons)</th>
                  <th>3000 Other (Tons)</th>
                  <th>$50 Cost</th>
                </tr>
              </tfoot>
            </table>
          </div>
              <div id="tabs-2" style="border:2px solid #7ABF53;"><span style="float:left;padding-right:10px">
                <label for="from">Start Date</label>
                <input name="from" type="text" id="from" value="09/01/2011" style="width:100px"/>
                </span><span style="float:left;padding-right:10px">
                <label for="to">End Date</label>
                <input name="to" type="text" id="to" value="03/01/2012" style="width:100px"/>
                </span><span style="float:right"><a href="<?=base_url()?>Company/reportsAllDCWaste" class="button" target="_blank">Printer Friendly</a> <a href="<?=base_url()?>Company/reportsAllDCWasteCSV" class="button">Export CSV</a></span><br style="clear:both;">
            <div id="allrecyclechart" style="width: 350px; height: 250px; float:left"></div>
            <div id="visualization" style="width: 600px; height: 300px;float:left;"></div>
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="recyclelist" width="100%">
              <thead>
                <tr>
                  <th>Region</th>
                  <th>District</th>
                  <th>Location</th>
                  <th>24H</th>
                  <th>SqFt</th>
                  <th>Cardboard</th>
                  <th>Aluminum</th>
                  <th>Film</th>
                  <th>Plastic</th>
                  <th>Trees</th>
                  <th>Landfill</th>
                  <th>Energy (KWh)</th>
                  <th>Co2 (Tons)</th>
                  <th>Rebate</th>
                </tr>
              </thead>
              <tbody>
                <tr class="odd gradeA">
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="odd gradeA">
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td class="odd gradeA">$1000</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>121</td>
                  <td class="even gradeA">$1000</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td class="odd gradeA">$1000</td>
                </tr>
                <tr class="odd gradeA">
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td class="even gradeA">$1000</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td class="odd gradeA">$1000</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td class="even gradeA">$1000</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td class="odd gradeA">$1000</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td class="even gradeA">$1000</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td class="odd gradeA">$1000</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td class="even gradeA">$1000</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td class="odd gradeA">$1000</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td class="even gradeA">$1000</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="odd gradeA">
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeA">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="even gradeA">
                  <td class="odd gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="odd gradeA">
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>121</td>
                  <td>$1000</td>
                </tr>
                <tr  class="even gradeA">
                  <td class="odd gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="odd gradeA">
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeA">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="even gradeA">
                  <td class="odd gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="odd gradeA">
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td class="odd gradeA">6</td>
                  <td>12</td>
                  <td>$1000</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <th>5 Regions</th>
                  <th>1000 Districts</th>
                  <th>1904 Locations</th>
                  <th>&nbsp;</th>
                  <th><p>100,000 SqFt</p></th>
                  <th>120 Cardboard</th>
                  <th>50 Aluminum</th>
                  <th>100 Film</th>
                  <th>100 Plastic</th>
                  <th>30 Trees</th>
                  <th>209 Landfill</th>
                  <th>2875 Energy (KWh)</th>
                  <th>12890 Co2 (Tons)</th>
                  <th>$100,000 Rebate</th>
                </tr>
              </tfoot>
            </table>
<h3>&nbsp;</h3>
          </div>
              <div id="tabs-3" style="border:2px solid #7ABF53;"><span style="float:left;padding-right:10px">
                <label for="from">Start Date</label>
                <input name="from" type="text" id="from" value="09/01/2011" style="width:100px"/>
                </span><span style="float:left;padding-right:10px">
                <label for="to">End Date</label>
                <input name="to" type="text" id="to" value="03/01/2012" style="width:100px"/>
                </span><span style="float:right"><a href="<?=base_url()?>Company/reportsAllDCWaste" class="button" target="_blank">Printer Friendly</a> <a href="<?=base_url()?>Company/reportsAllDCWasteCSV" class="button">Export CSV</a></span><br style="clear:both;">
            <div id="invoicechart" style="width: 350px; height: 180px;float:left"></div>
            <div id="savingschart" style="width: 350px; height: 180px;float:left"></div>
            <div id="netvisualization" style="width: 700px; height: 300px;float:left"></div>
            <br>
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="costlist" width="100%">
              <thead>
                <tr>
                  <th>Region</th>
                  <th>District</th>
                  <th>Location#</th>
                  <th>24H</th>
                  <th>SqFt</th>
                  <th>Total Tonnage</th>
                  <th> Waste<br>
                  Service</th>
                  <th>Waste <br>
                  Equipment Fee</th>
                  <th>Waste<br>
                  Haul Fee</th>
                  <th>Waste <br>
                  Disposal Fee</th>
                  <th>Recycling Rebate</th>
                  <th>Other  Fee</th>
                  <th>Net</th>
                </tr>
              </thead>
              <tbody>
                <tr class="odd gradeA">
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$100.34</td>
                  <td>&nbsp;</td>
                  <td>$100.34</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>2</td>
                  <td>$111.06</td>
                  <td>&nbsp;</td>
                  <td>$111.06</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$100.21</td>
                  <td>&nbsp;</td>
                  <td>$100.21</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>11</td>
                  <td>$112.24</td>
                  <td>&nbsp;</td>
                  <td>$112.24</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$113.75</td>
                  <td>&nbsp;</td>
                  <td>$113.75</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$113.55</td>
                  <td>&nbsp;</td>
                  <td>$113.55</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$300.00</td>
                  <td>&nbsp;</td>
                  <td>$300.00</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$113.73</td>
                  <td>&nbsp;</td>
                  <td>$113.73</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$109.56</td>
                  <td>&nbsp;</td>
                  <td>$109.56</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$112.06</td>
                  <td>&nbsp;</td>
                  <td>$112.06</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$114.29</td>
                  <td>&nbsp;</td>
                  <td>$114.29</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td class="odd gradeA">$100.34</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$100.34</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$50.00</td>
                </tr>
                <tr  class="odd gradeA">
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td class="even gradeA">$111.06</td>
                  <td class="even gradeA">&nbsp;</td>
                  <td class="even gradeA">$111.06</td>
                  <td class="even gradeA">$50.00</td>
                  <td class="even gradeA">$50.00</td>
                  <td class="even gradeA">&nbsp;</td>
                  <td class="even gradeA">$50.00</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td class="odd gradeA">$100.21</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$100.21</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$50.00</td>
                </tr>
                <tr  class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td class="even gradeA">$112.24</td>
                  <td class="even gradeA">&nbsp;</td>
                  <td class="even gradeA">$112.24</td>
                  <td class="even gradeA">$50.00</td>
                  <td class="even gradeA">$50.00</td>
                  <td class="even gradeA">&nbsp;</td>
                  <td class="even gradeA">$50.00</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td class="odd gradeA">$113.75</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$113.75</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$50.00</td>
                </tr>
                <tr  class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td class="even gradeA">$113.55</td>
                  <td class="even gradeA">&nbsp;</td>
                  <td class="even gradeA">$113.55</td>
                  <td class="even gradeA">$50.00</td>
                  <td class="even gradeA">$50.00</td>
                  <td class="even gradeA">&nbsp;</td>
                  <td class="even gradeA">$50.00</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td class="odd gradeA">$300.00</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$300.00</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$50.00</td>
                </tr>
                <tr  class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td class="even gradeA">$113.73</td>
                  <td class="even gradeA">&nbsp;</td>
                  <td class="even gradeA">$113.73</td>
                  <td class="even gradeA">$50.00</td>
                  <td class="even gradeA">$50.00</td>
                  <td class="even gradeA">&nbsp;</td>
                  <td class="even gradeA">$50.00</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td class="odd gradeA">$109.56</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$109.56</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$50.00</td>
                </tr>
                <tr  class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td class="even gradeA">$112.06</td>
                  <td class="even gradeA">&nbsp;</td>
                  <td class="even gradeA">$112.06</td>
                  <td class="even gradeA">$50.00</td>
                  <td class="even gradeA">$50.00</td>
                  <td class="even gradeA">&nbsp;</td>
                  <td class="even gradeA">$50.00</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td class="odd gradeA">$114.29</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$114.29</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$50.00</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$100.34</td>
                  <td>&nbsp;</td>
                  <td>$100.34</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$111.06</td>
                  <td>&nbsp;</td>
                  <td>$111.06</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="odd gradeA">
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$100.21</td>
                  <td>&nbsp;</td>
                  <td>$100.21</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>Y</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$112.24</td>
                  <td>&nbsp;</td>
                  <td>$112.24</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$100.34</td>
                  <td>&nbsp;</td>
                  <td>$100.34</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$111.06</td>
                  <td>&nbsp;</td>
                  <td>$111.06</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$100.21</td>
                  <td>&nbsp;</td>
                  <td>$100.21</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$112.24</td>
                  <td>&nbsp;</td>
                  <td>$112.24</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$113.75</td>
                  <td>&nbsp;</td>
                  <td>$113.75</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$113.55</td>
                  <td>&nbsp;</td>
                  <td>$113.55</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$300.00</td>
                  <td>&nbsp;</td>
                  <td>$300.00</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$113.73</td>
                  <td>&nbsp;</td>
                  <td>$113.73</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr  class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$109.56</td>
                  <td>&nbsp;</td>
                  <td>$109.56</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$112.06</td>
                  <td>&nbsp;</td>
                  <td>$112.06</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr  class="odd gradeA">
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeA">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td>$114.29</td>
                  <td>&nbsp;</td>
                  <td>$114.29</td>
                  <td>$50.00</td>
                  <td>$50.00</td>
                  <td>&nbsp;</td>
                  <td>$50.00</td>
                </tr>
                <tr  class="even gradeA">
                  <td class="odd gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td class="odd gradeA">$113.75</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$113.75</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$50.00</td>
                </tr>
                <tr  class="odd gradeA">
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td class="even gradeA">$113.55</td>
                  <td class="even gradeA">&nbsp;</td>
                  <td class="even gradeA">$113.55</td>
                  <td class="even gradeA">$50.00</td>
                  <td class="even gradeA">$50.00</td>
                  <td class="even gradeA">&nbsp;</td>
                  <td class="even gradeA">$50.00</td>
                </tr>
                <tr  class="even gradeA">
                  <td class="odd gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td class="odd gradeA">$300.00</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$300.00</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$50.00</td>
                </tr>
                <tr  class="odd gradeA">
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeA">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td class="even gradeA">$113.73</td>
                  <td class="even gradeA">&nbsp;</td>
                  <td class="even gradeA">$113.73</td>
                  <td class="even gradeA">$50.00</td>
                  <td class="even gradeA">$50.00</td>
                  <td class="even gradeA">&nbsp;</td>
                  <td class="even gradeA">$50.00</td>
                </tr>
                <tr  class="even gradeA">
                  <td class="odd gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td class="odd gradeA">$109.56</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$109.56</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$50.00</td>
                </tr>
                <tr  class="odd gradeA">
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td class="even gradeA">$112.06</td>
                  <td class="even gradeA">&nbsp;</td>
                  <td class="even gradeA">$112.06</td>
                  <td class="even gradeA">$50.00</td>
                  <td class="even gradeA">$50.00</td>
                  <td class="even gradeA">&nbsp;</td>
                  <td class="even gradeA">$50.00</td>
                </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>N</td>
                  <td>10,000</td>
                  <td>1</td>
                  <td class="odd gradeA">$114.29</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$114.29</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">$50.00</td>
                  <td class="odd gradeA">&nbsp;</td>
                  <td class="odd gradeA">$50.00</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <th>5 Regions</th>
                  <th>1000 Districts</th>
                  <th>1904 Locations</th>
                  <th>&nbsp;</th>
                  <th>30,000 SqFt</th>
                  <th>1234 Total Tonnage</th>
                  <th>$123,490 Waste</th>
                  <th>Waste <br>
Equipment Fee</th>
                  <th>$123,456 Haul Fee</th>
                  <th>$234,567 Disposal Fee</th>
                  <th>$20,000 Rebate</th>
                  <th>Other Fee</th>
                  <th>$6709 Total Fee</th>
                </tr>
              </tfoot>
            </table>
          </div>
              <div id="tabs-4"  style="border:2px solid #7ABF53;"><span style="float:right"><a href="<?=base_url()?>Company/reportsAllDCWaste" class="button" target="_blank">Printer Friendly</a> <a href="<?=base_url()?>Company/reportsAllDCWasteCSV" class="button">Export CSV</a></span><!--To style tr, use class="odd even gradeA gradeA gradeC gradeU gradeX"-->
                <table cellpadding="0" cellspacing="0" border="0" class="display" id="invoiceslist" width="100%">
                  <thead>
                    <tr>
                      <th>Region</th>
                      <th>District</th>
                      <th>Location</th>
                      <th>Invoice#</th>
                      <th> Date</th>
                      <th>Sent Date <br>
                        (only for admin)</th>
                      <th>Vendor</th>
                      <th>Trash Rate</th>
                      <th>Extra</th>
                      <th>Tax</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="odd gradeA">
                      <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                      <td>1234</td>
                      <td><a href="invoiceexample.html">1001</a></td>
                      <td>01/01/12</td>
                      <td>01/01/12</td>
                      <td>ABC Hauler</td>
                      <td>$100.34</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                    </tr>
                    <tr class="even gradeA">
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                      <td>1234</td>
                      <td><a href="invoiceexample.html">1180</a></td>
                      <td>01/01/12</td>
                      <td>01/01/12</td>
                      <td>ABC Hauler</td>
                      <td>$111.06</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                    </tr>
                    <tr class="odd gradeA">
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                      <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                      <td>1234</td>
                      <td><a href="invoiceexample.html">1214</a></td>
                      <td>01/01/12</td>
                      <td>01/01/12</td>
                      <td>ABC Hauler</td>
                      <td>$100.21</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                    </tr>
                    <tr class="even gradeA">
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                      <td class="odd gradeA">1234</td>
                      <td><a href="invoiceexample.html">1427</a></td>
                      <td>01/01/12</td>
                      <td>01/01/12</td>
                      <td>ABC Hauler</td>
                      <td>$112.24</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                    </tr>
                    <tr class="odd gradeA">
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                      <td>1234</td>
                      <td><a href="invoiceexample.html">1487</a></td>
                      <td>01/01/12</td>
                      <td>01/01/12</td>
                      <td>ABC Hauler</td>
                      <td>$113.75</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                    </tr>
                    <tr class="even gradeA">
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                      <td>1234</td>
                      <td><a href="invoiceexample.html">1572</a></td>
                      <td>01/01/12</td>
                      <td>01/01/12</td>
                      <td>ABC Hauler</td>
                      <td>$113.55</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                    </tr>
                    <tr class="odd gradeA">
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                      <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                      <td>1234</td>
                      <td><a href="invoiceexample.html">1598</a></td>
                      <td>01/01/12</td>
                      <td>01/01/12</td>
                      <td>Container Rental</td>
                      <td>$300.00</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                    </tr>
                    <tr class="even gradeA">
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                      <td class="even gradeX">1234</td>
                      <td><a href="invoiceexample.html">1616</a></td>
                      <td>01/01/12</td>
                      <td class="odd gradeA">01/01/12</td>
                      <td>ABC Hauler</td>
                      <td>$113.73</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                    </tr>
                    <tr class="odd gradeA">
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                      <td>1234</td>
                      <td><a href="invoiceexample.html">1874</a></td>
                      <td>01/01/12</td>
                      <td class="even gradeA">01/01/12</td>
                      <td>ABC Hauler</td>
                      <td>$109.56</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                    </tr>
                    <tr class="even gradeA">
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                      <td>1234</td>
                      <td ><a href="invoiceexample.html">1918</a></td>
                      <td >01/01/12</td>
                      <td class="odd gradeA">01/01/12</td>
                      <td>ABC Hauler</td>
                      <td>$112.06</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                    </tr>
                    <tr class="odd gradeA">
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                      <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                      <td>1234</td>
                      <td ><a href="invoiceexample.html">2055</a></td>
                      <td >01/01/12</td>
                      <td class="even gradeA">01/01/12</td>
                      <td>ABC Hauler</td>
                      <td>$114.29</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                    </tr>
                    <tr  class="even gradeA">
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                      <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                      <td class="even gradeX">1234</td>
                      <td ><a href="invoiceexample.html">2077</a></td>
                      <td >01/01/12</td>
                      <td class="odd gradeA">01/01/12</td>
                      <td>ABC Hauler</td>
                      <td>$124.01</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                      <td>$50.00</td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th>Region</th>
                      <th>District</th>
                      <th>Location</th>
                      <th>Invoice#</th>
                      <th> Date</th>
                      <th>Sent Date</th>
                      <th>2 Vendors</th>
                      <th>$123,489 Trash Rate</th>
                      <th>$234,509 Extra</th>
                      <th>$345,000 Tax</th>
                      <th>$100,00 Total</th>
                    </tr>
                  </tfoot>
                </table>
                <br>
          </div>
              <div id="tabs-5" style="border:2px solid #7ABF53;"><span style="float:right"><a href="<?=base_url()?>Company/reportsAllDCWaste" class="button" target="_blank">Printer Friendly</a> <a href="<?=base_url()?>Company/reportsAllDCWasteCSV" class="button">Export CSV</a></span>
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="callslist" width="100%">
                  <thead>
                <tr>
                  <th>Region</th>
                  <th>District</th>
                  <th>Location</th>
                      <th>Service#</th>
                      <th>Date</th>
                      <th>Contact</th>
                      <th>Phone#</th>
                      <th>Comments</th>
                      <th>Resolved</th>
                    </tr>
              </thead>
                  <tbody>
                <tr class="odd gradeA">
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>1001</td>
                      <td>01/01/12</td>
                      <td>James</td>
                      <td>324-586-1234</td>
                      <td>ABC Hauler additional pickup requested</td>
                      <td>Y</td>
                    </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>1180</td>
                      <td>01/01/12</td>
                      <td>John</td>
                      <td>324-586-1234</td>
                      <td>ABC Hauler additional pickup requested</td>
                      <td>Y</td>
                    </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>1214</td>
                      <td>01/01/12</td>
                      <td>John</td>
                      <td>324-586-1234</td>
                      <td>ABC Hauler additional pickup requested</td>
                      <td>Y</td>
                    </tr>
                <tr class="even gradeX">
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>1427</td>
                      <td>01/01/12</td>
                      <td>John</td>
                      <td>324-586-1234</td>
                      <td>ABC Hauler additional pickup requested</td>
                      <td>N</td>
                    </tr>
                <tr class="odd gradeX">
                  <td class="odd gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>1487</td>
                      <td>01/01/12</td>
                      <td>John</td>
                      <td>324-586-1234</td>
                      <td>ABC Hauler additional pickup requested</td>
                      <td>N</td>
                    </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>1572</td>
                      <td>01/01/12</td>
                      <td>John</td>
                      <td>324-586-1234</td>
                      <td>ABC Hauler additional pickup requested</td>
                      <td>Y</td>
                    </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>1598</td>
                      <td>01/01/12</td>
                      <td>John</td>
                      <td>324-586-1234</td>
                      <td>Container Rental did not pickup on Monday/Wednesday</td>
                      <td>Y</td>
                    </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td>1616</td>
                      <td>01/01/12</td>
                      <td>John</td>
                      <td>324-586-1234</td>
                      <td>ABC Hauler additional pickup requested</td>
                      <td>Y</td>
                    </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>1874</td>
                      <td>01/01/12</td>
                      <td>John</td>
                      <td>324-586-1234</td>
                      <td>ABC Hauler additional pickup requested</td>
                      <td>Y</td>
                    </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td >1918</td>
                      <td >01/01/12</td>
                      <td>John</td>
                      <td>324-586-1234</td>
                      <td>ABC Hauler additional pickup requested</td>
                      <td>Y</td>
                    </tr>
                <tr class="odd gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td >2055</td>
                      <td >01/01/12</td>
                      <td>John</td>
                      <td>324-586-1234</td>
                      <td>ABC Hauler additional pickup requested</td>
                      <td>Y</td>
                    </tr>
                <tr  class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeX">1234</td>
                  <td >2077</td>
                      <td >01/01/12</td>
                      <td>John</td>
                      <td>324-586-1234</td>
                      <td>ABC Hauler additional pickup requested</td>
                      <td>Y</td>
                    </tr>
              </tbody>
                  <tfoot>
                <tr>
                  <th>Region</th>
                  <th>District</th>
                  <th>Location</th>
                      <th>Service#</th>
                      <th> Invoice Date</th>
                      <th>Sent Date</th>
                      <th>Vendor #</th>
                      <th>Vendor</th>
                      <th>Extra</th>
                    </tr>
              </tfoot>
                </table>
            <br>
          </div>
              <div id="tabs-6" style="border:2px solid #7ABF53;"><span style="float:right"><a href="<?=base_url()?>Company/reportsAllDCWaste" class="button" target="_blank">Printer Friendly</a> <a href="<?=base_url()?>Company/reportsAllDCWasteCSV" class="button">Export CSV</a></span> Addresses
                <h5>123 Main St, 
                  Manhattan, NY 00120</h5>
            <h5>123 Main St, 
                  Manhattan, NY 00120</h5>
            <br>
            Services<br>
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="servicelist" width="100%">
              <thead>
                <tr>
                  <th>Region</th>
                  <th>District</th>
                  <th>Location</th>
                  <th>SqFt</th>
                  <th>24H</th>
                  <th>Container Type</th>
                  <th>Container Size</th>
                  <th>Duration</th>
                  <th>Frequency</th>
                  <th>Cost</th>
                  <th>Last Updated</th>
                </tr>
              </thead>
              <tbody>
                <tr class="odd gradeA">
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>10,000</td>
                  <td>Y</td>
                  <td>Open Top</td>
                  <td>40 yd</td>
                  <td>Temp</td>
                  <td>1x/Week</td>
                  <td>$12</td>
                  <td>01/01/2012</td>
                </tr>
                <tr class="even gradeA">
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td>10,000</td>
                  <td>Y</td>
                  <td>Compactor</td>
                  <td>30 yd</td>
                  <td>Perm</td>
                  <td>1x/week</td>
                  <td>$12</td>
                  <td>01/02/2012</td>
                </tr>
                <tr class="odd gradeB">
                  <td class="odd gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeX"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td>10,000</td>
                  <td>Y</td>
                  <td>Roll Off</td>
                  <td>8 yd</td>
                  <td>Temp</td>
                  <td>2x/week</td>
                  <td>$12</td>
                  <td>01/03/2012</td>
                </tr>
                <tr class="even gradeB">
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td class="even gradeA">10,000</td>
                  <td class="odd gradeA">Y</td>
                  <td class="odd gradeA">Open Top</td>
                  <td>40 yd</td>
                  <td>Perm</td>
                  <td>1x/week</td>
                  <td>$12</td>
                  <td>01/04/2012</td>
                </tr>
                <tr class="odd gradeU">
                  <td class="odd gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="odd gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td class="odd gradeA">10,000</td>
                  <td class="even gradeA">N</td>
                  <td class="even gradeA">Compactor</td>
                  <td>8 yd </td>
                  <td>Temp</td>
                  <td>On request</td>
                  <td>$12</td>
                  <td>01/01/2012</td>
                </tr>
                <tr class="even gradeU">
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="even gradeA">1234</td>
                  <td class="even gradeA">10,000</td>
                  <td class="odd gradeB">N</td>
                  <td class="odd gradeB">Roll Off</td>
                  <td>15 yd</td>
                  <td>Perm</td>
                  <td>3x/week</td>
                  <td>$12</td>
                  <td>01/01/2012</td>
                </tr>
                <tr class="odd gradeX">
                  <td class="odd gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td class="odd gradeA">1234</td>
                  <td class="odd gradeB">10,000</td>
                  <td>N</td>
                  <td>Roll Off</td>
                  <td>40 yd</td>
                  <td>Temp</td>
                  <td>On request</td>
                  <td>$12</td>
                  <td>01/01/2012</td>
                </tr>
                <tr class="even gradeX">
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/Region">East</a></td>
                  <td class="even gradeA"><a href="<?=base_url()?>Dcexample/Stores/Waste/District">610009</a></td>
                  <td>1234</td>
                  <td class="even gradeB">10,000</td>
                  <td>N</td>
                  <td>Dumpster</td>
                  <td>2 yd</td>
                  <td>Perm</td>
                  <td>2x/month</td>
                  <td>$14</td>
                  <td>01/01/2012</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <th>5 Regions</th>
                  <th>100 Districts</th>
                  <th>100,000 Stores</th>
                  <th>SqFt</th>
                  <th>24H</th>
                  <th>Container Type</th>
                  <th>Container Size</th>
                  <th>Duration</th>
                  <th>Frequency</th>
                  <th>Cost</th>
                  <th>Last Updated
                    </td>
                </tr>
              </tfoot>
            </table>
          </div>
            </div>
      </div>
        </div>
    <div> </div>
  </div>
      </div>
      </div>
      <footer> &nbsp;&copy; 2012 All rights reserved. The Astor Company. <a href="privacy.html">Privacy</a> & <a href="terms.html">Terms</a></footer>
    </article>
<!-- container --> 

<!-- End Document
================================================== -->
</body>
</html>